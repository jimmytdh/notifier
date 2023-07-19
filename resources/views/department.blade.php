@extends('master')
@section('content')
    <div class="d-flex justify-content-end mb-2">
        <div>
            <input class="form-control" placeholder="Search patient" id="search_patient"/>
        </div>
        <div class="ms-2 me-1">
            <input type="text" class="form-control" placeholder="Search department" id="search_department"/>
        </div>
        <div class="ms-1">
            <button class="btn btn-success" id="btn_search">
                <i class="bi bi-search"></i>
            </button>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Referrals</h5>
            <div class="table-responsive">
                <table class="table text-center">
                    <thead>
                    <tr>
                        <th>Referrer</th>
                        <th>Patient</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>Chief Complaint</th>
                        <th>Department</th>
                        <th>Date Referred</th>
                        <th style="width: 4%"></th>
                    </tr>
                    </thead>
                    <tbody id="referral_list"></tbody>
                </table>
            </div>
        </div>
    </div>
    @include('modals.patient')
@stop
@section('js')
    @include('js.department')
@stop
