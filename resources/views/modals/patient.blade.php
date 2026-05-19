<div class="modal fade" id="patient_modal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content patient-modal-shell">
            <div class="modal-body patient-modal-body">
                <div class="patient-head">
                    <div class="patient-breadcrumb">Patient <span>&gt;</span> Details</div>
                    <button type="button" class="btn patient-modal-close" data-bs-dismiss="modal" aria-label="Close">X</button>
                </div>

                <div class="patient-summary">
                    <div class="patient-identity">
                        <img src="assets/img/logo.png" alt="Profile" class="rounded-circle" id="employee_img">
                        <h2 id="patient"></h2>
                    </div>
                    <div class="patient-facts">
                        <div class="fact-item">
                            <label>Sex</label>
                            <p id="sex"></p>
                        </div>
                        <div class="fact-item">
                            <label>Age</label>
                            <p id="age"></p>
                        </div>
                        <div class="fact-item">
                            <label>Ward</label>
                            <input type="text" class="form-control form-control-sm" id="ward_input" placeholder="-">
                        </div>
                        <div class="fact-item">
                            <label>Bed No.</label>
                            <input type="text" class="form-control form-control-sm" id="bed_no_input" placeholder="-">
                        </div>
                    </div>
                </div>

                <div class="patient-clinical">
                    <div class="clinical-card">
                        <label>Diagnosis</label>
                        <p id="diagnosis" style="white-space: pre-wrap;"></p>
                    </div>
                    <div class="clinical-card">
                        <label>Chief Complaint</label>
                        <p id="chief_complaint" style="white-space: pre-wrap;"></p>
                    </div>
                </div>

                <div class="patient-action-wrap track-div">
                    @if(session('code') === 'TRIAGGE')
                    <button class="btn btn-warning" id="btn_queue"><i class="bi bi-list-check me-1"></i>QUEUE PATIENT</button>
                    @endif
                    <button class="btn btn-primary" id="btn_done"><i class="bi bi-check2-circle me-1"></i>TAG AS DONE</button>
                    <button class="btn btn-success" id="btn_send"><i class="bi bi-send-check me-1"></i>FORWARD TO</button>
                </div>

                <div id="activity_container" class="patient-history">
                    <div class="patient-history-head">
                        <h5>Patient History</h5>
                        <span>Tracking Logs</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table text-center premium-table">
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
                    <select class="form-control" id="queue_dept_select">
                        <option value="">Select department</option>
                        <option value="PEDIA">PEDIATRICS</option>
                        <option value="INTERNAL MEDICINE">INTERNAL MEDICINE</option>
                        <option value="SURGERY">SURGERY</option>
                        <option value="OB GYNE">OB GYNE</option>
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
                    <select class="form-control" id="forward_department">
                        <option value="">Select department</option>
                        <option value="GS">GENERAL SURGERY</option>
                        <option value="IM">INTERNAL MEDICINE</option>
                        <option value="ER">EMERGENCY ROOM</option>
                        <option value="SURGERY">SURGERY</option>
                        <option value="OB">OB GYNE</option>
                        <option value="PEDIA">PEDIATRICS</option>
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
