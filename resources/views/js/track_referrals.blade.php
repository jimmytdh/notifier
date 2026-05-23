<script>
    (function () {
        let _store = {};
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

        function getAvatarSrc(age, sex) {
            const a = parseInt(age, 10) || 0;
            let ageGroup;
            if (a <= 12) ageGroup = '0_12';
            else if (a <= 59) ageGroup = '13_59';
            else ageGroup = '60_plus';
            const gender = (sex || '').toLowerCase().indexOf('f') !== -1 ? 'female' : 'male';
            return 'assets/img/avatar_' + ageGroup + '_' + gender + '.png';
        }

        function getDeptChipHtml(dept) {
            const d = (dept || '').toString().trim().toUpperCase();
            if (d === 'ER' || d === 'EMERGENCY ROOM' || d === 'EMERGENCY') {
                return `<span class="st-dept-tag st-dept-er">${dept}</span>`;
            }
            return `<span class="st-dept-tag st-dept-default">${dept || '-'}</span>`;
        }

        function getTimeAgo(dateStr) {
            if (!dateStr) return '';
            const now = moment();
            const then = moment(dateStr);
            const diff = now.diff(then, 'minutes');
            if (diff < 1) return 'just now';
            if (diff < 60) return diff + 'm ago';
            const hrs = Math.floor(diff / 60);
            if (hrs < 24) return hrs + 'h ago';
            return Math.floor(hrs / 24) + 'd ago';
        }

        function getReferrals() {
            const patient = (String($("#search_patient").val() || "")).trim().toUpperCase();
            _store = {};
            $("#referral_list").empty();
            $("#referral_list").html(
                `<div class="st-state-row"><i class="bi bi-arrow-repeat"></i><p>Loading data, please wait&hellip;</p></div>`
            );
            fetch('{{ route('getAllReferrals') }}?search_patient=' + encodeURIComponent(patient) + '&page=' + page, {
                method: 'GET'
            })
                .then(res => res.json())
                .then(data => {
                    $("#pagination_container").addClass("d-none");
                    $("#referral_list").empty();
                    $("#btn_next").addClass("d-none");

                    if (data.results.length < 1) {
                        $("#referral_list").html(
                            `<div class="st-state-row"><i class="bi bi-inbox"></i><p>No record found</p></div>`
                        );
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
                        _store[it.id] = it;

                        const typeKey = (it.type || 'referral').toString().trim().toLowerCase();
                        const typeLbl = typeKey === 'manual' ? 'Manual' : 'Referral';
                        const avatarSrc = getAvatarSrc(it.age, it.gender);
                        const department = it.department || '';
                        const deptHtml = getDeptChipHtml(department);
                        const timeAgo = getTimeAgo(it.date_created);

                        const card = `<div class="st-ref-card" id="referral_id_${it.id}" onclick="viewPatient('${it.id}')">
                            <div class="st-card-accent"></div>
                            <div class="st-card-hosp">
                                <div class="st-hosp-name"><i class="bi bi-hospital-fill"></i><span>${it.referring_hospital || ''}</span></div>
                                <span class="st-type-badge st-type-${typeKey}">${typeLbl}</span>
                            </div>
                            <div class="st-card-patient">
                                <img src="${avatarSrc}" class="st-avatar" alt="">
                                <div>
                                    <p class="st-patient-name">${it.patient || ''}</p>
                                    <div class="st-patient-meta">
                                        <span>${it.age || ''} Y</span><span class="st-meta-dot"></span><span>${it.gender || ''}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="st-card-tags">
                                ${deptHtml}
                                <span class="st-time"><i class="bi bi-clock"></i> ${timeAgo}</span>
                            </div>
                            <div class="st-card-actions">
                                <button class="st-btn-view" onclick="event.stopPropagation();viewPatient('${it.id}')">
                                    <i class="bi bi-eye"></i> View Details
                                </button>
                            </div>
                        </div>`;
                        $("#referral_list").append(card);
                    }
                }).catch(err => {
                    console.log(err);
                });
        }

        function getLogs(id) {
            $("#activity_logs").empty();
            fetch('{{ route('loadLogs') }}?id=' + id, {
                method: 'GET'
            })
                .then(res => res.json())
                .then(data => {
                    if (data.referral) {
                        const ward = (data.referral.ward || '').toString().trim();
                        const bedNo = (data.referral.bed_no || '').toString().trim();
                        $("#patient_modal #ward_input").val(ward || '-');
                        $("#patient_modal #bed_no_input").val(bedNo || '-');
                        $("#patient_modal #ward_input").prop('disabled', true);
                        $("#patient_modal #bed_no_input").prop('disabled', true);
                    }
                    const makeBadge = function (value, tone) {
                        if (!value) return `<span class="text-muted">-</span>`;
                        const cls = tone || 'bg-secondary';
                        return `<span class="badge rounded-pill ${cls}">${value}</span>`;
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
            const rec = _store[id] || {};
            const $modal = $("#patient_modal");

            const age = rec.age || '';
            const sex = rec.gender || '';

            $modal.find("#employee_img").attr('src', getAvatarSrc(age, sex));
            $modal.find("#patient").text(rec.patient || '');
            $modal.find("#age").text(age + ' years old');
            $modal.find("#sex").text(sex);
            $modal.find("#referring_hospital").text(rec.referring_hospital || '');
            $modal.find("#date_referred").text(rec.date_created ? moment(rec.date_created).format('MMMM DD, YYYY hh:mm A') : '');
            $modal.find("#ward_input").val('-');
            $modal.find("#bed_no_input").val('-');
            $modal.find("#ward_input").prop('disabled', true);
            $modal.find("#bed_no_input").prop('disabled', true);
            $modal.find("#chief_complaint").text(rec.chief_complaint || '');
            $modal.find("#diagnosis").text(rec.diagnosis || '');

            @if(session('code') === 'TRIAGGE')
            $(".track-div").removeClass("d-none");
            $(".track-div #btn_done").hide();
            $(".track-div #btn_send").hide();
            @else
            $(".track-div").addClass("d-none");
            @endif
            $("#activity_container").removeClass("d-none");

            if (window.hoistModals) window.hoistModals();
            if (window.cleanupModals) window.cleanupModals();
            const modal = getPatientModal();
            if (modal) modal.show();
            getLogs(id);
        };

        function bindEvents() {
            $(".track-div").addClass("d-none");
            $("#activity_container").removeClass("d-none");
            page = 0;
            $("#search_patient").val('');
            $("#patient_modal .card-body").prop('disabled', true);

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
                const rec = _store[referral_id] || {};
                if (window.setQueueDeptOptions) window.setQueueDeptOptions(rec.type);
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

                const rec = _store[referral_id] || {};
                const params = {
                    referring_hospital: rec.referring_hospital || '',
                    patient:            rec.patient            || '',
                    age:                rec.age                || '',
                    gender:             rec.gender             || '',
                    department:         department,
                    remarks:            "QUEUED FROM TRIAGGE",
                    diagnosis:          rec.diagnosis          || '',
                    chief_complaint:    rec.chief_complaint    || ''
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
