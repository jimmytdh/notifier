@extends('master')
@section('Title', 'Manual Referral')
@section('Subtitle', 'Create a patient referral internally and send it to another department.')
@section('content')
<style>
    :root {
        --mr-primary:        #003166;
        --mr-primary-cont:   #00478f;
        --mr-secondary:      #29695b;
        --mr-sec-cont:       #acedda;
        --mr-surface-low:    #f2f4f6;
        --mr-surface-cont:   #eceef0;
        --mr-outline-var:    #c2c6d2;
        --mr-outline:        #737782;
        --mr-on-surface:     #191c1e;
        --mr-on-surface-var: #424751;
        --mr-error:          #ba1a1a;
        --mr-error-cont:     #ffdad6;
    }

    /* ── Page Header ── */
    .mr-header { display:flex; align-items:flex-end; justify-content:space-between; gap:12px; margin-bottom:20px; }
    .mr-header-title { font-family:'Manrope',sans-serif; font-size:22px; font-weight:700; color:var(--mr-primary); margin:0 0 2px; }
    .mr-header-sub   { font-size:13px; color:var(--mr-on-surface-var); margin:0; }

    /* ── Card ── */
    .mr-card { background:#fff; border:1px solid var(--mr-outline-var); border-radius:12px; overflow:hidden; }
    .mr-card-head {
        display:flex; align-items:center; justify-content:space-between;
        padding:14px 20px;
        border-bottom:1px solid var(--mr-outline-var);
        background:var(--mr-surface-low);
    }
    .mr-card-head-info {
        display:flex; align-items:center; gap:8px;
        font-family:'Manrope',sans-serif; font-size:14px; font-weight:700; color:var(--mr-primary);
    }
    .mr-card-badge {
        font-size:10px; font-weight:700; letter-spacing:.05em; text-transform:uppercase;
        background:var(--mr-sec-cont); color:var(--mr-secondary);
        padding:3px 10px; border-radius:9999px;
    }
    .mr-card-body { padding:24px; }

    /* ── Form Section ── */
    .mr-section { margin-bottom:28px; }
    .mr-section:last-of-type { margin-bottom:0; }
    .mr-section-label {
        display:flex; align-items:center; gap:7px;
        font-family:'Manrope',sans-serif; font-size:11px; font-weight:700;
        letter-spacing:.07em; text-transform:uppercase;
        color:var(--mr-primary-cont);
        margin-bottom:14px;
        padding-bottom:8px;
        border-bottom:1px solid var(--mr-surface-cont);
    }

    /* ── Form Controls ── */
    .mr-form .form-label {
        font-size:12px; font-weight:600; color:var(--mr-on-surface-var);
        margin-bottom:5px; display:flex; align-items:center; gap:5px;
    }
    .mr-form .form-control,
    .mr-form .form-select {
        border:1px solid var(--mr-outline-var);
        border-radius:10px;
        background:var(--mr-surface-low);
        font-size:13px;
        color:var(--mr-on-surface);
        padding:9px 14px;
        height:auto;
        box-shadow:none;
        transition:border-color .15s, background .15s, box-shadow .15s;
        font-family:'Inter',sans-serif;
    }
    .mr-form .form-control:focus,
    .mr-form .form-select:focus {
        border-color:var(--mr-primary-cont);
        background:#fff;
        box-shadow:0 0 0 3px rgba(0,71,143,.1);
        outline:none;
    }
    .mr-form .form-control[readonly] {
        background:var(--mr-surface-cont);
        color:var(--mr-on-surface-var);
        cursor:default;
        border-color:var(--mr-surface-cont);
    }
    .mr-form textarea.form-control { resize:vertical; min-height:88px; }
    .mr-form .form-control.is-invalid,
    .mr-form .form-select.is-invalid {
        border-color:var(--mr-error) !important;
        background:var(--mr-error-cont) !important;
        box-shadow:none !important;
    }
    .mr-required { color:var(--mr-error); font-size:11px; }
    .mr-optional  { color:var(--mr-outline); font-size:10px; font-weight:500; font-style:italic; }

    /* ── Action Bar ── */
    .mr-actions {
        display:flex; align-items:center; gap:10px;
        padding-top:20px;
        border-top:1px solid var(--mr-surface-cont);
        margin-top:24px;
        flex-wrap:wrap;
    }
    .mr-btn-submit {
        display:inline-flex; align-items:center; gap:7px;
        background:var(--mr-primary); color:#fff; border:none;
        font-family:'Manrope',sans-serif; font-size:13px; font-weight:700;
        padding:11px 24px; border-radius:10px; cursor:pointer;
        transition:opacity .15s, transform .1s;
    }
    .mr-btn-submit:hover  { opacity:.88; }
    .mr-btn-submit:active { transform:scale(.97); }
    .mr-btn-reset {
        display:inline-flex; align-items:center; gap:7px;
        background:transparent; color:var(--mr-on-surface-var);
        border:1px solid var(--mr-outline-var);
        font-family:'Manrope',sans-serif; font-size:13px; font-weight:600;
        padding:10px 20px; border-radius:10px; cursor:pointer;
        transition:background .12s, border-color .12s;
    }
    .mr-btn-reset:hover { background:var(--mr-surface-low); border-color:var(--mr-outline); }

    @media(max-width:767px) {
        .mr-header { flex-direction:column; align-items:flex-start; }
        .mr-actions { flex-direction:column; align-items:stretch; }
        .mr-btn-submit, .mr-btn-reset { justify-content:center; }
    }
</style>

<!-- Page Header -->
<div class="col-12">
    <div class="mr-header">
        <div>
            <h2 class="mr-header-title">Manual Patient Intake</h2>
            <p class="mr-header-sub">Create a referral and forward it directly to a receiving department.</p>
        </div>
    </div>
</div>

<!-- Form Card -->
<div class="col-12">
    <div class="mr-card">
        <div class="mr-card-head">
            <div class="mr-card-head-info">
                <i class="bi bi-clipboard2-plus-fill"></i> Referral Form
            </div>
            <span class="mr-card-badge">Manual Entry</span>
        </div>

        <div class="mr-card-body">
            <div class="mr-form">

                <!-- Route -->
                <div class="mr-section">
                    <div class="mr-section-label">
                        <i class="bi bi-arrow-left-right"></i> Route
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">From</label>
                            <input type="text" class="form-control" id="referring_department"
                                   value="{{ session('access_rights') }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">
                                To <span class="mr-required">*</span>
                            </label>
                            <select class="form-select" id="destination_department">
                                <option value="">Select department</option>
                                @foreach($departments as $department)
                                    @if($department->code !== session('access_rights'))
                                        <option value="{{ $department->code }}">{{ $department->department }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Patient Info -->
                <div class="mr-section">
                    <div class="mr-section-label">
                        <i class="bi bi-person-fill"></i> Patient Info
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">
                                Patient Name <span class="mr-required">*</span>
                            </label>
                            <input type="text" class="form-control" id="patient_name"
                                   placeholder="Last name, First name MI.">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">
                                Age <span class="mr-required">*</span>
                            </label>
                            <input type="number" class="form-control" id="patient_age" min="0" placeholder="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">
                                Gender <span class="mr-required">*</span>
                            </label>
                            <select class="form-select" id="patient_gender">
                                <option value="">Select gender</option>
                                <option value="Male">MALE</option>
                                <option value="Female">FEMALE</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">
                                Ward <span class="mr-optional">optional</span>
                            </label>
                            <input type="text" class="form-control" id="patient_ward" placeholder="Ward name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">
                                Bed No. <span class="mr-optional">optional</span>
                            </label>
                            <input type="text" class="form-control" id="patient_bed_no" placeholder="Bed number">
                        </div>
                    </div>
                </div>

                <!-- Clinical Details -->
                <div class="mr-section">
                    <div class="mr-section-label">
                        <i class="bi bi-clipboard2-pulse-fill"></i> Clinical Details
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">
                                Chief Complaint <span class="mr-optional">optional</span>
                            </label>
                            <textarea class="form-control" id="chief_complaint" rows="3"
                                      placeholder="Primary complaint"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">
                                Diagnosis <span class="mr-optional">optional</span>
                            </label>
                            <textarea class="form-control" id="diagnosis" rows="3"
                                      placeholder="Working diagnosis"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">
                                Remarks <span class="mr-required">*</span>
                            </label>
                            <textarea class="form-control" id="remarks" rows="3"
                                      placeholder="Include urgency, handover notes, or special instructions"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="mr-actions">
                    <button class="mr-btn-submit" id="btn_submit_manual">
                        <i class="bi bi-send-check-fill"></i> Submit Referral
                    </button>
                    <button class="mr-btn-reset" id="btn_reset_manual">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>

@stop
@section('js')
    @include('js.manual_referral')
@stop
