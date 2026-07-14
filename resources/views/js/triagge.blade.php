<script>
    (function () {
        let current_list = [];
        let is_init = false;
        let referral_data;
        let page = 0;
        const page_size = 10;
        let page_cache = [];
        let next_page_available = false;

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

        /* ── Avatar helper ── */
        function getAvatarSrc(age, sex) {
            const n = parseInt(age) || 0;
            const g = (sex || '').toLowerCase().includes('f') ? 'female' : 'male';
            const a = n <= 12 ? '0_12' : n <= 59 ? '13_59' : '60_plus';
            return 'assets/img/avatar_' + a + '_' + g + '.png';
        }

        /* ── Department chip ── */
        function getDeptChipHtml(dept) {
            const d = (dept || '').toUpperCase();
            const urgent = ['ER', 'TRAUMA', 'ICU', 'CRITICAL'];
            const isUrgent = urgent.some(function (k) { return d.includes(k); });
            const cls = isUrgent ? 'tg-dept-er' : 'tg-dept-default';
            return '<span class="tg-dept-tag ' + cls + '">' + (d || '-') + '</span>';
        }

        /* ── Time ago ── */
        function getTimeAgo(dateStr) {
            if (!dateStr) return '-';
            const diff = Date.now() - new Date(dateStr).getTime();
            const mins = Math.floor(diff / 60000);
            if (mins < 1)  return 'just now';
            if (mins < 60) return mins + ' min ago';
            const hrs = Math.floor(mins / 60);
            if (hrs < 24)  return hrs + ' hr ago';
            return Math.floor(hrs / 24) + ' d ago';
        }

        function getLogs(id) {
            $("#activity_logs").empty();
            fetch('{{ route('loadLogs') }}?id=' + id, {
                method: "GET"
            })
                .then(res => res.json())
                .then(data => {
                    if (data.referral) {
                        const ward  = (data.referral.ward   || "").toString().trim();
                        const bedNo = (data.referral.bed_no || "").toString().trim();
                        $("#patient_modal #ward_input").val(ward  || "-");
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

        function getDataByKey(key) {
            for (let i = 0; i < current_list.length; i++) {
                if (current_list[i].key == key) return current_list[i];
            }
            return null;
        }

        function getPositionByKey(key) {
            for (let i = 0; i < current_list.length; i++) {
                if (current_list[i].key == key) return i;
            }
            return -1;
        }

        function setLoadingState() {
            $("#referral_list").empty();
            $("#referral_list").append(
                '<div class="tg-state-row">' +
                '<div class="spinner-border spinner-border-sm text-secondary" role="status"></div>' +
                '<p>Loading referrals, please wait&hellip;</p>' +
                '</div>'
            );
        }

        function resetPaginationAndReload() {
            page = 0;
            page_cache = [];
            next_page_available = false;
            setLoadingState();
            loadPage(0);
        }

        function loadPage(targetPage) {
            if (targetPage < 0) return;

            if (page_cache[targetPage]) {
                page = targetPage;
                current_list = page_cache[targetPage].items;
                next_page_available = page_cache[targetPage].hasMore;
                renderReferrals();
                return;
            }

            if (targetPage !== page_cache.length) return;

            const beforeKey = targetPage > 0 ? page_cache[targetPage - 1].oldestKey : null;
            if (targetPage > 0 && !beforeKey) return;

            fetchPageFromFirebase(beforeKey, function (result) {
                const pageData = {
                    items: result.items,
                    oldestKey: result.oldestKey,
                    hasMore: result.hasMore
                };
                page_cache[targetPage] = pageData;
                page = targetPage;
                current_list = result.items;
                next_page_available = result.hasMore;
                renderReferrals();
            });
        }

        function fetchPageFromFirebase(beforeKey, callback) {
            let query = firebase.database().ref("23").orderByKey();
            if (beforeKey) {
                query = query.endAt(beforeKey).limitToLast(page_size + 1);
            } else {
                query = query.limitToLast(page_size + 1);
            }

            query.once("value", function (snapshot) {
                const rows = [];
                snapshot.forEach(function (child) {
                    const item = child.val() || {};
                    item.key = child.key;
                    rows.push(item);
                });

                if (beforeKey && rows.length > 0 && rows[rows.length - 1].key === beforeKey) {
                    rows.pop();
                }
                if (!beforeKey && rows.length > page_size) {
                    rows.shift();
                }

                const hasMore = snapshot.numChildren() === (page_size + 1);
                callback({
                    items: rows,
                    oldestKey: rows.length > 0 ? rows[0].key : null,
                    hasMore: hasMore
                });
            });
        }

        function toUpper(value) {
            return (value || "").toString().trim().toUpperCase();
        }

        function getFilteredReferrals() {
            const patient    = toUpper($("#search_patient").val());
            const department = toUpper($("#search_department").val());

            return current_list.filter(function (item) {
                return toUpper(item.patient).includes(patient) &&
                       toUpper(item.department).includes(department);
            }).sort(function (a, b) {
                return new Date(b.date_referred) - new Date(a.date_referred);
            });
        }

        function renderReferrals() {
            const filtered = getFilteredReferrals();
            const total    = filtered.length;
            $("#referral_list").empty();

            if (total < 1) {
                $("#referral_list").append(
                    '<div class="tg-state-row">' +
                    '<i class="bi bi-inbox"></i>' +
                    '<p>No referrals found</p>' +
                    '</div>'
                );
                $("#page_items_count").text("0");
                $("#btn_prev").toggleClass("d-none", page === 0);
                $("#btn_next").toggleClass("d-none", !next_page_available);
                return;
            }

            const hasQueueModal = !!document.getElementById('queue_modal');

            filtered.forEach(function (data) {
                const sex      = data.sex || data.gender || "";
                const avatar   = getAvatarSrc(data.age, sex);
                const timeAgo  = getTimeAgo(data.date_referred);
                const deptHtml = getDeptChipHtml(data.department);
                const key      = data.key;


                const card =
                    '<div class="tg-ref-card" onclick="viewPatient(\'' + key + '\')">' +
                        '<div class="tg-card-accent"></div>' +
                        '<div class="tg-card-hosp">' +
                            '<div class="tg-hosp-name">' +
                                '<i class="bi bi-hospital-fill"></i>' +
                                '<span>' + (data.referring_hospital || '-') + '</span>' +
                            '</div>' +
                            '<span class="tg-new-badge">NEW</span>' +
                        '</div>' +
                        '<div class="tg-card-patient">' +
                            '<img src="' + avatar + '" class="tg-avatar" alt="' + (data.patient || '') + '">' +
                            '<div>' +
                                '<p class="tg-patient-name">' + (data.patient || '').toUpperCase() + '</p>' +
                                '<div class="tg-patient-meta">' +
                                    '<span>' + (data.age || '-') + 'Y</span>' +
                                    '<span class="tg-meta-dot"></span>' +
                                    '<span>' + sex + '</span>' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                        '<div class="tg-card-tags">' +
                            deptHtml +
                            '<span class="tg-time"><i class="bi bi-clock"></i> ' + timeAgo + '</span>' +
                        '</div>' +
                        '<div class="tg-card-actions">' +
                            '<button class="tg-btn-view" onclick="event.stopPropagation();viewPatient(\'' + key + '\')">' +
                                '<i class="bi bi-eye"></i> View Details' +
                            '</button>' +
                        '</div>' +
                    '</div>';

                $("#referral_list").append(card);
            });

            const start = (page * page_size) + 1;
            const end   = (page * page_size) + filtered.length;
            $("#page_items_count").text(start + " - " + end);
            $("#btn_prev").toggleClass("d-none", page === 0);
            $("#btn_next").toggleClass("d-none", !next_page_available);
        }

        window.viewPatient = function (key) {
            referral_data = getDataByKey(key);
            if (referral_data) {
                const $modal = $("#patient_modal");
                const sex = referral_data.sex || referral_data.gender || "";
                $modal.find("#patient").text(referral_data.patient);
                $modal.find("#age").text((referral_data.age || "-") + " Years");
                $modal.find("#sex").text(sex);
                $modal.find("#ward_input").val("-");
                $modal.find("#bed_no_input").val("-");
                $modal.find("#ward_input").prop("disabled", true);
                $modal.find("#bed_no_input").prop("disabled", true);
                $modal.find("#diagnosis").text(referral_data.diagnosis || "-");
                $modal.find("#chief_complaint").text(referral_data.chiefComplaint || "-");
                $modal.find("#employee_img").attr("src", getAvatarSrc(referral_data.age, sex));
                $modal.find("#referring_hospital").text((referral_data.referring_hospital || "-").toUpperCase());
                $modal.find("#date_referred").text(
                    referral_data.date_referred
                        ? moment(referral_data.date_referred).format("MMMM DD, YYYY hh:mm A")
                        : "-"
                );
                if (window.hoistModals)  window.hoistModals();
                if (window.cleanupModals) window.cleanupModals();
                const modal = getPatientModal();
                if (modal) modal.show();
            }
        };

        window.queuePatientDirect = function (key) {
            referral_data = getDataByKey(key);
            if (!referral_data) {
                toastr.error("Patient not found", "Error");
                return;
            }
            if (window.setQueueDeptOptions) window.setQueueDeptOptions('Referral');
            $("#queue_dept_select").val("").removeClass("is-invalid");
            const queueModal = getQueueModal();
            if (queueModal) queueModal.show();
        };

        function bindEvents() {
            $(".track-div").removeClass("d-none");
            $("#activity_container").addClass("d-none");
            setLoadingState();
            $("#pagination_container").removeClass("d-none");
            page = 0;
            $("#search_patient").val("");
            $("#search_department").val("");

            $("#spa-content").off("click", "#btn_search").on("click", "#btn_search", function () {
                page = 0;
                renderReferrals();
            });

            $("#spa-content").off("keydown", "#search_patient, #search_department").on("keydown", "#search_patient, #search_department", function (e) {
                if (e.key === "Enter") {
                    e.preventDefault();
                    $("#btn_search").click();
                }
            });

            $("#spa-content").off("click", "#btn_next").on("click", "#btn_next", function () {
                loadPage(page + 1);
            });

            $("#spa-content").off("click", "#btn_prev").on("click", "#btn_prev", function () {
                loadPage(page - 1);
            });

            $("#btn_done").off("click").on("click", function () {
                const $btn = $(this);
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

                const params = {
                    firebase_key:       referral_data.key,
                    patient:            referral_data.patient,
                    gender:             referral_data.sex,
                    age:                referral_data.age,
                    diagnosis:          referral_data.diagnosis,
                    chief_complaint:    referral_data.chiefComplaint,
                    referring_hospital: referral_data.referring_hospital,
                    department:         "",
                    topic:              "",
                    remarks:            remarks,
                    date_created:       referral_data.date_referred
                };

                $btn.prop("disabled", true);
                $btn.html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...`);
                fetch('{{ route('tagAsDoneTriagge') }}', {
                    method: 'POST',
                    headers: {
                        "Content-Type": "application/json",
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    body: JSON.stringify(params)
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.code == 500) {
                            toastr.error(data.message, "Ooops");
                            resetPaginationAndReload();
                            return;
                        }
                        const modal = getPatientModal();
                        if (modal) modal.hide();
                        toastr.success(data.message, "Information");
                        deleteChild("23/" + referral_data.key);
                        resetPaginationAndReload();
                    }).catch(err => {
                        toastr.error(err, "Ooops");
                    }).finally(() => {
                        $btn.prop("disabled", false);
                        $btn.html(`<i class="bi bi-check2-circle me-1"></i>TAG AS DONE`);
                    });
                }); // end Swal.then
            });

            $("#btn_queue").off("click").on("click", function () {
                if (!referral_data) {
                    toastr.error("No selected patient", "Error");
                    return;
                }
                if (window.setQueueDeptOptions) window.setQueueDeptOptions('Referral');
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
                    referring_hospital: (referral_data.referring_hospital || "TRIAGGE"),
                    patient:            (referral_data.patient || ""),
                    age:                (referral_data.age || ""),
                    gender:             (referral_data.sex || referral_data.gender || ""),
                    department:         department,
                    remarks:            "QUEUED FROM TRIAGGE",
                    diagnosis:          (referral_data.diagnosis     || ""),
                    chief_complaint:    (referral_data.chiefComplaint || "")
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

            $("#btn_send").off("click").on("click", function () {
                $("#forward_department").val("");
                $("#forward_remarks").val("");
                const modal = getForwardModal();
                if (modal) modal.show();
            });

            $("#btn_confirm_forward").off("click").on("click", function () {
                $(".is-invalid").removeClass("is-invalid");
                const remarks    = $("#forward_remarks").val().trim();
                const topic      = $("#forward_department").val();
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
                    firebase_key:       referral_data.key,
                    patient:            referral_data.patient,
                    gender:             referral_data.sex,
                    age:                referral_data.age,
                    diagnosis:          referral_data.diagnosis,
                    chief_complaint:    referral_data.chiefComplaint,
                    referring_hospital: referral_data.referring_hospital,
                    department:         department,
                    topic:              topic,
                    remarks:            remarks,
                    date_created:       referral_data.date_referred
                };

                $(this).prop("disabled", true);
                $(this).html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...`);
                fetch('{{ route('insertReferral') }}', {
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
                            const errFwd = getForwardModal();
                            if (errFwd) errFwd.hide();
                            resetPaginationAndReload();
                            return;
                        }
                        const forwardModal = getForwardModal();
                        if (forwardModal) forwardModal.hide();
                        const modal = getPatientModal();
                        if (modal) modal.hide();
                        toastr.success(data.message, "Information");
                        insert(params.topic, params.patient, function (message) {
                            deleteChild(params.topic);
                        });
                        deleteChild("23/" + referral_data.key);
                        const index = getPositionByKey(referral_data.key);
                        if (index > -1) {
                            resetPaginationAndReload();
                        }
                    }).catch(err => {
                        $("#btn_confirm_forward").prop("disabled", false);
                        $("#btn_confirm_forward").html("Confirm Forward");
                        toastr.error(err, "Ooops");
                    });
            });

            observe("23", function (snapshot) {
                if (!is_init || !snapshot.val()) return;
                toastr.success(snapshot.val().patient, "New referral");
                var msg = new SpeechSynthesisUtterance();
                msg.text = "New referral";
                window.speechSynthesis.speak(msg);
                resetPaginationAndReload();
            });

            observeRemoved("23", function () {
                if (!is_init) return;
                resetPaginationAndReload();
            });

            get("23", function (snapshots) {
                is_init = true;
                resetPaginationAndReload();
            });
        }

        $(document).ready(function () {
            bindEvents();
        });
    })();
</script>
