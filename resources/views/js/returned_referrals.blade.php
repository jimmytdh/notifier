<script>
    (function () {
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

        function getQueueModal() {
            const el = document.getElementById('queue_modal');
            if (!el || !window.bootstrap || !window.bootstrap.Modal) return null;
            return window.bootstrap.Modal.getOrCreateInstance(el);
        }

        function getTypeBadge(type) {
            const normalized = (type || 'Referral').toString().trim().toLowerCase();
            if (normalized === 'manual') {
                return `<span class="badge rounded-pill bg-warning text-dark">Manual</span>`;
            }
            return `<span class="badge rounded-pill bg-primary">Referral</span>`;
        }

        function getReferrals() {
            const patient = (String($("#search_patient").val() || '')).trim().toUpperCase();
            $("#referral_list").empty();
            $("#referral_list").append("<tr><td colspan='9'>Loading data, please wait ...</td></tr>");
            fetch('{{ route('getReturnedReferrals') }}?search_patient=' + encodeURIComponent(patient) + '&page=' + page, {
                method: 'GET'
            })
                .then(res => res.json())
                .then(data => {
                    $("#pagination_container").addClass("d-none");
                    $("#referral_list").empty();
                    $("#btn_next").addClass("d-none");

                    if (!data.results || data.results.length < 1) {
                        $("#referral_list").append("<tr><td colspan='9'>No record found</td></tr>");
                        return;
                    }

                    $("#pagination_container").removeClass("d-none");

                    let max_visible = data.results.length;
                    if (max_visible == 11) {
                        max_visible = 10;
                        $("#btn_next").removeClass("d-none");
                    }

                    $("#page_items_count").text(((page * 10) + 1) + ' - ' + ((page * 10) + max_visible));

                    for (let i = 0; i < max_visible; i++) {
                        const it = data.results[i];
                        const ward   = it.ward   || '';
                        const bedNo  = it.bed_no || '';
                        const tr = `<tr id="referral_id_${it.id}">
                                        <td>${getTypeBadge(it.type)}</td>
                                        <td>${it.referring_hospital || ''}</td>
                                        <td>${it.patient || ''}</td>
                                        <td>${it.age || ''}</td>
                                        <td>${it.gender || ''}</td>
                                        <td>${ward || '-'}</td>
                                        <td>${bedNo || '-'}</td>
                                        <td class="d-none">${ward}</td>
                                        <td class="d-none">${bedNo}</td>
                                        <td class="d-none">${it.chief_complaint || ''}</td>
                                        <td class="d-none">${it.diagnosis || ''}</td>
                                        <td>${moment(it.date_created).format('MM/DD/YYYY hh:mm:ss A')}</td>
                                        <td>
                                            <button class='btn btn-sm' style="background:transparent" onClick='viewPatient("${it.id}")'>
                                                <i class="bi bi-eye-fill text-success"></i>
                                            </button>
                                        </td>
                                    </tr>`;
                        $("#referral_list").append(tr);
                    }
                }).catch(err => {
                    console.log(err);
                });
        }

        function getLogs(id) {
            $("#activity_logs").empty();
            fetch('{{ route('loadLogs') }}?id=' + id, { method: 'GET' })
                .then(res => res.json())
                .then(data => {
                    if (data.referral) {
                        const ward  = (data.referral.ward  || '').toString().trim();
                        const bedNo = (data.referral.bed_no || '').toString().trim();
                        $("#patient_modal #ward_input").val(ward);
                        $("#patient_modal #bed_no_input").val(bedNo);
                    }
                    const makeBadge = (value, tone) => {
                        if (!value) return `<span class="text-muted">-</span>`;
                        return `<span class="badge rounded-pill ${tone || 'bg-secondary'}">${value}</span>`;
                    };
                    data.results.forEach(it => {
                        const tr = `<tr>
                                        <td>${it.origin}</td>
                                        <td>${makeBadge(it.forwarded_by_name, 'bg-success')}</td>
                                        <td>${it.remarks}</td>
                                        <td>${it.destination}</td>
                                        <td>${makeBadge(it.tagged_by_name, 'bg-info')}</td>
                                        <td>${moment(it.date_forwarded).format('MM/DD/YYYY hh:mm:ss A')}</td>
                                    </tr>`;
                        $("#activity_logs").append(tr);
                    });
                }).catch(err => {
                    toastr.error(err, 'Ooops');
                });
        }

        window.viewPatient = function (id) {
            referral_id = id;
            const patient   = $("#referral_id_" + id + " td:eq(2)").text();
            const age       = $("#referral_id_" + id + " td:eq(3)").text();
            const sex       = $("#referral_id_" + id + " td:eq(4)").text();
            const ward      = $("#referral_id_" + id + " td:eq(7)").text().trim();
            const bedNo     = $("#referral_id_" + id + " td:eq(8)").text().trim();
            const cc        = $("#referral_id_" + id + " td:eq(9)").text();
            const diagnosis = $("#referral_id_" + id + " td:eq(10)").text();

            const $modal = $("#patient_modal");
            $modal.find("#patient").text(patient);
            $modal.find("#age").text(age + " years old");
            $modal.find("#sex").text(sex);
            $modal.find("#ward_input").val(ward).prop("disabled", false);
            $modal.find("#bed_no_input").val(bedNo).prop("disabled", false);
            $modal.find("#chief_complaint").text(cc);
            $modal.find("#diagnosis").text(diagnosis);

            if (window.hoistModals) window.hoistModals();
            if (window.cleanupModals) window.cleanupModals();
            const modal = getPatientModal();
            if (modal) modal.show();
            getLogs(id);
        };

        function bindEvents() {
            $(".track-div").removeClass("d-none");
            $("#activity_container").removeClass("d-none");
            page = 0;
            $("#search_patient").val("");

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
                if (page == 1) $("#btn_prev").addClass("d-none");
                page--;
                getReferrals();
            });

            // TAG AS DONE
            $("#btn_done").off("click").on("click", function () {
                let remarks = prompt("Remarks (optional):", "TAGGED AS DONE");
                if (remarks === null) return;
                remarks = remarks.trim();
                if (remarks === "") remarks = "TAGGED AS DONE";

                if (!confirm("Are you sure you want to tag this as done?")) return;

                const ward  = $("#patient_modal #ward_input").val().trim();
                const bedNo = $("#patient_modal #bed_no_input").val().trim();

                $(this).prop("disabled", true);
                $(this).html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...`);
                fetch('{{ route('tagAsDone') }}', {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    body: JSON.stringify({ id: referral_id, remarks: remarks, ward: ward, bed_no: bedNo })
                })
                    .then(res => res.json())
                    .then(data => {
                        $("#btn_done").prop("disabled", false);
                        $("#btn_done").html(`<i class="bi bi-check2-circle me-1"></i>TAG AS DONE`);
                        if (data.code == 500) {
                            toastr.error(data.message, "Ooops");
                            return;
                        }
                        const modal = getPatientModal();
                        if (modal) modal.hide();
                        toastr.success(data.message, "Information");
                        getReferrals();
                    }).catch(err => {
                        $("#btn_done").prop("disabled", false);
                        $("#btn_done").html(`<i class="bi bi-check2-circle me-1"></i>TAG AS DONE`);
                        toastr.error(err, "Ooops");
                    });
            });

            // QUEUE PATIENT
            $("#btn_queue").off("click").on("click", function () {
                if (!referral_id) {
                    toastr.error("No selected patient", "Error");
                    return;
                }
                $("#queue_dept_select").val("").removeClass("is-invalid");
                const queueModal = getQueueModal();
                if (queueModal) queueModal.show();
            });

            $("#btn_confirm_queue").off("click").on("click", function () {
                const department = $("#queue_dept_select").val();
                if (!department) {
                    $("#queue_dept_select").addClass("is-invalid");
                    return;
                }

                const params = {
                    referring_hospital: $("#referral_id_" + referral_id + " td:eq(1)").text().trim(),
                    patient:            $("#referral_id_" + referral_id + " td:eq(2)").text().trim(),
                    age:                $("#referral_id_" + referral_id + " td:eq(3)").text().trim(),
                    gender:             $("#referral_id_" + referral_id + " td:eq(4)").text().trim(),
                    department:         department,
                    remarks:            "QUEUED FROM TRIAGGE"
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
                        $("#btn_confirm_queue").prop("disabled", false);
                        $("#btn_confirm_queue").html(`<i class="bi bi-list-check me-1"></i> Confirm Queue`);
                        if (data.code == 500) {
                            toastr.error(data.message || "Unable to queue patient", "Error");
                            return;
                        }
                        const queueModal = getQueueModal();
                        if (queueModal) queueModal.hide();
                        toastr.success(data.message, "Information");
                    }).catch(err => {
                        $("#btn_confirm_queue").prop("disabled", false);
                        $("#btn_confirm_queue").html(`<i class="bi bi-list-check me-1"></i> Confirm Queue`);
                        toastr.error(err.message || "Unable to queue patient", "Error");
                    });
            });

            // FORWARD REFERRAL
            $("#btn_send").off("click").on("click", function () {
                $("#forward_department").val("");
                $("#forward_remarks").val("");
                const modal = getForwardModal();
                if (modal) modal.show();
            });

            $("#btn_confirm_forward").off("click").on("click", function () {
                $(".is-invalid").removeClass("is-invalid");
                const topic      = $("#forward_department").val();
                const department = $("#forward_department").val();
                const remarks    = $("#forward_remarks").val().trim();
                const ward       = $("#patient_modal #ward_input").val().trim();
                const bedNo      = $("#patient_modal #bed_no_input").val().trim();

                if (topic === "") {
                    $("#forward_department").addClass("is-invalid");
                    return;
                }
                if (remarks === "") {
                    $("#forward_remarks").addClass("is-invalid");
                    return;
                }

                const patient = $("#referral_id_" + referral_id + " td:eq(2)").text().trim();

                const params = {
                    id:                 referral_id,
                    department:         department,
                    code:               topic,
                    remarks:            remarks,
                    ward:               ward,
                    bed_no:             bedNo
                };

                $(this).prop("disabled", true);
                $(this).html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...`);
                fetch('{{ route('forwardReferral') }}', {
                    method: "POST",
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
                            return;
                        }
                        const forwardModal = getForwardModal();
                        if (forwardModal) forwardModal.hide();
                        const modal = getPatientModal();
                        if (modal) modal.hide();
                        toastr.success(data.message, "Information");
                        getReferrals();
                    }).catch(err => {
                        $("#btn_confirm_forward").prop("disabled", false);
                        $("#btn_confirm_forward").html("Confirm Forward");
                        toastr.error(err, "Ooops");
                    });
            });

            getReferrals();
        }

        $(document).ready(function () {
            bindEvents();
        });
    })();
</script>
