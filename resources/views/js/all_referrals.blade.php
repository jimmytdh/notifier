<script>
    (function () {
        let firebase_map = {};
        let firebase_is_init = false;
        let firebase_page = 0;
        const FIREBASE_PAGE_SIZE = 10;
        let current_source = null; // 'firebase' | 'db'
        let current_firebase_key = null;
        let current_db_id = null;
        let db_page = 0;

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

        // ── Firebase section ──────────────────────────────────────────────────

        function getFirebaseSearchTerm() {
            return ($("#firebase_search").val() || '').trim().toUpperCase();
        }

        function renderFirebaseList() {
            const searchTerm = getFirebaseSearchTerm();
            const all = Object.values(firebase_map)
                .sort((a, b) => new Date(b.date_referred) - new Date(a.date_referred));

            const filtered = searchTerm
                ? all.filter(d => (d.patient || '').toUpperCase().includes(searchTerm))
                : all;

            $("#firebase_badge").text(filtered.length);

            const $list = $("#firebase_list");
            $list.empty();

            if (filtered.length === 0) {
                $("#firebase_pagination").addClass("d-none");
                $list.append("<tr><td colspan='7'>No live pending referrals</td></tr>");
                return;
            }

            const totalPages = Math.ceil(filtered.length / FIREBASE_PAGE_SIZE);
            if (firebase_page >= totalPages) firebase_page = totalPages - 1;

            const start = firebase_page * FIREBASE_PAGE_SIZE;
            const page_items = filtered.slice(start, start + FIREBASE_PAGE_SIZE);

            $("#firebase_pagination").removeClass("d-none");
            $("#firebase_page_count").text((start + 1) + ' - ' + (start + page_items.length));
            $("#firebase_btn_prev").toggleClass("d-none", firebase_page === 0);
            $("#firebase_btn_next").toggleClass("d-none", firebase_page >= totalPages - 1);

            page_items.forEach(function (data) {
                const sex = data.sex || data.gender || '';
                const tr = `<tr id="firebase_row_${data.key}">
                                <td>${data.referring_hospital || ''}</td>
                                <td>${data.patient || ''}</td>
                                <td>${data.age || ''}</td>
                                <td>${sex}</td>
                                <td class="d-none">${data.chiefComplaint || data.chief_complaint || ''}</td>
                                <td class="d-none">${data.diagnosis || ''}</td>
                                <td>${data.department || ''}</td>
                                <td>${moment(data.date_referred).format('MM/DD/YYYY hh:mm:ss A')}</td>
                                <td>
                                    <button class='btn btn-sm' style="background:transparent" onClick='viewFirebasePatient("${data.key}")'>
                                        <i class="bi bi-eye-fill text-success"></i>
                                    </button>
                                </td>
                            </tr>`;
                $list.append(tr);
            });
        }

        function loadFirebasePending() {
            observe("23", function (snapshot) {
                if (!firebase_is_init || !snapshot.val()) return;
                firebase_map[snapshot.key] = Object.assign({}, snapshot.val(), { key: snapshot.key });
                renderFirebaseList();
            });

            observeRemoved("23", function (snapshot) {
                delete firebase_map[snapshot.key];
                renderFirebaseList();
            });

            get("23", function (snapshot) {
                firebase_map = {};
                snapshot.forEach(function (child) {
                    firebase_map[child.key] = Object.assign({}, child.val(), { key: child.key });
                });
                firebase_is_init = true;
                renderFirebaseList();
            });
        }

        window.viewFirebasePatient = function (key) {
            const data = firebase_map[key];
            if (!data) return;

            current_source = 'firebase';
            current_firebase_key = key;
            current_db_id = null;

            const $modal = $("#patient_modal");
            $modal.find("#patient").text(data.patient || '');
            $modal.find("#age").text((data.age || '') + " years old");
            $modal.find("#sex").text(data.sex || data.gender || '');
            $modal.find("#ward_input").val('').prop("disabled", true);
            $modal.find("#bed_no_input").val('').prop("disabled", true);
            $modal.find("#chief_complaint").text(data.chiefComplaint || data.chief_complaint || '');
            $modal.find("#diagnosis").text(data.diagnosis || '');
            $("#activity_container").addClass("d-none");

            if (window.hoistModals) window.hoistModals();
            if (window.cleanupModals) window.cleanupModals();
            const modal = getPatientModal();
            if (modal) modal.show();
        };

        // ── DB section ────────────────────────────────────────────────────────

        function getTypeBadge(type) {
            const normalized = (type || 'Referral').toString().trim().toLowerCase();
            if (normalized === 'manual') {
                return `<span class="badge rounded-pill bg-warning text-dark">Manual</span>`;
            }
            return `<span class="badge rounded-pill bg-primary">Referral</span>`;
        }

        function getStatusBadge(status) {
            if (!status) return `<span class="badge rounded-pill bg-success">Active</span>`;
            if (status === 'DONE') return `<span class="badge rounded-pill bg-secondary">Done</span>`;
            return `<span class="badge rounded-pill bg-warning text-dark">${status}</span>`;
        }

        function getDbReferrals() {
            const patient = (String($("#search_patient").val() || '')).trim().toUpperCase();
            $("#referral_list").empty();
            $("#referral_list").append("<tr><td colspan='9'>Loading data, please wait ...</td></tr>");
            fetch('{{ route('getTriaggeReferrals') }}?search_patient=' + encodeURIComponent(patient) + '&page=' + db_page, {
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

                    $("#page_items_count").text(((db_page * 10) + 1) + ' - ' + ((db_page * 10) + max_visible));

                    for (let i = 0; i < max_visible; i++) {
                        const it = data.results[i];
                        const tr = `<tr id="referral_id_${it.id}" data-type="${it.type || ''}">
                                        <td>${getTypeBadge(it.type)}</td>
                                        <td>${it.referring_hospital || ''}</td>
                                        <td>${it.patient || ''}</td>
                                        <td>${it.age || ''}</td>
                                        <td>${it.gender || ''}</td>
                                        <td class="d-none">${it.ward || ''}</td>
                                        <td class="d-none">${it.bed_no || ''}</td>
                                        <td class="d-none">${it.chief_complaint || ''}</td>
                                        <td class="d-none">${it.diagnosis || ''}</td>
                                        <td>${it.department || ''}</td>
                                        <td>${getStatusBadge(it.status)}</td>
                                        <td>${moment(it.date_created).format('MM/DD/YYYY hh:mm:ss A')}</td>
                                        <td>
                                            <button class='btn btn-sm' style="background:transparent" onClick='viewDbPatient("${it.id}")'>
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
                        $("#patient_modal #ward_input").val((data.referral.ward || '').toString().trim());
                        $("#patient_modal #bed_no_input").val((data.referral.bed_no || '').toString().trim());
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

        window.viewDbPatient = function (id) {
            current_source = 'db';
            current_db_id = id;
            current_firebase_key = null;

            const patient   = $("#referral_id_" + id + " td:eq(2)").text();
            const age       = $("#referral_id_" + id + " td:eq(3)").text();
            const sex       = $("#referral_id_" + id + " td:eq(4)").text();
            const ward      = $("#referral_id_" + id + " td:eq(5)").text().trim();
            const bedNo     = $("#referral_id_" + id + " td:eq(6)").text().trim();
            const cc        = $("#referral_id_" + id + " td:eq(7)").text();
            const diagnosis = $("#referral_id_" + id + " td:eq(8)").text();

            const $modal = $("#patient_modal");
            $modal.find("#patient").text(patient);
            $modal.find("#age").text(age + " years old");
            $modal.find("#sex").text(sex);
            $modal.find("#ward_input").val(ward).prop("disabled", true);
            $modal.find("#bed_no_input").val(bedNo).prop("disabled", true);
            $modal.find("#chief_complaint").text(cc);
            $modal.find("#diagnosis").text(diagnosis);
            $("#activity_container").removeClass("d-none");
            $("#activity_logs").empty();

            if (window.hoistModals) window.hoistModals();
            if (window.cleanupModals) window.cleanupModals();
            const modal = getPatientModal();
            if (modal) modal.show();
            getLogs(id);
        };

        // ── Shared queue logic ────────────────────────────────────────────────

        function bindEvents() {
            $(".track-div #btn_done").hide();
            $(".track-div #btn_send").hide();
            $(".track-div").removeClass("d-none");
            db_page = 0;
            $("#search_patient").val("");

            loadFirebasePending();
            getDbReferrals();

            $("#spa-content").off("click", "#btn_firebase_search").on("click", "#btn_firebase_search", function () {
                firebase_page = 0;
                renderFirebaseList();
            });

            $("#spa-content").off("keydown", "#firebase_search").on("keydown", "#firebase_search", function (e) {
                if (e.key === "Enter") {
                    e.preventDefault();
                    firebase_page = 0;
                    renderFirebaseList();
                }
            });

            $("#spa-content").off("click", "#firebase_btn_next").on("click", "#firebase_btn_next", function () {
                firebase_page++;
                renderFirebaseList();
            });

            $("#spa-content").off("click", "#firebase_btn_prev").on("click", "#firebase_btn_prev", function () {
                if (firebase_page === 0) return;
                firebase_page--;
                renderFirebaseList();
            });

            $("#spa-content").off("click", "#btn_search").on("click", "#btn_search", function () {
                db_page = 0;
                getDbReferrals();
            });

            $("#spa-content").off("keydown", "#search_patient").on("keydown", "#search_patient", function (e) {
                if (e.key === "Enter") {
                    e.preventDefault();
                    $("#btn_search").click();
                }
            });

            $("#spa-content").off("click", "#btn_next").on("click", "#btn_next", function () {
                db_page++;
                getDbReferrals();
                $("#btn_prev").removeClass("d-none");
            });

            $("#spa-content").off("click", "#btn_prev").on("click", "#btn_prev", function () {
                if (db_page == 0) return;
                if (db_page == 1) $("#btn_prev").addClass("d-none");
                db_page--;
                getDbReferrals();
            });

            $("#btn_queue").off("click").on("click", function () {
                if (!current_source) {
                    toastr.error("No selected patient", "Error");
                    return;
                }
                let queueType = 'Referral';
                if (current_source === 'db' && current_db_id) {
                    queueType = $("#referral_id_" + current_db_id).data("type") || '';
                }
                if (window.setQueueDeptOptions) window.setQueueDeptOptions(queueType);
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

                let params;
                if (current_source === 'firebase') {
                    const data = firebase_map[current_firebase_key];
                    if (!data) return;
                    params = {
                        referring_hospital: data.referring_hospital || '',
                        patient:            data.patient            || '',
                        age:                String(data.age         || ''),
                        gender:             data.sex || data.gender || '',
                        department:         department,
                        remarks:            "QUEUED FROM TRIAGGE",
                        diagnosis:          data.diagnosis                            || '',
                        chief_complaint:    data.chiefComplaint || data.chief_complaint || ''
                    };
                } else {
                    params = {
                        referring_hospital: $("#referral_id_" + current_db_id + " td:eq(1)").text().trim(),
                        patient:            $("#referral_id_" + current_db_id + " td:eq(2)").text().trim(),
                        age:                $("#referral_id_" + current_db_id + " td:eq(3)").text().trim(),
                        gender:             $("#referral_id_" + current_db_id + " td:eq(4)").text().trim(),
                        department:         department,
                        remarks:            "QUEUED FROM TRIAGGE",
                        chief_complaint:    $("#referral_id_" + current_db_id + " td:eq(7)").text().trim(),
                        diagnosis:          $("#referral_id_" + current_db_id + " td:eq(8)").text().trim()
                    };
                }

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
        }

        $(document).ready(function () {
            bindEvents();
        });
    })();
</script>
