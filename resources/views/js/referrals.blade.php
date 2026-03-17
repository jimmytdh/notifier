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
            $("#referral_list").append("<tr><td colspan='10'>Loading data, please wait ...</td></tr>");
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
                        $("#referral_list").append("<tr><td colspan='10'>No record found</td></tr>");
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
                        const ward = it.ward || "";
                        const bedNo = it.bed_no || "";
                        const wardDisplay = ward || "-";
                        const bedDisplay = bedNo || "-";
                        const tr = `<tr id="referral_id_` + it.id + `">
                                        <td>` + getTypeBadge(it.type) + `</td>
                                        <td>` + it.referring_hospital + `</td>
                                        <td>` + it.patient + `</td>
                                        <td>` + it.age + `</td>
                                        <td>` + it.gender + `</td>
                                        <td>` + wardDisplay + `</td>
                                        <td>` + bedDisplay + `</td>
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

        window.refreshReferrals = getReferrals;

        function getLogs(id) {
            $("#activity_logs").empty();
            fetch('{{ route('loadLogs') }}?id=' + id, {
                method: "GET"
            })
                .then(res => res.json())
                .then(data => {
                    if (data.referral) {
                        const ward = (data.referral.ward || "").toString().trim();
                        const bedNo = (data.referral.bed_no || "").toString().trim();
                        $("#patient_modal #ward_input").val(ward);
                        $("#patient_modal #bed_no_input").val(bedNo);
                    }
                    const makeBadge = function (value, tone) {
                        if (!value) return `<span class="text-muted">-</span>`;
                        const cls = tone || "bg-secondary";
                        return `<span class="badge rounded-pill ${cls}">${value}</span>`;
                    };
                    data.results.forEach(it => {
                        const tr = `<tr>
                                        <td>` + it.origin + `</td>
                                        <td>` + makeBadge(it.forwarded_by_name, "bg-success") + `</td>
                                        <td>` + it.remarks + `</td>
                                        <td>` + it.destination + `</td>
                                        <td>` + makeBadge(it.tagged_by_name, "bg-info") + `</td>
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
            const $modal = $("#patient_modal");
            const patient = $("#referral_id_" + id + " td:eq(2)").text();
            const age = $("#referral_id_" + id + " td:eq(3)").text();
            const sex = $("#referral_id_" + id + " td:eq(4)").text();
            const ward = $("#referral_id_" + id + " td:eq(5)").text().trim();
            const bedNo = $("#referral_id_" + id + " td:eq(6)").text().trim();
            const chief_complaint = $("#referral_id_" + id + " td:eq(7)").text();
            const diagnosis = $("#referral_id_" + id + " td:eq(8)").text();

            $modal.find("#patient").text(patient);
            $modal.find("#age").text(age + " years old");
            $modal.find("#sex").text(sex);
            $modal.find("#ward_input").val(ward === "-" ? "" : ward);
            $modal.find("#bed_no_input").val(bedNo === "-" ? "" : bedNo);
            $modal.find("#ward_input").prop("disabled", false);
            $modal.find("#bed_no_input").prop("disabled", false);
            $modal.find("#chief_complaint").text(chief_complaint);
            $modal.find("#diagnosis").text(diagnosis);

            if (window.hoistModals) {
                window.hoistModals();
            }
            if (window.cleanupModals) {
                window.cleanupModals();
            }
            const modal = getPatientModal();
            if (modal) modal.show();
            getLogs(id);
        }

        function bindEvents() {
            $(".track-div").removeClass("d-none");
            $("#activity_container").removeClass("d-none");
            page = 0;
            $("#search_patient").val("");
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

            $("#spa-content").off("click", "#btn_search").on("click", "#btn_search", function () {
                page = 0;
                getReferrals();
            });

            $("#spa-content").off("keydown", "#search_patient").on("keydown", "#search_patient", function (e) {
                if (e.key === "Enter") {
                    e.preventDefault();
                    $("#btn_search").click();
                }
            });

            $("#spa-content").off("click", "#btn_next").on("click", "#btn_next", function () {
                page++;
                getReferrals();
                $("#btn_prev").removeClass("d-none");
            });

            $("#spa-content").off("click", "#btn_prev").on("click", "#btn_prev", function () {
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
                const ward = $("#patient_modal #ward_input").val().trim();
                const bedNo = $("#patient_modal #bed_no_input").val().trim();

                if (confirm("Are you sure you want to tag this as done?")) {
                    $(this).prop("disabled", true);
                    $(this).html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...`);
                    fetch('{{ route('tagAsDone') }}', {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        body: JSON.stringify({id: referral_id, remarks: remarks, ward: ward, bed_no: bedNo})
                    })
                        .then(res => res.json())
                        .then(data => {
                            $("#btn_done").prop("disabled", false);
                            $("#btn_done").html("TAG AS DONE");
                            if (data.code == 500) {
                                toastr.error(data.message, "Ooops");
                                return;
                            }

                    const row = $("#referral_id_" + referral_id);
                    if (row.length) {
                        row.find("td:eq(5)").text(ward || "-");
                        row.find("td:eq(6)").text(bedNo || "-");
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
                const department = $("#referral_id_" + referral_id + " td:eq(9)").text().trim();
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
                const referring_hospital = $("#referral_id_" + referral_id + " td:eq(1)").text();
                const patient = $("#referral_id_" + referral_id + " td:eq(2)").text();
                const age = $("#referral_id_" + referral_id + " td:eq(3)").text();
                const gender = $("#referral_id_" + referral_id + " td:eq(4)").text();
                const ward = $("#patient_modal #ward_input").val().trim();
                const bedNo = $("#patient_modal #bed_no_input").val().trim();
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
                    ward: ward,
                    bed_no: bedNo,
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

                        const row = $("#referral_id_" + referral_id);
                        if (row.length) {
                            row.find("td:eq(5)").text(ward || "-");
                            row.find("td:eq(6)").text(bedNo || "-");
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
