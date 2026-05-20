@extends('master')
@section('Title', 'Queue Archives')
@section('Subtitle', 'All confirmed referral queue entries with turnaround time.')
@section('content')
    <div class="col-12">
        <div class="search-toolbar">
            <div class="toolbar-label">
                <h6 class="mb-1">Confirmed Queue</h6>
                <small>Patients already received by their department.</small>
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
            <h5 class="card-title">Archive by Department</h5>
            <div class="d-flex gap-2 mb-3 flex-wrap dept-filters-dynamic"></div>
        </div>
        <div class="table-responsive">
            <table class="table text-center premium-table">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Referrer</th>
                    <th>Patient</th>
                    <th>Age</th>
                    <th>Gender</th>
                    <th>Department</th>
                    <th>Remarks</th>
                    <th>Date Queued</th>
                    <th>Confirmed At</th>
                    <th>Turnaround Time</th>
                </tr>
                </thead>
                <tbody id="archive_list"></tbody>
            </table>
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
@stop
@section('js')
    @include('js.queue_archives')
@stop
