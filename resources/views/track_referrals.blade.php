@extends('master')
@section('Title', 'Patient Timeline')
@section('Subtitle', 'Follow patient movement across departments with full audit visibility.')
@section('content')
    <div class="col-12">
        <div class="search-toolbar">
            <div class="toolbar-label">
                <h6 class="mb-1">Referral Audit Trail</h6>
                <small>Track the patient journey and verify latest status.</small>
            </div>
            <div class="toolbar-controls">
                <input class="form-control" placeholder="Search patient" id="search_patient"/>
                <button class="btn btn-success" id="btn_search">
                    <i class="bi bi-search me-1"></i> Search
                </button>
            </div>
        </div>
    </div>
    <div class="col-12">
    <div class="card premium-card">
        <div class="card-body">
            <h5 class="card-title">Tracked Referrals</h5>
            <div class="table-responsive">
                <table class="table text-center premium-table">
                    <thead>
                    <tr>
                        <th>Referrer</th>
                        <th>Patient</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>Department</th>
                        <th>Date Referred</th>
                        <th style="width: 4%"></th>
                    </tr>
                    </thead>
                    <tbody id="referral_list"></tbody>
                </table>
            </div>
        </div>
        <div class="card-footer text-dark" id="pagination_container">
            <div class="d-flex align-items-center justify-content-center premium-pagination">
                <button class="btn btn-sm btn-pager d-none" id="btn_prev">
                    <i class="bi bi-caret-left"></i>
                </button>
                <div id="page_items_count"></div>
                <button class="btn btn-sm btn-pager" id="btn_next">
                    <i class="bi bi-caret-right"></i>
                </button>
            </div>
        </div>
    </div>
    </div>
    @include('modals.patient')
@stop
@section('js')
    @include('js.track_referrals')
@stop
