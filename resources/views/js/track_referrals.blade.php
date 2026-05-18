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

        function getQueueModal() {
            const el = document.getElementById('queue_modal');
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
            $("#referral_list").append("<tr><td colspan='8'>Loading data, please wait ...</td></tr>");
            fetch('{{ route('getAllReferrals') }}?search_patient=' + patient +
                "&page=" + page, {
                method: "GET"
            })
                .then(res => res.json())
                .then(data => {
                    $("#pagination_container").addClass("d-none")
                    $("#referral_list").empty();
                    $("#btn_next").addClass("d-none");
                    if (data.results.length < 1) {
                        $("#referral_list").append("<tr><td colspan='8'>No record found</td></tr>");
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
                        const department = (it.department) ? it.department : "";
                        const tr = `<tr id="referral_id_` + it.id + `">
                                        <td>` + getTypeBadge(it.type) + `</td>
                                        <td>` + it.referring_hospital + `</td>
                                        <td>` + it.patient + `</td>
                                        <td>` + it.age + `</td>
                                        <td>` + it.gender + `</td>
                                        <td class="d-none" style="max-width: 100px;text-overflow:ellipsis;white-space: nowrap;overflow: hidden">` + it.chief_complaint + `</td>
                                        <td class="d-none">` + it.diagnosis + `</td>
                                        <td>` + department + `</td>
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
                    if (data.referral) {
                        const ward = (data.referral.ward || "").toString().trim();
                        const bedNo = (data.referral.bed_no || "").toString().trim();
                        $("#patient_modal #ward_input").val(ward || "-");
                        $("#patient_modal #bed_no_input").val(bedNo || "-");
                        $("#patient_modal #ward_input").prop("disabled", true);
                        $("#patient_modal #bed_no_input").prop("disabled", true);
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
            const patient = $("#referral_id_" + id + " td:eq(2)").text();
            const age = $("#referral_id_" + id + " td:eq(3)").text();
            const sex = $("#referral_id_" + id + " td:eq(4)").text();
            const chief_complaint = $("#referral_id_" + id + " td:eq(5)").text();
            const diagnosis = $("#referral_id_" + id + " td:eq(6)").text();

            const $modal = $("#patient_modal");
            $modal.find("#patient").text(patient);
            $modal.find("#age").text(age + " years old");
            $modal.find("#sex").text(sex);
            $modal.find("#ward_input").val("-");
            $modal.find("#bed_no_input").val("-");
            $modal.find("#ward_input").prop("disabled", true);
            $modal.find("#bed_no_input").prop("disabled", true);
            $modal.find("#chief_complaint").text(chief_complaint);
            $modal.find("#diagnosis").text(diagnosis);
            @if(session('code') === 'TRIAGGE')
            $(".track-div").removeClass("d-none");
            $(".track-div #btn_done").hide();
            $(".track-div #btn_send").hide();
            @else
            $(".track-div").addClass("d-none");
            @endif
            $("#activity_container").removeClass("d-none");

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
            $(".track-div").addClass("d-none");
            $("#activity_container").removeClass("d-none");
            page = 0;
            $("#search_patient").val("");
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

            @if(session('code') === 'TRIAGGE')
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
            @endif
        }

        $(document).ready(function () {
            bindEvents();
            getReferrals();
        });
    })();
</script>
