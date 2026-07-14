@extends('master')
@section('Title', 'Department Referrals')
@section('Subtitle', 'Receive and process incoming patient referrals with clear next actions.')
@section('content')
<style>
    :root {
        --st-primary:        #003166;
        --st-primary-cont:   #00478f;
        --st-secondary:      #29695b;
        --st-secondary-cont: #acedda;
        --st-tertiary-fixed: #93f1ff;
        --st-surface-low:    #f2f4f6;
        --st-surface-cont:   #eceef0;
        --st-outline:        #737782;
        --st-outline-var:    #c2c6d2;
        --st-on-surface:     #191c1e;
        --st-on-surface-var: #424751;
    }
    .st-header { display:flex; align-items:flex-end; justify-content:space-between; gap:12px; margin-bottom:20px; }
    .st-header-title { font-family:'Manrope',sans-serif; font-size:22px; font-weight:700; color:var(--st-primary); margin:0 0 2px; line-height:1.25; }
    .st-header-sub   { font-size:13px; color:var(--st-on-surface-var); margin:0; }
    .st-chip {
        display:inline-flex; align-items:center; gap:6px;
        background:var(--st-secondary-cont); color:var(--st-secondary);
        font-family:'Manrope',sans-serif; font-size:11px; font-weight:700;
        letter-spacing:.05em; padding:4px 12px; border-radius:9999px; white-space:nowrap;
    }
    /* Toolbar */
    .st-toolbar { display:flex; align-items:center; gap:10px; background:#fff; border:1px solid var(--st-outline-var); border-radius:12px; padding:12px 16px; margin-bottom:16px; flex-wrap:wrap; }
    .st-toolbar-label { flex:0 0 auto; font-family:'Manrope',sans-serif; font-size:12px; font-weight:700; color:var(--st-on-surface-var); letter-spacing:.04em; text-transform:uppercase; white-space:nowrap; }
    .st-search-wrap { position:relative; flex:1 1 180px; }
    .st-search-wrap .bi { position:absolute; left:12px; top:50%; transform:translateY(-50%); font-size:14px; color:var(--st-outline); pointer-events:none; }
    .st-search-wrap input.form-control { padding-left:34px; border:1px solid var(--st-outline-var); border-radius:9999px; background:var(--st-surface-low); font-size:13px; height:36px; box-shadow:none; transition:border-color .15s,background .15s; }
    .st-search-wrap input.form-control:focus { border-color:var(--st-primary-cont); background:#fff; box-shadow:0 0 0 3px rgba(0,71,143,.12); outline:none; }
    .st-btn-search { flex:0 0 auto; background:var(--st-primary); color:#fff; border:none; border-radius:8px; font-family:'Manrope',sans-serif; font-size:12px; font-weight:700; letter-spacing:.04em; padding:8px 18px; height:36px; display:inline-flex; align-items:center; gap:6px; transition:opacity .15s,transform .1s; }
    .st-btn-search:hover  { opacity:.88; }
    .st-btn-search:active { transform:scale(.97); }
    /* Shell */
    .st-shell { background:#fff; border:1px solid var(--st-outline-var); border-radius:12px; overflow:hidden; }
    .st-shell-head { display:flex; align-items:center; justify-content:space-between; padding:14px 20px; border-bottom:1px solid var(--st-outline-var); background:var(--st-surface-low); }
    .st-shell-title { font-family:'Manrope',sans-serif; font-size:14px; font-weight:700; color:var(--st-primary); margin:0; }
    .st-shell-badge { font-size:10px; font-weight:700; letter-spacing:.05em; text-transform:uppercase; background:var(--st-secondary-cont); color:var(--st-secondary); padding:3px 10px; border-radius:9999px; }
    /* Pagination */
    .st-pagination { display:flex; align-items:center; justify-content:center; gap:10px; padding:12px 20px; border-top:1px solid var(--st-surface-cont); }
    .st-page-count { font-size:12px; font-weight:600; color:var(--st-on-surface-var); min-width:60px; text-align:center; }
    .st-btn-pager { border:1px solid var(--st-outline-var); border-radius:8px; background:#fff; color:var(--st-primary); font-size:14px; padding:5px 12px; transition:background .12s; }
    .st-btn-pager:hover { background:var(--st-surface-low); }
    /* Grid */
    .st-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(270px,1fr)); gap:16px; padding:16px; }
    /* Card */
    .st-ref-card { background:#fff; border:1px solid var(--st-outline-var); border-radius:12px; overflow:hidden; display:flex; flex-direction:column; position:relative; cursor:pointer; transition:border-color .15s,box-shadow .15s; }
    .st-ref-card:hover { border-color:var(--st-primary-cont); box-shadow:0 4px 16px rgba(0,49,102,.1); }
    .st-card-accent { position:absolute; left:0;top:0;bottom:0; width:4px; background:#ba1a1a; }
    .st-card-hosp { display:flex; align-items:center; justify-content:space-between; padding:9px 12px 9px 16px; border-bottom:1px solid var(--st-outline-var); background:rgba(242,244,246,.5); gap:8px; }
    .st-hosp-name { display:flex; align-items:center; gap:5px; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--st-on-surface-var); overflow:hidden; }
    .st-hosp-name .bi { color:var(--st-primary); font-size:12px; flex-shrink:0; }
    .st-hosp-name span { overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width:175px; }
    .st-type-badge { font-size:9px; font-weight:700; letter-spacing:.04em; padding:2px 7px; border-radius:4px; flex-shrink:0; }
    .st-type-referral { background:var(--st-primary); color:#fff; }
    .st-type-manual { background:#b45309; color:#fff; }
    .st-card-patient { display:flex; align-items:center; gap:12px; padding:12px 12px 12px 16px; }
    .st-avatar { width:58px; height:58px; border-radius:10px; object-fit:cover; border:1px solid var(--st-outline-var); flex-shrink:0; background:var(--st-surface-low); }
    .st-patient-name { font-family:'Manrope',sans-serif; font-size:13px; font-weight:700; color:var(--st-on-surface); margin:0 0 5px; line-height:1.3; text-transform:uppercase; word-break:break-word; }
    .st-patient-meta { display:flex; align-items:center; gap:5px; font-size:12px; color:var(--st-on-surface-var); }
    .st-meta-dot { width:3px;height:3px; border-radius:50%; background:var(--st-outline-var); display:inline-block; }
    .st-card-tags { display:flex; align-items:center; gap:8px; padding:0 12px 12px 16px; flex-wrap:wrap; }
    .st-dept-tag { font-size:11px; font-weight:700; padding:3px 9px; border-radius:9999px; display:inline-flex; align-items:center; gap:4px; }
    .st-dept-er { background:#ffdad6; color:#ba1a1a; }
    .st-dept-er::before { content:''; display:inline-block; width:6px;height:6px; border-radius:50%; background:#ba1a1a; animation:st-pulse 1.4s ease-in-out infinite; }
    .st-dept-default { background:var(--st-surface-cont); color:var(--st-on-surface-var); }
    @keyframes st-pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.4;transform:scale(.7)} }
    .st-time { font-size:11px; color:var(--st-on-surface-var); display:inline-flex; align-items:center; gap:3px; }
    .st-card-actions { display:flex; margin-top:auto; border-top:1px solid var(--st-outline-var); }
    .st-btn-view { flex:1; padding:11px 6px; font-family:'Manrope',sans-serif; font-size:12px; font-weight:700; display:inline-flex; align-items:center; justify-content:center; gap:5px; border:none; background:transparent; color:var(--st-primary); cursor:pointer; transition:background .12s; }
    .st-btn-view:hover { background:var(--st-surface-low); }
    /* State rows */
    .st-state-row { grid-column:1/-1; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:10px; padding:52px 24px; color:var(--st-on-surface-var); text-align:center; }
    .st-state-row .bi { font-size:32px; opacity:.35; }
    .st-state-row p { margin:0; font-size:13px; }
    @media(max-width:767px){
        .st-grid{ grid-template-columns:1fr; }
        .st-toolbar { flex-direction:column; align-items:stretch; }
        .st-toolbar-label { display:none; }
        .st-header { flex-direction:column; align-items:flex-start; }
    }
</style>

<!-- Page Header -->
<div class="col-12">
    <div class="st-header">
        <div>
            <h2 class="st-header-title">Active Referrals</h2>
            <p class="st-header-sub">Search by patient name and open records quickly.</p>
        </div>
        <div class="st-chip"><i class="bi bi-activity"></i> Department Queue</div>
    </div>
</div>

<!-- Search Toolbar -->
<div class="col-12">
    <div class="st-toolbar">
        <span class="st-toolbar-label">Filter</span>
        <div class="st-search-wrap">
            <i class="bi bi-person-fill"></i>
            <input class="form-control" placeholder="Search patient" id="search_patient"/>
        </div>
        <button class="btn st-btn-search" id="btn_search">
            <i class="bi bi-search"></i> Search
        </button>
    </div>
</div>

<!-- Card Grid -->
<div class="col-12">
    <div class="st-shell">
        <div class="st-shell-head">
            <h6 class="st-shell-title">Incoming Referrals</h6>
            <span class="st-shell-badge">Referral List</span>
        </div>
        <div id="referral_list" class="st-grid"></div>
        <div class="st-pagination d-none" id="pagination_container">
            <button class="btn btn-sm st-btn-pager d-none" id="btn_prev">
                <i class="bi bi-caret-left"></i>
            </button>
            <div class="st-page-count" id="page_items_count"></div>
            <button class="btn btn-sm st-btn-pager" id="btn_next">
                <i class="bi bi-caret-right"></i>
            </button>
        </div>
    </div>
</div>

@include('modals.patient')
@stop
@section('js')
    @include('js.referrals')
@stop
