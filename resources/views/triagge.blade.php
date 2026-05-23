@extends('master')
@section('Title', 'Triagge Intake')
@section('Subtitle', 'Validate incoming patient details and route to the right specialty quickly.')
@section('content')
<style>
    /* ── Stitch Design Tokens ── */
    :root {
        --stitch-primary:        #003166;
        --stitch-primary-cont:   #00478f;
        --stitch-secondary:      #29695b;
        --stitch-secondary-cont: #acedda;
        --stitch-tertiary-fixed: #93f1ff;
        --stitch-surface-low:    #f2f4f6;
        --stitch-surface-cont:   #eceef0;
        --stitch-outline:        #737782;
        --stitch-outline-var:    #c2c6d2;
        --stitch-on-surface:     #191c1e;
        --stitch-on-surface-var: #424751;
        --stitch-error:          #ba1a1a;
        --stitch-error-cont:     #ffdad6;
    }

    /* ── Page Header ── */
    .tg-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 20px;
    }
    .tg-header-title {
        font-family: 'Manrope', sans-serif;
        font-size: 22px;
        font-weight: 700;
        color: var(--stitch-primary);
        margin: 0 0 2px;
        line-height: 1.25;
    }
    .tg-header-sub {
        font-size: 13px;
        color: var(--stitch-on-surface-var);
        margin: 0;
    }
    .tg-live-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--stitch-tertiary-fixed);
        color: #001f23;
        font-family: 'Manrope', sans-serif;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .05em;
        padding: 4px 12px;
        border-radius: 9999px;
        white-space: nowrap;
    }
    .tg-live-chip .tg-pulse {
        width: 7px; height: 7px;
        border-radius: 50%;
        background: var(--stitch-secondary);
        animation: tg-pulse 1.4s ease-in-out infinite;
    }
    @keyframes tg-pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50%       { opacity: .4; transform: scale(.7); }
    }

    /* ── Search Toolbar ── */
    .tg-toolbar {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #fff;
        border: 1px solid var(--stitch-outline-var);
        border-radius: 12px;
        padding: 12px 16px;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }
    .tg-toolbar-label {
        flex: 0 0 auto;
        font-family: 'Manrope', sans-serif;
        font-size: 12px;
        font-weight: 700;
        color: var(--stitch-on-surface-var);
        letter-spacing: .04em;
        text-transform: uppercase;
        white-space: nowrap;
    }
    .tg-search-group {
        display: flex;
        align-items: center;
        gap: 8px;
        flex: 1 1 auto;
        flex-wrap: wrap;
    }
    .tg-search-wrap {
        position: relative;
        flex: 1 1 180px;
    }
    .tg-search-wrap .bi {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 14px;
        color: var(--stitch-outline);
        pointer-events: none;
    }
    .tg-search-wrap input.form-control {
        padding-left: 34px;
        border: 1px solid var(--stitch-outline-var);
        border-radius: 9999px;
        background: var(--stitch-surface-low);
        font-size: 13px;
        color: var(--stitch-on-surface);
        height: 36px;
        box-shadow: none;
        transition: border-color .15s, background .15s;
    }
    .tg-search-wrap input.form-control:focus {
        border-color: var(--stitch-primary-cont);
        background: #fff;
        box-shadow: 0 0 0 3px rgba(0,71,143,.12);
        outline: none;
    }
    .tg-btn-search {
        flex: 0 0 auto;
        background: var(--stitch-primary);
        color: #fff;
        border: none;
        border-radius: 8px;
        font-family: 'Manrope', sans-serif;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .04em;
        padding: 8px 18px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: opacity .15s, transform .1s;
    }
    .tg-btn-search:hover  { opacity: .88; }
    .tg-btn-search:active { transform: scale(.97); }

    /* ── Outer Card Shell ── */
    .tg-shell {
        background: #fff;
        border: 1px solid var(--stitch-outline-var);
        border-radius: 12px;
        overflow: hidden;
    }
    .tg-shell-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 20px;
        border-bottom: 1px solid var(--stitch-outline-var);
        background: var(--stitch-surface-low);
    }
    .tg-shell-head-title {
        font-family: 'Manrope', sans-serif;
        font-size: 14px;
        font-weight: 700;
        color: var(--stitch-primary);
        margin: 0;
    }
    .tg-shell-badge {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .05em;
        text-transform: uppercase;
        background: var(--stitch-secondary-cont);
        color: var(--stitch-secondary);
        padding: 3px 10px;
        border-radius: 9999px;
    }

    /* ── Card Grid ── */
    .tg-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
        gap: 16px;
        padding: 16px;
    }

    /* ── Referral Card ── */
    .tg-ref-card {
        background: #fff;
        border: 1px solid var(--stitch-outline-var);
        border-radius: 12px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        position: relative;
        cursor: pointer;
        transition: border-color .15s, box-shadow .15s;
    }
    .tg-ref-card:hover {
        border-color: var(--stitch-primary-cont);
        box-shadow: 0 4px 16px rgba(0,49,102,.1);
    }
    /* left urgency accent */
    .tg-card-accent {
        position: absolute;
        left: 0; top: 0; bottom: 0;
        width: 4px;
        background: var(--stitch-error);
    }

    /* hospital strip */
    .tg-card-hosp {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 9px 12px 9px 16px;
        border-bottom: 1px solid var(--stitch-outline-var);
        background: rgba(242,244,246,.5);
        gap: 8px;
    }
    .tg-hosp-name {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: var(--stitch-on-surface-var);
        overflow: hidden;
    }
    .tg-hosp-name .bi { color: var(--stitch-primary); font-size: 12px; flex-shrink: 0; }
    .tg-hosp-name span {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        max-width: 175px;
    }
    .tg-new-badge {
        background: var(--stitch-secondary);
        color: #fff;
        font-size: 9px;
        font-weight: 700;
        letter-spacing: .04em;
        padding: 2px 7px;
        border-radius: 4px;
        flex-shrink: 0;
    }

    /* patient body */
    .tg-card-patient {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 12px 12px 16px;
    }
    .tg-avatar {
        width: 58px;
        height: 58px;
        border-radius: 10px;
        object-fit: cover;
        border: 1px solid var(--stitch-outline-var);
        flex-shrink: 0;
        background: var(--stitch-surface-low);
    }
    .tg-patient-name {
        font-family: 'Manrope', sans-serif;
        font-size: 13px;
        font-weight: 700;
        color: var(--stitch-on-surface);
        margin: 0 0 5px;
        line-height: 1.3;
        text-transform: uppercase;
        word-break: break-word;
    }
    .tg-patient-meta {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
        color: var(--stitch-on-surface-var);
    }
    .tg-meta-dot {
        width: 3px; height: 3px;
        border-radius: 50%;
        background: var(--stitch-outline-var);
        display: inline-block;
    }

    /* department tag & time row */
    .tg-card-tags {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 0 12px 12px 16px;
        flex-wrap: wrap;
    }
    .tg-dept-tag {
        font-size: 11px;
        font-weight: 700;
        padding: 3px 9px;
        border-radius: 9999px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .tg-dept-er {
        background: var(--stitch-error-cont);
        color: var(--stitch-error);
    }
    .tg-dept-er::before {
        content: '';
        display: inline-block;
        width: 6px; height: 6px;
        border-radius: 50%;
        background: var(--stitch-error);
        animation: tg-pulse 1.4s ease-in-out infinite;
    }
    .tg-dept-default {
        background: var(--stitch-surface-cont);
        color: var(--stitch-on-surface-var);
    }
    .tg-time {
        font-size: 11px;
        color: var(--stitch-on-surface-var);
        display: inline-flex;
        align-items: center;
        gap: 3px;
    }

    /* action buttons */
    .tg-card-actions {
        display: flex;
        margin-top: auto;
        border-top: 1px solid var(--stitch-outline-var);
    }
    .tg-btn-view,
    .tg-btn-queue-card {
        flex: 1;
        padding: 11px 6px;
        font-family: 'Manrope', sans-serif;
        font-size: 12px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        border: none;
        cursor: pointer;
        transition: background .12s;
    }
    .tg-btn-view {
        background: transparent;
        color: var(--stitch-primary);
        border-right: 1px solid var(--stitch-outline-var);
    }
    .tg-btn-view:hover { background: var(--stitch-surface-low); }
    .tg-btn-queue-card {
        background: var(--stitch-secondary);
        color: #fff;
    }
    .tg-btn-queue-card:hover { opacity: .88; }

    /* ── Empty / Loading states ── */
    .tg-state-row {
        grid-column: 1 / -1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 52px 24px;
        color: var(--stitch-on-surface-var);
        text-align: center;
    }
    .tg-state-row .bi { font-size: 32px; opacity: .35; }
    .tg-state-row p { margin: 0; font-size: 13px; }

    /* ── Pagination ── */
    .tg-pagination {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 12px 20px;
        border-top: 1px solid var(--stitch-surface-cont);
    }
    .tg-page-count {
        font-size: 12px;
        font-weight: 600;
        color: var(--stitch-on-surface-var);
        min-width: 60px;
        text-align: center;
    }
    .tg-btn-pager {
        border: 1px solid var(--stitch-outline-var);
        border-radius: 8px;
        background: #fff;
        color: var(--stitch-primary);
        font-size: 14px;
        padding: 5px 12px;
        transition: background .12s;
    }
    .tg-btn-pager:hover { background: var(--stitch-surface-low); }

    /* ── Responsive ── */
    @media (max-width: 767px) {
        .tg-toolbar      { flex-direction: column; align-items: stretch; }
        .tg-toolbar-label { display: none; }
        .tg-header        { flex-direction: column; align-items: flex-start; }
        .tg-grid          { grid-template-columns: 1fr; }
    }
</style>

<!-- Page Header -->
<div class="col-12">
    <div class="tg-header">
        <div>
            <h2 class="tg-header-title">Referrals</h2>
            <p class="tg-header-sub">Search by patient and department before forwarding to the right specialty.</p>
        </div>
        <div class="tg-live-chip">
            <span class="tg-pulse"></span> LIVE INTAKE
        </div>
    </div>
</div>

<!-- Search Toolbar -->
<div class="col-12">
    <div class="tg-toolbar">
        <span class="tg-toolbar-label">Filter</span>
        <div class="tg-search-group">
            <div class="tg-search-wrap">
                <i class="bi bi-person-fill"></i>
                <input class="form-control" placeholder="Search patient" id="search_patient"/>
            </div>
            <div class="tg-search-wrap">
                <i class="bi bi-grid-fill"></i>
                <input type="text" class="form-control" placeholder="Search department" id="search_department"/>
            </div>
        </div>
        <button class="btn tg-btn-search" id="btn_search">
            <i class="bi bi-search"></i> Search
        </button>
    </div>
</div>

<!-- Referral Card Grid -->
<div class="col-12">
    <div class="tg-shell">
        <div class="tg-shell-head">
            <h6 class="tg-shell-head-title">Incoming Referrals</h6>
            <span class="tg-shell-badge">Referral Queue</span>
        </div>
        <div id="referral_list" class="tg-grid"></div>
        <div class="tg-pagination d-none" id="pagination_container">
            <button class="btn btn-sm tg-btn-pager d-none" id="btn_prev">
                <i class="bi bi-caret-left"></i>
            </button>
            <div class="tg-page-count" id="page_items_count"></div>
            <button class="btn btn-sm tg-btn-pager d-none" id="btn_next">
                <i class="bi bi-caret-right"></i>
            </button>
        </div>
    </div>
</div>

@include('modals.patient')
@stop
@section('js')
    @include('js.triagge')
@stop
