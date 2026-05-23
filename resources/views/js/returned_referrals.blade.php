<script>
    (function () {
        let _store = {};
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
            const patient = (String($("#search_patient").val() || '')).trim().toUpperCase();
            _store = {};
            $("#referral_list").empty();
            $("#referral_list").html(
                `<div class="st-state-row"><i class="bi bi-arrow-repeat"></i><p>Loading data, please wait&hellip;</p></div>`
            );
            fetch('{{ route('getReturnedReferrals') }}?search_patient=' + encodeURIComponent(patient) + '&page=' + page, {
                method: 'GET'
            })
                .then(res => res.json())
                .then(data => {
                    $("#pagination_container").addClass("d-none");
                    $("#referral_list").empty();
                    $("#btn_next").addClass("d-none");

                    if (!data.results || data.results.length < 1) {
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
                        const deptHtml = getDeptChipHtml(it.department);
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
            fetch('{{ route('loadLogs') }}?id=' + id, { method: 'GET' })
                .then(res => res.json())
                .then(data => {
                    if (data.referral) {
                        const ward = (data.referral.ward || '').toString().trim();
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
            const rec = _store[id] || {};
            const $modal = $("#patient_modal");

            const age = rec.age || '';
            const sex = rec.gender || '';
            const ward = (rec.ward || '').toString().trim();
            const bedNo = (rec.bed_no || '').toString().trim();

            $modal.find("#employee_img").attr('src', getAvatarSrc(age, sex));
            $modal.find("#patient").text(rec.patient || '');
            $modal.find("#age").text(age + ' years old');
            $modal.find("#sex").text(sex);
            $modal.find("#referring_hospital").text(rec.referring_hospital || '');
            $modal.find("#date_referred").text(rec.date_created ? moment(rec.date_created).format('MMMM DD, YYYY hh:mm A') : '');
            $modal.find("#ward_input").val(ward).prop('disabled', false);
            $modal.find("#bed_no_input").val(bedNo).prop('disabled', false);
            $modal.find("#chief_complaint").text(rec.chief_complaint || '');
            $modal.find("#diagnosis").text(rec.diagnosis || '');

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
            $("#search_patient").val('');

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
                const $btn = $(this);
                const ward  = $("#patient_modal #ward_input").val().trim();
                const bedNo = $("#patient_modal #bed_no_input").val().trim();
                Swal.fire({
                    title: 'Tag as Done',
                    input: 'text',
                    inputLabel: 'Remarks (optional)',
                    inputValue: 'TAGGED AS DONE',
                    inputPlaceholder: 'Enter remarks...',
                    showCancelButton: true,
                    confirmButtonText: 'Tag as Done',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#003166',
                }).then(function (result) {
                    if (!result.isConfirmed) return;
                    const remarks = (result.value || '').trim() || 'TAGGED AS DONE';

                $btn.prop("disabled", true);
                $btn.html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...`);
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
                            const errModal = getPatientModal();
                            if (errModal) errModal.hide();
                            getReferrals();
                            return;
                        }
                        const modal = getPatientModal();
                        if (modal) modal.hide();
                        toastr.success(data.message, "Information");
                        getReferrals();
                    }).catch(err => {
                        $btn.prop("disabled", false);
                        $btn.html(`<i class="bi bi-check2-circle me-1"></i>TAG AS DONE`);
                        toastr.error(err, "Ooops");
                    });
                }); // end Swal.then
            });

            // QUEUE PATIENT
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

                        insert(params.department, params.patient, function (message) {
                            deleteChild(params.department);
                        });
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
                const topic = $("#forward_department").val();
                const department = $("#forward_department").val();
                const remarks = $("#forward_remarks").val().trim();
                const ward = $("#patient_modal #ward_input").val().trim();
                const bedNo = $("#patient_modal #bed_no_input").val().trim();

                if (topic === "") {
                    $("#forward_department").addClass("is-invalid");
                    return;
                }
                if (remarks === "") {
                    $("#forward_remarks").addClass("is-invalid");
                    return;
                }

                const rec = _store[referral_id] || {};
                const patient = rec.patient || '';

                const params = {
                    id: referral_id,
                    department: department,
                    code: topic,
                    remarks: remarks,
                    ward: ward,
                    bed_no: bedNo
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
                            const errFwd = getForwardModal();
                            if (errFwd) errFwd.hide();
                            const errPat = getPatientModal();
                            if (errPat) errPat.hide();
                            getReferrals();
                            return;
                        }
                        const forwardModal = getForwardModal();
                        if (forwardModal) forwardModal.hide();
                        const modal = getPatientModal();
                        if (modal) modal.hide();
                        toastr.success(data.message, "Information");
                        console.log(params);
                        insert(params.code, patient, function (message) {
                            deleteChild(params.code);
                        });

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
