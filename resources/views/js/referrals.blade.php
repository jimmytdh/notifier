<script>
    (function () {
        let current_list = [];
        let is_init = false;
        let is_searching = false;
        let referral_id;
        let page = 0;
        function getPatientModal() {
            const el = document.getElementById('patient_modal');
            if (!el || !window.bootstrap || !window.bootstrap.Modal) return null;
            return window.bootstrap.Modal.getOrCreateInstance(el);
        }

        function getForwardModal() {
            const el = document.getElementById('forward_modal');
            if (!el || !window.bootstrap || !window.bootstrap.Modal) return null;
            return window.bootstrap.Modal.getOrCreateInstance(el);
        }

        function getTypeBadge(type) {
            const normalized = (type || "Referral").toString().trim().toLowerCase();
            if (normalized === "manual") {
                return `<span class="badge rounded-pill bg-warning text-dark">Manual</span>`;
            }
            return `<span class="badge rounded-pill bg-primary">Referral</span>`;
        }

        function getReferrals() {
            const patient = (String($("#search_patient").val() || "")).trim().toUpperCase();
            $("#referral_list").empty();
            $("#referral_list").append("<tr><td colspan='9'>Loading data, please wait ...</td></tr>");
            fetch('{{ route('getReferrals') }}?search_patient=' + patient +
                "&page=" + page, {
                method: "GET"
            })
                .then(res => res.json())
                .then(data => {
                    $("#pagination_container").addClass("d-none")
                    $("#referral_list").empty();
                    $("#btn_next").addClass("d-none");
                    if (data.results.length < 1) {
                        $("#referral_list").append("<tr><td colspan='9'>No record found</td></tr>");
                        return;
                    }

                    $("#pagination_container").removeClass("d-none")

                    let max_visible = data.results.length;
                    if (max_visible == 11) {
                        max_visible = 10;
                        $("#btn_next").removeClass("d-none");
                    }

                    $("#page_items_count").text(((page * 10) + 1) + " - " + ((page * 10) + max_visible));
                    for (let i = 0; i < max_visible; i++) {
                        const it = data.results[i];
                        const tr = `<tr id="referral_id_` + it.id + `">
                                        <td>` + getTypeBadge(it.type) + `</td>
                                        <td>` + it.referring_hospital + `</td>
                                        <td>` + it.patient + `</td>
                                        <td>` + it.age + `</td>
                                        <td>` + it.gender + `</td>
                                        <td class="d-none" style="max-width: 100px;text-overflow:ellipsis;white-space: nowrap;overflow: hidden">` + it.chief_complaint + `</td>
                                        <td class="d-none">` + it.diagnosis + `</td>
                                        <td>` + it.department + `</td>
                                        <td>` + moment(it.date_created).format("MM/DD/YYYY hh:mm:ss A") + `</td>
                                        <td>
                                            <button class='btn btn-sm' style="background: transparent" onClick='viewPatient("` + it.id + `")'>
                                                <i class="bi bi-eye-fill text-success"></i>
                                            </button>
                                        </td>
                                    </tr>`;
                        $("#referral_list").append(tr);
                    }
                    data.results.forEach(it => {

                    })
                }).catch(err => {
                console.log(err);
            });
        }

        function getLogs(id) {
            $("#activity_logs").empty();
            fetch('{{ route('loadLogs') }}?id=' + id, {
                method: "GET"
            })
                .then(res => res.json())
                .then(data => {
                    data.results.forEach(it => {
                        const tr = `<tr>
                                        <td>` + it.origin + `</td>
                                        <td>` + it.remarks + `</td>
                                        <td>` + it.destination + `</td>
                                        <td>` + moment(it.date_forwarded).format("MM/DD/YYYY hh:mm:ss A") + `</td>
                                   </tr>`;

                        $("#activity_logs").append(tr);
                    });
                }).catch(err => {
                toastr.error(err, "Ooops");
            });
        }

        window.viewPatient = function (id) {
            referral_id = id;
            const patient = $("#referral_id_" + id + " td:eq(2)").text();
            const age = $("#referral_id_" + id + " td:eq(3)").text();
            const sex = $("#referral_id_" + id + " td:eq(4)").text();
            const chief_complaint = $("#referral_id_" + id + " td:eq(6)").text();
            const diagnosis = $("#referral_id_" + id + " td:eq(7)").text();

            $("#patient").text(patient);
            $("#age").text(age + " years old");
            $("#sex").text(sex);
            $("#chief_complaint").text(chief_complaint);
            $("#diagnosis").text(diagnosis);

            const modal = getPatientModal();
            if (modal) modal.toggle();
            getLogs(id);
        }

        function bindEvents() {
            const userCode = $("#user_code").text().trim();
            observe(userCode, function (snapshot) {
                if (!is_init) return;

                toastr.success(snapshot.val(), "New incoming referral");
                responsiveVoice.speak('New incoming referral');

                getReferrals();
            });

            get(userCode, function () {
                is_init = true;
            });

            $("#patient_modal .card-body").prop("disabled", true);

            $("#btn_search").off("click").on("click", function () {
                page = 0;
                getReferrals();
            });

            $("#search_patient").off("keydown").on("keydown", function (e) {
                if (e.key === "Enter") {
                    e.preventDefault();
                    $("#btn_search").click();
                }
            });

            $("#btn_next").off("click").on("click", function () {
                page++;
                getReferrals();
                $("#btn_prev").removeClass("d-none");
            });

            $("#btn_prev").off("click").on("click", function () {
                if (page == 0) return;
                if (page == 1) {
                    $("#btn_prev").addClass("d-none");
                }
                page--;
                getReferrals();
            });

            $("#btn_done").off("click").on("click", function () {
                let remarks = prompt("Remarks (optional):", "TAGGED AS DONE");
                if (remarks === null) return;
                remarks = remarks.trim();
                if (remarks === "") remarks = "TAGGED AS DONE";

                if (confirm("Are you sure you want to tag this as done?")) {
                    $(this).prop("disabled", true);
                    $(this).html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...`);
                    fetch('{{ route('tagAsDone') }}', {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        body: JSON.stringify({id: referral_id, remarks: remarks})
                    })
                        .then(res => res.json())
                        .then(data => {
                            $("#btn_done").prop("disabled", false);
                            $("#btn_done").html("TAG AS DONE");
                            if (data.code == 500) {
                                toastr.error(data.message, "Ooops");
                                return;
                            }

                    const modal = getPatientModal();
                    if (modal) modal.toggle();
                    toastr.success(data.message, "Information");
                    getReferrals();
                }).catch(err => {
                        console.log(err);
                    })
                }
            });

            $("#btn_queue").off("click").on("click", function () {
                if (!referral_id) {
                    toastr.error("No selected patient", "Error");
                    return;
                }

                const referring_hospital = $("#referral_id_" + referral_id + " td:eq(1)").text().trim();
                const patient = $("#referral_id_" + referral_id + " td:eq(2)").text().trim();
                const age = $("#referral_id_" + referral_id + " td:eq(3)").text().trim();
                const gender = $("#referral_id_" + referral_id + " td:eq(4)").text().trim();
                const department = $("#referral_id_" + referral_id + " td:eq(8)").text().trim();
                const remarks = "QUEUED FROM " + $("#user_department").text().trim();

                const params = {
                    referring_hospital: referring_hospital,
                    patient: patient,
                    age: age,
                    gender: gender,
                    department: department,
                    remarks: remarks
                };

                $(this).prop("disabled", true);
                $(this).html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...`);
                fetch('{{ route('insertQueue') }}', {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    body: JSON.stringify(params)
                })
                    .then(res => res.json())
                    .then(data => {
                        $("#btn_queue").prop("disabled", false);
                        $("#btn_queue").html("QUEUE PATIENT");
                        if (data.code == 500) {
                            toastr.error(data.message || "Unable to queue patient", "Error");
                            return;
                        }

                        toastr.success(data.message, "Information");
                    }).catch(err => {
                    $("#btn_queue").prop("disabled", false);
                    $("#btn_queue").html("QUEUE PATIENT");
                    toastr.error(err.message || "Unable to queue patient", "Error");
                });
            });

            $("#btn_send").off("click").on("click", function () {
                $("#forward_department").val("");
                $("#forward_remarks").val("");
                const modal = getForwardModal();
                if (modal) modal.show();
            });

            $("#btn_confirm_forward").off("click").on("click", function () {
                $(".is-invalid").removeClass("is-invalid");
                const referring_hospital = $("#referral_id_" + referral_id + " td:eq(0)").text();
                const patient = $("#referral_id_" + referral_id + " td:eq(1)").text();
                const age = $("#referral_id_" + referral_id + " td:eq(2)").text();
                const gender = $("#referral_id_" + referral_id + " td:eq(3)").text();
                const remarks = $("#forward_remarks").val().trim();
                const topic = $("#forward_department").val();
                const department = $("#forward_department option:selected").text();

                if (topic == "") {
                    $("#forward_department").addClass("is-invalid");
                    return;
                }

                if (remarks == "") {
                    $("#forward_remarks").addClass("is-invalid");
                    return;
                }

                const params = {
                    id: referral_id,
                    patient: patient,
                    gender: gender,
                    age: age,
                    diagnosis: "",
                    referring_hospital: referring_hospital,
                    department: department,
                    topic: topic,
                    remarks: remarks
                }

                $(this).prop("disabled", true);
                $(this).html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...`);
                fetch('{{ route('forwardReferral') }}', {
                    method: 'POST',
                    headers: {
                        "Content-Type": "application/json",
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    body: JSON.stringify(params)
                })
                    .then(res => res.json())
                    .then(data => {
                        $("#btn_confirm_forward").prop("disabled", false);
                        $("#btn_confirm_forward").html("Confirm Forward");
                        if (data.code == 500) {
                            toastr.error(data.message, "Ooops");
                            getReferrals();
                            return;
                        }

                        const forwardModal = getForwardModal();
                        if (forwardModal) forwardModal.hide();
                        const modal = getPatientModal();
                        if (modal) modal.toggle();
                        toastr.success(data.message, "Information");
                        insert(params.topic, params.patient, function (message) {
                            deleteChild(params.topic);
                        });
                        getReferrals();
                    }).catch(err => {
                    $("#btn_confirm_forward").prop("disabled", false);
                    $("#btn_confirm_forward").html("Confirm Forward");
                    toastr.error(err, "Ooops");
                });
            });
        }

        $(document).ready(function () {
            bindEvents();
            getReferrals();
        });
    })();
</script>
