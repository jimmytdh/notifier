<script>
    (function () {

        const DEPTS = ['IM', 'PEDIA', 'OB', 'SURGERY'];
        let _store = {};

        /* ── Helpers ── */
        function getAvatarSrc(age, sex) {
            const n = parseInt(age) || 0;
            const g = (sex || '').toLowerCase().includes('f') ? 'female' : 'male';
            const a = n <= 12 ? '0_12' : n <= 59 ? '13_59' : '60_plus';
            return 'assets/img/avatar_' + a + '_' + g + '.png';
        }

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

        /* ── Load one department ── */
        function loadDept(deptCode) {
            const container = $('#dept_list_' + deptCode);
            const countEl   = $('#dept_count_' + deptCode);

            container.html(
                '<div class="qc-loading">' +
                '<div class="spinner-border spinner-border-sm text-secondary" role="status"></div>' +
                '<span>Loading&hellip;</span>' +
                '</div>'
            );

            fetch('{{ route('getQueue') }}?search_department=' + encodeURIComponent(deptCode), {
                method: 'GET'
            })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                container.empty();

                if (!data.results || data.results.length < 1) {
                    countEl.text('0');
                    container.html(
                        '<div class="qc-empty">' +
                        '<i class="bi bi-inbox"></i>' +
                        '<p>No pending patients</p>' +
                        '</div>'
                    );
                    return;
                }

                const max = Math.min(data.results.length, 10);
                countEl.text(data.results.length > 10 ? '10+' : max);

                for (var i = 0; i < max; i++) {
                    var it      = data.results[i];
                    _store[it.id] = it;
                    var timeAgo = getTimeAgo(it.created_at);
                    var sex     = it.gender || '';
                    var pos     = it.queue_position || (i + 1);
                    var hosp    = (it.referring_hospital || '-');
                    var name    = (it.patient || '').toUpperCase();
                    var age     = it.age || '-';
                    var remarks = it.remarks ? it.remarks : '';

                    var item =
                        '<div class="qc-item" id="queue_item_' + it.id + '">' +
                            '<span class="qc-pos">' + pos + '</span>' +
                            '<div class="qc-info">' +
                                '<p class="qc-name qc-name-link" onclick="viewQueueDetail(\'' + it.id + '\')">' + name + '</p>' +
                                '<div class="qc-meta">' +
                                    '<span>' + age + 'Y</span>' +
                                    '<span class="qc-dot"></span>' +
                                    '<span>' + sex + '</span>' +
                                    '<span class="qc-dot"></span>' +
                                    '<i class="bi bi-clock" style="font-size:10px"></i>' +
                                    '<span>' + timeAgo + '</span>' +
                                '</div>' +
                                '<p class="qc-referrer">' +
                                    '<i class="bi bi-hospital-fill" style="font-size:9px;margin-right:2px"></i>' +
                                    hosp +
                                    (remarks ? ' &middot; ' + remarks : '') +
                                '</p>' +
                            '</div>' +
                            @if(session('code') === 'TRIAGGE')
                            '<div class="qc-actions">' +
                                '<button class="qc-btn-confirm" title="Confirm received" onclick="confirmQueue(\'' + it.id + '\',\'' + deptCode + '\')">' +
                                    '<i class="bi bi-check-lg"></i>' +
                                '</button>' +
                                '<button class="qc-btn-delete" title="Delete" onclick="deleteQueue(\'' + it.id + '\',\'' + deptCode + '\')">' +
                                    '<i class="bi bi-trash3"></i>' +
                                '</button>' +
                            '</div>' +
                            @endif
                        '</div>';

                    container.append(item);
                }
            })
            .catch(function (err) {
                countEl.text('!');
                container.html(
                    '<div class="qc-empty">' +
                    '<i class="bi bi-exclamation-circle"></i>' +
                    '<p>' + (err.message || 'Failed to load') + '</p>' +
                    '</div>'
                );
            });
        }

        function loadAll() {
            _store = {};
            DEPTS.forEach(function (d) { loadDept(d); });
        }

        /* ── Confirm received ── */
        window.confirmQueue = function (id, deptCode) {
            Swal.fire({
                title: 'Confirm Patient Received?',
                text: 'Mark this patient as received by the department.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Confirm',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#29695b',
            }).then(function (result) {
                if (!result.isConfirmed) return;
                fetch('{{ route('confirmQueue') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    body: JSON.stringify({ id: id })
                })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (data.code !== 200) {
                        toastr.error(data.message || 'Failed to confirm', 'Error');
                        loadDept(deptCode);
                        return;
                    }
                    const tat = data.turnaround_minutes + 'm ' + data.turnaround_seconds + 's';
                    toastr.success(data.patient + ' confirmed. TAT: ' + tat, 'Confirmed');
                    loadDept(deptCode);
                })
                .catch(function (err) {
                    toastr.error(err.message || 'Something went wrong', 'Error');
                });
            });
        };

        /* ── Delete queue entry ── */
        window.deleteQueue = function (id, deptCode) {
            Swal.fire({
                title: 'Delete Queue Entry?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Delete',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#ba1a1a',
            }).then(function (result) {
                if (!result.isConfirmed) return;
                fetch('{{ route('deleteQueue') }}', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    body: JSON.stringify({ id: id })
                })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    toastr.success(data.message, 'Information');
                    loadDept(deptCode);
                })
                .catch(function (err) {
                    toastr.error(err, 'Ooops');
                });
            });
        };

        /* ── Queue detail modal (dedicated, read-only) ── */
        window.viewQueueDetail = function (id) {
            var it = _store[id];
            if (!it) {
                toastr.warning('Patient record not loaded yet, please try again.', 'Not Ready');
                return;
            }

            var sex = it.gender || '';

            $('#qdi_avatar').attr('src', getAvatarSrc(it.age, sex));
            $('#qdi_patient').text((it.patient || '').toUpperCase());
            $('#qdi_age').text((it.age || '-') + ' Years');
            $('#qdi_sex').text(sex);
            $('#qdi_hospital').text((it.referring_hospital || '-').toUpperCase());
            $('#qdi_diagnosis').text((it.diagnosis || '').trim() || '—');
            $('#qdi_chief_complaint').text((it.chief_complaint || '').trim() || '—');

            var el = document.getElementById('queue_detail_modal');
            if (el && window.bootstrap && window.bootstrap.Modal) {
                window.bootstrap.Modal.getOrCreateInstance(el).show();
            }
        };

        /* ── Add to queue (used by other parts of the app) ── */
        function bindAddQueue() {
            $('#spa-content').off('click', '#btn_add_queue').on('click', '#btn_add_queue', function () {
                $('.is-invalid').removeClass('is-invalid');

                const referring_hospital = $('#queue_referrer').val().trim();
                const patient            = $('#queue_patient').val().trim();
                const age                = $('#queue_age').val().trim();
                const gender             = $('#queue_gender').val().trim();
                const department         = $('#queue_department').val().trim();
                const remarks            = $('#queue_remarks').val().trim();

                let valid = true;
                if (!referring_hospital) { $('#queue_referrer').addClass('is-invalid'); valid = false; }
                if (!patient)            { $('#queue_patient').addClass('is-invalid');  valid = false; }
                if (!valid) { toastr.error('Referrer and patient are required', 'Validation'); return; }

                const btn = $(this);
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status"></span> Saving...');

                fetch('{{ route('insertQueue') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    body: JSON.stringify({ referring_hospital, patient, age, gender, department, remarks })
                })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (data.code === 500) { toastr.error(data.message || 'Failed to queue patient', 'Error'); return; }
                    toastr.success(data.message, 'Information');
                    $('#queue_referrer,#queue_patient,#queue_age,#queue_gender,#queue_department,#queue_remarks').val('');
                    if (department) { loadDept(department); } else { loadAll(); }
                })
                .catch(function (err) { toastr.error(err.message || 'Something went wrong', 'Error'); })
                .finally(function () {
                    btn.prop('disabled', false).html('<i class="bi bi-plus-circle me-1"></i> Add to Queue');
                });
            });
        }

        function bindEvents() {
            bindAddQueue();

            $('#spa-content').off('click', '#btn_search').on('click', '#btn_search', function () {
                loadAll();
            });

            $('#spa-content').off('click', '#btn_refresh').on('click', '#btn_refresh', function () {
                $('#search_patient').val('');
                loadAll();
            });

            $('#spa-content').off('keydown', '#search_patient').on('keydown', '#search_patient', function (e) {
                if (e.key === 'Enter') { e.preventDefault(); $('#btn_search').click(); }
            });

            loadAll();
        }

        $(document).ready(function () {
            bindEvents();
        });

    })();
</script>
