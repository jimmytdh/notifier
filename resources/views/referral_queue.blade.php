@extends('master')
@section('Title', 'Referral Queue')
@section('Subtitle', 'Monitor pending patients by specialty and keep throughput under control.')
@section('content')
    <div class="col-12">
        <div class="search-toolbar">
            <div class="toolbar-label">
                <h6 class="mb-1">Pending Queue</h6>
                <small>Filter by department and remove invalid queue entries.</small>
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
            <h5 class="card-title">Department Queue</h5>
            <div class="d-flex gap-2 mb-3 flex-wrap">
                <button class="btn btn-secondary btn-filter" data-filter="PEDIA">PEDIATRICS</button>
                <button class="btn btn-secondary btn-filter" data-filter="INTERNAL MEDICINE">INTERNAL MEDICINE</button>
                <button class="btn btn-secondary btn-filter" data-filter="SURGERY">SURGERY</button>
                <button class="btn btn-secondary btn-filter" data-filter="OB GYNE">OB GYNE</button>
                <button class="btn btn-secondary btn-filter" data-filter="EMERGENCY ROOM">EMERGENCY ROOM</button>
                <button class="btn btn-secondary btn-filter" data-filter="EMED">EMED</button>
            </div>
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
                    <th>Date Referred</th>
                    <th style="width: 4%"></th>
                </tr>
                </thead>
                <tbody id="referral_list"></tbody>
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
    @include('js.referral_queue')
@stop
