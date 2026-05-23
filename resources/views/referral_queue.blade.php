@extends('master')
@section('Title', 'Referral Queue')
@section('Subtitle', 'Monitor pending patients by specialty and keep throughput under control.')
@section('content')
<style>
    :root {
        --qc-primary:        #003166;
        --qc-primary-cont:   #00478f;
        --qc-secondary:      #29695b;
        --qc-secondary-cont: #acedda;
        --qc-surface-low:    #f2f4f6;
        --qc-surface-cont:   #eceef0;
        --qc-outline-var:    #c2c6d2;
        --qc-outline:        #737782;
        --qc-on-surface:     #191c1e;
        --qc-on-surface-var: #424751;
        --qc-error:          #ba1a1a;
        --qc-error-cont:     #ffdad6;
    }

    /* ── Page Header ── */
    .qc-page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }
    .qc-page-title  { font-family:'Manrope',sans-serif; font-size:22px; font-weight:700; color:var(--qc-primary); margin:0 0 2px; }
    .qc-page-sub    { font-size:13px; color:var(--qc-on-surface-var); margin:0; }
    .qc-header-right { display:flex; align-items:center; gap:8px; }
    .qc-search-wrap { position:relative; }
    .qc-search-wrap .bi-search { position:absolute; left:11px; top:50%; transform:translateY(-50%); font-size:13px; color:var(--qc-outline); pointer-events:none; }
    .qc-search-wrap input.form-control { padding-left:32px; border:1px solid var(--qc-outline-var); border-radius:9999px; background:var(--qc-surface-low); font-size:13px; height:36px; width:200px; box-shadow:none; transition:border-color .15s,background .15s; }
    .qc-search-wrap input.form-control:focus { border-color:var(--qc-primary-cont); background:#fff; box-shadow:0 0 0 3px rgba(0,71,143,.12); outline:none; }
    .qc-btn-search {
        background:var(--qc-primary); color:#fff; border:none; border-radius:8px;
        font-family:'Manrope',sans-serif; font-size:12px; font-weight:700;
        padding:8px 16px; height:36px; display:inline-flex; align-items:center; gap:5px;
        transition:opacity .15s,transform .1s;
    }
    .qc-btn-search:hover  { opacity:.88; }
    .qc-btn-search:active { transform:scale(.97); }
    .qc-btn-refresh {
        background:#fff; color:var(--qc-primary); border:1px solid var(--qc-outline-var);
        border-radius:8px; padding:8px 12px; height:36px; display:inline-flex; align-items:center;
        font-size:14px; transition:background .12s;
    }
    .qc-btn-refresh:hover { background:var(--qc-surface-low); }

    /* ── 4-Column Department Grid ── */
    .qc-dept-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }

    /* ── Department Card ── */
    .qc-dept-card {
        background: #fff;
        border: 1px solid var(--qc-outline-var);
        border-radius: 12px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        min-height: 280px;
    }

    /* Card header variants */
    .qc-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px;
        border-bottom: 1px solid var(--qc-outline-var);
        border-left: 4px solid transparent;
    }
    .qc-header-im      { background:#eff4ff; border-left-color:#003166; }
    .qc-header-pedia   { background:#f0fdf8; border-left-color:#29695b; }
    .qc-header-ob      { background:#ecfeff; border-left-color:#005058; }
    .qc-header-surgery { background:#fffbeb; border-left-color:#92400e; }

    .qc-dept-info { display:flex; align-items:center; gap:10px; }
    .qc-dept-icon {
        width:36px; height:36px;
        border-radius:8px;
        display:flex; align-items:center; justify-content:center;
        font-size:16px; flex-shrink:0;
    }
    .qc-icon-im      { background:#d6e3ff; color:#003166; }
    .qc-icon-pedia   { background:#acedda; color:#29695b; }
    .qc-icon-ob      { background:#93f1ff; color:#005058; }
    .qc-icon-surgery { background:#fde68a; color:#92400e; }

    .qc-dept-name { font-family:'Manrope',sans-serif; font-size:13px; font-weight:700; color:var(--qc-on-surface); margin:0 0 1px; }
    .qc-dept-code { font-size:10px; font-weight:700; letter-spacing:.06em; text-transform:uppercase; color:var(--qc-outline); margin:0; }

    /* Count badge */
    .qc-count-badge {
        font-family:'Manrope',sans-serif; font-size:13px; font-weight:700;
        min-width:30px; height:30px;
        border-radius:9999px; padding:0 8px;
        display:inline-flex; align-items:center; justify-content:center;
    }
    .qc-badge-im      { background:#d6e3ff; color:#003166; }
    .qc-badge-pedia   { background:#acedda; color:#29695b; }
    .qc-badge-ob      { background:#93f1ff; color:#005058; }
    .qc-badge-surgery { background:#fde68a; color:#92400e; }

    /* Card body — scrollable */
    .qc-card-body {
        flex: 1;
        overflow-y: auto;
        max-height: 380px;
    }
    .qc-card-body::-webkit-scrollbar { width:4px; }
    .qc-card-body::-webkit-scrollbar-track { background:transparent; }
    .qc-card-body::-webkit-scrollbar-thumb { background:var(--qc-outline-var); border-radius:2px; }

    /* Queue item row */
    .qc-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 14px;
        border-bottom: 1px solid var(--qc-surface-cont);
        transition: background .1s;
    }
    .qc-item:hover { background: #f7f9fb; }
    .qc-item:last-child { border-bottom: none; }

    /* Position number */
    .qc-pos {
        width: 22px; height: 22px;
        border-radius: 50%;
        background: var(--qc-surface-cont);
        color: var(--qc-on-surface-var);
        font-size: 10px; font-weight: 700;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }

    /* Patient info */
    .qc-info { flex:1; min-width:0; }
    .qc-name {
        font-family:'Manrope',sans-serif; font-size:12px; font-weight:700;
        color:var(--qc-on-surface); text-transform:uppercase;
        margin:0 0 3px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;
    }
    .qc-meta {
        display:flex; align-items:center; gap:4px; flex-wrap:wrap;
        font-size:11px; color:var(--qc-on-surface-var); margin-bottom:2px;
    }
    .qc-dot { width:3px; height:3px; border-radius:50%; background:var(--qc-outline-var); display:inline-block; }
    .qc-referrer {
        font-size:10px; color:var(--qc-outline);
        margin:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;
    }

    /* Action buttons */
    .qc-actions { display:flex; gap:4px; flex-shrink:0; }
    .qc-btn-confirm, .qc-btn-delete {
        width:28px; height:28px;
        border-radius:7px; border:1px solid var(--qc-outline-var);
        background:transparent; font-size:13px;
        display:flex; align-items:center; justify-content:center;
        cursor:pointer; transition:background .12s,border-color .12s;
        padding:0;
    }
    .qc-btn-confirm { color:var(--qc-secondary); }
    .qc-btn-confirm:hover { background:var(--qc-secondary-cont); border-color:var(--qc-secondary); }
    .qc-btn-delete  { color:var(--qc-error); }
    .qc-btn-delete:hover  { background:var(--qc-error-cont); border-color:var(--qc-error); }

    /* Empty & loading states */
    .qc-empty, .qc-loading {
        display:flex; flex-direction:column; align-items:center; justify-content:center;
        gap:8px; padding:36px 16px;
        color:var(--qc-outline); text-align:center;
    }
    .qc-loading { flex-direction:row; padding:24px; }
    .qc-empty .bi  { font-size:26px; opacity:.35; }
    .qc-empty p, .qc-loading span { margin:0; font-size:12px; }

    /* Clickable patient name */
    .qc-name-link { cursor:pointer; transition:color .12s; }
    .qc-name-link:hover { color:var(--qc-primary-cont); text-decoration:underline dotted; }

    /* ── Queue Detail Modal ── */
    #queue_detail_modal .modal-content { border:none; border-radius:16px; overflow:hidden; background:#f7f9fb; }
    #queue_detail_modal .modal-body    { padding:0; }
    .qdi-header {
        display:flex; align-items:center; justify-content:space-between;
        padding:14px 20px; background:#fff; border-bottom:1px solid var(--qc-outline-var);
    }
    .qdi-title {
        font-family:'Manrope',sans-serif; font-size:16px; font-weight:700;
        color:var(--qc-primary); margin:0; display:flex; align-items:center; gap:8px;
    }
    .qdi-close {
        background:transparent; border:none; color:var(--qc-outline);
        font-size:16px; padding:5px 8px; border-radius:8px; cursor:pointer; transition:background .12s;
    }
    .qdi-close:hover { background:var(--qc-surface-cont); color:var(--qc-on-surface); }
    .qdi-content { padding:16px; }
    .qdi-card {
        background:#fff; border:1px solid var(--qc-outline-var); border-radius:12px;
        padding:16px; height:100%; transition:border-color .15s;
    }
    .qdi-card:hover { border-color:rgba(0,49,102,.2); }
    .qdi-section-label {
        font-size:11px; font-weight:700; letter-spacing:.07em; text-transform:uppercase;
        color:var(--qc-primary-cont); margin-bottom:12px; display:flex; align-items:center; gap:5px;
    }
    .qdi-profile { display:flex; align-items:center; gap:14px; }
    .qdi-avatar {
        width:80px; height:80px; border-radius:10px; object-fit:cover;
        border:1px solid var(--qc-outline-var); flex-shrink:0; background:var(--qc-surface-low);
    }
    .qdi-name {
        font-family:'Manrope',sans-serif; font-size:18px; font-weight:700;
        color:var(--qc-on-surface); text-transform:uppercase; margin:0 0 5px;
        line-height:1.25; word-break:break-word;
    }
    .qdi-meta { display:flex; align-items:center; gap:7px; font-size:13px; color:var(--qc-on-surface-var); }
    .qdi-meta-dot { width:4px; height:4px; border-radius:50%; background:var(--qc-outline-var); display:inline-block; }
    .qdi-icon-wrap {
        width:34px; height:34px; border-radius:8px; background:#d6e3ff; color:var(--qc-primary);
        display:flex; align-items:center; justify-content:center; font-size:15px; margin-bottom:8px;
    }
    .qdi-info-label { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:var(--qc-on-surface-var); margin-bottom:4px; }
    .qdi-info-value { font-family:'Manrope',sans-serif; font-size:14px; font-weight:600; color:var(--qc-on-surface); margin:0; }
    .qdi-detail-wrap { position:relative; overflow:hidden; height:100%; }
    .qdi-accent { position:absolute; left:0; top:0; bottom:0; width:4px; border-radius:4px 0 0 4px; }
    .qdi-detail-card { height:100%; }
    .qdi-detail-header { display:flex; align-items:center; gap:8px; margin-bottom:8px; }
    .qdi-detail-header .qdi-icon-wrap { margin-bottom:0; }
    .qdi-detail-header .qdi-info-label { margin-bottom:0; }
    .qdi-detail-text { font-size:13px; color:var(--qc-on-surface); line-height:1.65; margin:0; white-space:pre-wrap; }
    @media(max-width:767px) {
        .qdi-profile { flex-direction:column; align-items:center; text-align:center; }
        .qdi-meta    { justify-content:center; }
    }

    /* Responsive */
    @media(max-width:767px) {
        .qc-dept-grid { grid-template-columns:1fr; }
        .qc-page-header { flex-direction:column; align-items:flex-start; }
        .qc-search-wrap input.form-control { width:100%; }
        .qdm-profile-body { flex-direction: column; align-items: center; text-align: center; }
        .qdm-patient-meta { justify-content: center; }
    }
</style>

<!-- Page Header -->
<div class="col-12">
    <div class="qc-page-header">
        <div>
            <h2 class="qc-page-title">Department Queue</h2>
            <p class="qc-page-sub">Monitor pending patients by specialty and keep throughput under control.</p>
        </div>
        <div class="qc-header-right">
            <button class="btn qc-btn-refresh" id="btn_refresh" title="Refresh all">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
        </div>
    </div>
</div>

<!-- 4 Department Cards -->
<div class="col-12">
    <div class="qc-dept-grid">

        <!-- Internal Medicine -->
        <div class="qc-dept-card">
            <div class="qc-card-header qc-header-im">
                <div class="qc-dept-info">
                    <div class="qc-dept-icon qc-icon-im">
                        <i class="bi bi-heart-pulse-fill"></i>
                    </div>
                    <div>
                        <p class="qc-dept-name">Internal Medicine</p>
                        <p class="qc-dept-code">IM</p>
                    </div>
                </div>
                <span class="qc-count-badge qc-badge-im" id="dept_count_IM">—</span>
            </div>
            <div class="qc-card-body" id="dept_list_IM"></div>
        </div>

        <!-- Pediatrics -->
        <div class="qc-dept-card">
            <div class="qc-card-header qc-header-pedia">
                <div class="qc-dept-info">
                    <div class="qc-dept-icon qc-icon-pedia">
                        <i class="bi bi-emoji-smile-fill"></i>
                    </div>
                    <div>
                        <p class="qc-dept-name">Pediatrics</p>
                        <p class="qc-dept-code">PEDIA</p>
                    </div>
                </div>
                <span class="qc-count-badge qc-badge-pedia" id="dept_count_PEDIA">—</span>
            </div>
            <div class="qc-card-body" id="dept_list_PEDIA"></div>
        </div>

        <!-- Obstetrics -->
        <div class="qc-dept-card">
            <div class="qc-card-header qc-header-ob">
                <div class="qc-dept-info">
                    <div class="qc-dept-icon qc-icon-ob">
                        <i class="bi bi-gender-female"></i>
                    </div>
                    <div>
                        <p class="qc-dept-name">Obstetrics</p>
                        <p class="qc-dept-code">OB</p>
                    </div>
                </div>
                <span class="qc-count-badge qc-badge-ob" id="dept_count_OB">—</span>
            </div>
            <div class="qc-card-body" id="dept_list_OB"></div>
        </div>

        <!-- Surgery -->
        <div class="qc-dept-card">
            <div class="qc-card-header qc-header-surgery">
                <div class="qc-dept-info">
                    <div class="qc-dept-icon qc-icon-surgery">
                        <i class="bi bi-bandaid-fill"></i>
                    </div>
                    <div>
                        <p class="qc-dept-name">Surgery</p>
                        <p class="qc-dept-code">SURGERY</p>
                    </div>
                </div>
                <span class="qc-count-badge qc-badge-surgery" id="dept_count_SURGERY">—</span>
            </div>
            <div class="qc-card-body" id="dept_list_SURGERY"></div>
        </div>

    </div>
</div>

<!-- Queue Patient Detail Modal (read-only) -->
<div class="modal fade" id="queue_detail_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-body">

                <div class="qdi-header">
                    <h5 class="qdi-title">
                        <i class="bi bi-clipboard2-heart-fill"></i> Patient Info
                    </h5>
                    <button type="button" class="qdi-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <div class="qdi-content">
                    <div class="row g-3">

                        <!-- Profile -->
                        <div class="col-12">
                            <div class="qdi-card">
                                <div class="qdi-section-label">
                                    <i class="bi bi-person-badge-fill"></i> Patient Profile
                                </div>
                                <div class="qdi-profile">
                                    <img id="qdi_avatar" class="qdi-avatar" src="assets/img/logo.png" alt="Avatar">
                                    <div class="flex-fill">
                                        <p class="qdi-name" id="qdi_patient"></p>
                                        <div class="qdi-meta">
                                            <span id="qdi_sex"></span>
                                            <span class="qdi-meta-dot"></span>
                                            <span id="qdi_age"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Referring Hospital -->
                        <div class="col-12">
                            <div class="qdi-card">
                                <div class="qdi-icon-wrap"><i class="bi bi-hospital-fill"></i></div>
                                <div class="qdi-info-label">Referring Hospital</div>
                                <p class="qdi-info-value" id="qdi_hospital"></p>
                            </div>
                        </div>

                        <!-- Diagnosis -->
                        <div class="col-md-6">
                            <div class="qdi-detail-wrap">
                                <div style="background:#003166"></div>
                                <div class="qdi-card" style="padding-left:20px">
                                    <div class="qdi-detail-header">
                                        <div class="qdi-icon-wrap"><i class="bi bi-clipboard2-pulse-fill"></i></div>
                                        <div class="qdi-info-label">Diagnosis</div>
                                    </div>
                                    <p class="qdi-detail-text" id="qdi_diagnosis">—</p>
                                </div>
                            </div>
                        </div>

                        <!-- Chief Complaint -->
                        <div class="col-md-6">
                            <div class="qdi-detail-wrap">
                                <div style="background:#58d7e8"></div>
                                <div class="qdi-card" style="padding-left:20px">
                                    <div class="qdi-detail-header">
                                        <div class="qdi-icon-wrap" style="background:#c4f3fb;color:#005058">
                                            <i class="bi bi-chat-text-fill"></i>
                                        </div>
                                        <div class="qdi-info-label">Chief Complaint</div>
                                    </div>
                                    <p class="qdi-detail-text" id="qdi_chief_complaint">—</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@stop
@section('js')
    @include('js.referral_queue')
@stop
