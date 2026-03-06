@extends('master')
@section('Title', 'Triagge Intake')
@section('Subtitle', 'Validate incoming patient details and route to the right specialty quickly.')
@section('content')
    <div class="col-12">
        <div class="search-toolbar triagge-toolbar">
            <div class="toolbar-label">
                <h6 class="mb-1">Live Intake</h6>
                <small>Search by patient and department before forwarding.</small>
            </div>
            <div class="toolbar-controls">
                <input class="form-control" placeholder="Search patient" id="search_patient"/>
                <input type="text" class="form-control" placeholder="Search department" id="search_department"/>
                <button class="btn btn-success" id="btn_search">
                    <i class="bi bi-search me-1"></i> Search
                </button>
            </div>
        </div>
    </div>
    <div class="col-12">
    <div class="card premium-card">
        <div class="card-body">
            <h5 class="card-title">Incoming Referrals</h5>
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
        <div class="card-footer text-dark d-none" id="pagination_container">
            <div class="d-flex align-items-center justify-content-center premium-pagination">
                <button class="btn btn-sm btn-pager d-none" id="btn_prev">
                    <i class="bi bi-caret-left"></i>
                </button>
                <div id="page_items_count"></div>
                <button class="btn btn-sm btn-pager d-none" id="btn_next">
                    <i class="bi bi-caret-right"></i>
                </button>
            </div>
        </div>
    </div>
    </div>
    @include('modals.patient')
@stop
@section('js')
    @include('js.triagge')
@stop
