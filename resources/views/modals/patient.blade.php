<style>
    /* ── Patient Detail Modal ── */
    #patient_modal .modal-content {
        border: none;
        border-radius: 16px;
        overflow: hidden;
        background: #f7f9fb;
    }
    #patient_modal .modal-body {
        padding: 0;
    }

    /* Header */
    .pd-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 20px;
        background: #ffffff;
        border-bottom: 1px solid #c2c6d2;
    }
    .pd-modal-title {
        font-family: 'Manrope', sans-serif;
        font-size: 17px;
        font-weight: 700;
        color: #003166;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .pd-close-btn {
        background: transparent;
        border: none;
        color: #737782;
        font-size: 16px;
        line-height: 1;
        cursor: pointer;
        padding: 6px 8px;
        border-radius: 8px;
        transition: background .12s;
    }
    .pd-close-btn:hover { background: #eceef0; color: #191c1e; }

    /* Content area */
    .pd-content {
        padding: 16px;
    }

    /* Card base */
    .pd-card {
        background: #ffffff;
        border: 1px solid #c2c6d2;
        border-radius: 12px;
        padding: 16px;
        height: 100%;
        transition: border-color .15s;
    }
    .pd-card:hover { border-color: rgba(0,49,102,.25); }

    /* Section label */
    .pd-section-label {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .07em;
        text-transform: uppercase;
        color: #00478f;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    /* Patient profile */
    .pd-patient-body {
        display: flex;
        gap: 16px;
        align-items: flex-start;
    }
    .pd-avatar-lg {
        width: 110px;
        height: 110px;
        border-radius: 12px;
        object-fit: cover;
        border: 1px solid #c2c6d2;
        flex-shrink: 0;
        background: #f2f4f6;
    }
    .pd-patient-name {
        font-family: 'Manrope', sans-serif;
        font-size: 22px;
        font-weight: 700;
        color: #191c1e;
        text-transform: uppercase;
        margin: 0 0 6px;
        line-height: 1.3;
        word-break: break-word;
    }
    .pd-patient-meta {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        color: #424751;
        margin-bottom: 12px;
    }
    .pd-meta-dot {
        width: 5px; height: 5px;
        border-radius: 50%;
        background: #c2c6d2;
        display: inline-block;
    }

    /* Action bar (horizontal, full-width, bottom of modal) */
    .pd-action-bar {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .pd-action-bar .pd-btn-queue,
    .pd-action-bar .pd-btn-forward,
    .pd-action-bar .pd-btn-done {
        flex: 1 1 140px;
        padding: 11px 16px;
        border-radius: 10px;
        font-family: 'Manrope', sans-serif;
        font-size: 13px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        white-space: nowrap;
        transition: opacity .12s, transform .1s;
        cursor: pointer;
        border: none;
    }
    .pd-action-bar .pd-btn-forward {
        background: transparent !important;
        border: 2px solid #003166 !important;
        color: #003166 !important;
    }
    .pd-action-bar .pd-btn-forward:hover { background: #f2f4f6 !important; }
    .pd-action-bar .pd-btn-done {
        background: #003166 !important;
        color: #ffffff !important;
    }
    .pd-action-bar .pd-btn-done:hover { opacity: .88; }
    .pd-action-bar .pd-btn-queue {
        background: #29695b !important;
        color: #ffffff !important;
    }
    .pd-action-bar .pd-btn-queue:hover { opacity: .88; }
    .pd-action-bar .pd-btn-queue:active,
    .pd-action-bar .pd-btn-forward:active,
    .pd-action-bar .pd-btn-done:active { transform: scale(.97); }

    /* Info cards */
    .pd-icon-wrap {
        width: 38px; height: 38px;
        border-radius: 8px;
        background: #d6e3ff;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #003166;
        font-size: 17px;
        flex-shrink: 0;
        margin-bottom: 10px;
    }
    .pd-icon-wrap-teal {
        background: #c4f3fb;
        color: #005058;
    }
    .pd-info-label {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .07em;
        color: #424751;
        margin-bottom: 5px;
    }
    .pd-info-value {
        font-family: 'Manrope', sans-serif;
        font-size: 14px;
        font-weight: 600;
        color: #191c1e;
    }

    /* Ward / Bed fields */
    .pd-field-row {
        display: flex;
        gap: 8px;
    }
    .pd-field-wrap { flex: 1; }
    .pd-field-label {
        font-size: 11px;
        font-weight: 600;
        color: #424751;
        margin-bottom: 3px;
        display: block;
    }
    .pd-field-wrap .form-control {
        font-size: 13px;
        border-color: #c2c6d2;
        border-radius: 8px;
        height: 34px;
        padding: 4px 10px;
        box-shadow: none;
    }
    .pd-field-wrap .form-control:focus {
        border-color: #00478f;
        box-shadow: 0 0 0 3px rgba(0,71,143,.12);
    }

    /* Detail cards (diagnosis / complaint) */
    .pd-detail-card {
        position: relative;
        overflow: hidden;
    }
    .pd-accent {
        position: absolute;
        left: 0; top: 0; bottom: 0;
        width: 4px;
        border-radius: 4px 0 0 4px;
    }
    .pd-detail-card .pd-card { padding-left: 20px; }
    .pd-detail-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
    }
    .pd-detail-header .pd-icon-wrap { margin-bottom: 0; }
    .pd-detail-header .pd-info-label { margin-bottom: 0; }
    .pd-detail-text {
        font-size: 14px;
        color: #191c1e;
        line-height: 1.65;
        margin: 0;
        white-space: pre-wrap;
    }

    /* History section */
    .pd-history-head {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 12px;
        padding-bottom: 10px;
        border-bottom: 1px solid #eceef0;
    }
    .pd-history-title {
        font-family: 'Manrope', sans-serif;
        font-size: 14px;
        font-weight: 700;
        color: #003166;
        margin: 0;
    }
    .pd-history-sub {
        font-size: 11px;
        color: #424751;
        font-weight: 500;
    }
    .pd-table { margin: 0; font-size: 13px; }
    .pd-table thead th {
        background: #eceef0;
        color: #424751;
        font-family: 'Manrope', sans-serif;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
        border: none;
        padding: 9px 12px;
        white-space: nowrap;
    }
    .pd-table tbody td {
        border-color: #eceef0;
        padding: 10px 12px;
        vertical-align: middle;
        color: #191c1e;
        font-size: 13px;
    }
    .pd-table tbody tr:hover { background: #f2f4f6; }
    .pd-table tbody tr:last-child td { border-bottom: none; }

    /* Responsive */
    @media (max-width: 767px) {
        .pd-patient-body { flex-direction: column; align-items: center; text-align: center; }
        .pd-patient-meta { justify-content: center; }
        .pd-avatar-lg { width: 90px; height: 90px; }
        .pd-patient-name { font-size: 18px; }
    }
</style>

<div class="modal fade" id="patient_modal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-body">

                <!-- Modal Header -->
                <div class="pd-modal-header">
                    <h5 class="pd-modal-title">
                        <i class="bi bi-clipboard2-heart-fill"></i> Referral Detail
                    </h5>
                    <button type="button" class="pd-close-btn" data-bs-dismiss="modal" aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <!-- Content -->
                <div class="pd-content">
                    <div class="row g-3">

                        <!-- Patient Profile -->
                        <div class="col-12">
                            <div class="pd-card">
                                <div class="pd-section-label">
                                    <i class="bi bi-person-badge-fill"></i> Patient Profile
                                </div>
                                <div class="pd-patient-body">
                                    <img src="assets/img/logo.png" alt="Profile" class="pd-avatar-lg" id="employee_img">
                                    <div class="flex-fill">
                                        <p id="patient" class="pd-patient-name"></p>
                                        <div class="pd-patient-meta">
                                            <span id="sex"></span>
                                            <span class="pd-meta-dot"></span>
                                            <span id="age"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Date Referred -->
                        <div class="col-lg-4 col-md-6">
                            <div class="pd-card pd-info-card">
                                <div class="pd-icon-wrap">
                                    <i class="bi bi-calendar-date-fill"></i>
                                </div>
                                <div class="pd-info-label">Date Referred</div>
                                <div class="pd-info-value" id="date_referred">—</div>
                            </div>
                        </div>

                        <!-- Referring Hospital -->
                        <div class="col-lg-4 col-md-6">
                            <div class="pd-card pd-info-card">
                                <div class="pd-icon-wrap">
                                    <i class="bi bi-hospital-fill"></i>
                                </div>
                                <div class="pd-info-label">Referring Hospital</div>
                                <div class="pd-info-value" id="referring_hospital">—</div>
                            </div>
                        </div>

                        <!-- Ward / Bed -->
                        <div class="col-lg-4 col-md-12">
                            <div class="pd-card pd-info-card">
                                <div class="pd-icon-wrap">
                                    <i class="bi bi-clipboard2-pulse-fill"></i>
                                </div>
                                <div class="pd-info-label">Ward / Bed No.</div>
                                <div class="pd-field-row">
                                    <div class="pd-field-wrap">
                                        <label class="pd-field-label">Ward</label>
                                        <input type="text" class="form-control" id="ward_input" placeholder="—">
                                    </div>
                                    <div class="pd-field-wrap">
                                        <label class="pd-field-label">Bed No.</label>
                                        <input type="text" class="form-control" id="bed_no_input" placeholder="—">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Diagnosis -->
                        <div class="col-md-6">
                            <div class="pd-detail-card" style="height:100%">
                                <div class="pd-accent"></div>
                                <div class="pd-card" style="height:100%;padding-left:20px">
                                    <div class="pd-detail-header">
                                        <div class="pd-icon-wrap" style="margin-bottom:0">
                                            <i class="bi bi-clipboard2-pulse-fill"></i>
                                        </div>
                                        <div class="pd-info-label" style="margin-bottom:0">Diagnosis</div>
                                    </div>
                                    <p id="diagnosis" class="pd-detail-text"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Chief Complaint -->
                        <div class="col-md-6">
                            <div class="pd-detail-card" style="height:100%">
                                <div class="pd-accent"></div>
                                <div class="pd-card" style="height:100%;padding-left:20px">
                                    <div class="pd-detail-header">
                                        <div class="pd-icon-wrap pd-icon-wrap-teal" style="margin-bottom:0">
                                            <i class="bi bi-chat-text-fill"></i>
                                        </div>
                                        <div class="pd-info-label" style="margin-bottom:0">Chief Complaint</div>
                                    </div>
                                    <p id="chief_complaint" class="pd-detail-text"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Patient History (hidden on triagge, shown on other pages) -->
                        <div class="col-12" id="activity_container">
                            <div class="pd-card">
                                <div class="pd-history-head">
                                    <h6 class="pd-history-title">Tracking Logs</h6>
                                </div>
                                <div class="table-responsive">
                                    <table class="table text-center pd-table">
                                        <thead>
                                        <tr>
                                            <th>Origin</th>
                                            <th>Forwarded By</th>
                                            <th>Remarks</th>
                                            <th>Destination</th>
                                            <th>Tagged By</th>
                                            <th>Date Forwarded</th>
                                        </tr>
                                        </thead>
                                        <tbody id="activity_logs"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Action Bar -->
                        <div class="col-12 track-div">
                            <div class="pd-action-bar">
                                @if(session('code') === 'TRIAGGE')
                                <button class="btn pd-btn-queue" id="btn_queue">
                                    <i class="bi bi-list-check"></i> Queue Patient
                                </button>
                                @endif
                                <button class="btn pd-btn-forward" id="btn_send">
                                    <i class="bi bi-send-check-fill"></i> Forward Referral
                                </button>
                                <button class="btn pd-btn-done" id="btn_done">
                                    <i class="bi bi-check2-circle"></i> Mark as Done
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- /Content -->

            </div>
        </div>
    </div>
</div>

@if(session('code') === 'TRIAGGE')
<div class="modal fade" id="queue_modal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content patient-forward-shell">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title">Queue Patient</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-2">
                <div class="mb-0">
                    <label class="form-label fw-semibold">Department</label>
                    <select class="form-control dept-select-dynamic" id="queue_dept_select">
                        <option value="">Select department</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer border-0 pt-2">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" id="btn_confirm_queue">
                    <i class="bi bi-list-check me-1"></i> Confirm Queue
                </button>
            </div>
        </div>
    </div>
</div>
@endif

<div class="modal fade" id="forward_modal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content patient-forward-shell">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title">Forward Patient</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-2">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Department</label>
                    <select class="form-control dept-select-dynamic" id="forward_department">
                        <option value="">Select department</option>
                    </select>
                </div>
                <div class="mb-0">
                    <label class="form-label fw-semibold">Remarks</label>
                    <textarea class="form-control" id="forward_remarks" rows="4" placeholder="Type remarks"></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 pt-2">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="btn_confirm_forward">Confirm Forward</button>
            </div>
        </div>
    </div>
</div>
