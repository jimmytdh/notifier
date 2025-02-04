@extends('master')
@section('content')
    <div class="d-flex justify-content-end mb-2">
        <div>
            <input class="form-control" placeholder="Search patient" id="search_patient"/>
        </div>
        <div class="ms-1">
            <button class="btn btn-success" id="btn_search">
                <i class="bi bi-search"></i>
            </button>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Queue</h5>
            <div class="d-flex gap-2 mb-3">
                <button class="btn btn-secondary btn-filter" data-filter="PEDIA">PEDIATRICS</button>
                <button class="btn btn-secondary btn-filter" data-filter="INTERNAL MEDICINE">INTERNAL MEDICINE</button>
                <button class="btn btn-secondary btn-filter" data-filter="SURGERY">SURGERY</button>
                <button class="btn btn-secondary btn-filter" data-filter="OB GYNE">OB GYNE</button>
                <button class="btn btn-secondary btn-filter" data-filter="EMERGENCY ROOM">EMERGENCY ROOM</button>
                <button class="btn btn-secondary btn-filter" data-filter="EMED">EMED</button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table text-center">
                <thead>
                <tr>
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
            <div class="d-flex align-items-center justify-content-center">
                <button class="btn btn-sm d-none" style="background: transparent !important;" id="btn_prev">
                    <i class="bi bi-caret-left"></i>
                </button>
                <div id="page_items_count"></div>
                <button class="btn btn-sm" style="background: transparent !important;" id="btn_next">
                    <i class="bi bi-caret-right"></i>
                </button>
            </div>
        </div>
    </div>

@stop
@section('js')
    @include('js.referral_queue')
@stop
