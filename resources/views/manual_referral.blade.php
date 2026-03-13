@extends('master')
@section('Title', 'Manual Referral')
@section('Subtitle', 'Create a patient referral internally and send it to another department.')
@section('content')
    <div class="col-12">
        <div class="card premium-card">
            <div class="card-body">
                <h5 class="card-title">Manual Patient Intake</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Referring Department</label>
                        <input type="text" class="form-control" id="referring_department" value="{{ session('access_rights') }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Destination Department</label>
                        <select class="form-control" id="destination_department">
                            <option value="">Select department</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->code }}">{{ $department->department }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Patient Name</label>
                        <input type="text" class="form-control" id="patient_name" placeholder="Last name, First name">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Age</label>
                        <input type="number" class="form-control" id="patient_age" min="0" placeholder="0">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Gender</label>
                        <select class="form-control" id="patient_gender">
                            <option value="">Select gender</option>
                            <option value="Male">MALE</option>
                            <option value="Female">FEMALE</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Chief Complaint</label>
                        <textarea class="form-control" id="chief_complaint" rows="3" placeholder="Primary complaint"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Diagnosis</label>
                        <textarea class="form-control" id="diagnosis" rows="3" placeholder="Working diagnosis"></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Remarks</label>
                        <textarea class="form-control" id="remarks" rows="3" placeholder="Include urgency, notes, or instructions"></textarea>
                    </div>
                    <div class="col-12 d-flex gap-2">
                        <button class="btn btn-success" id="btn_submit_manual">
                            <i class="bi bi-send-check me-1"></i> Submit Manual Referral
                        </button>
                        <button class="btn btn-light" id="btn_reset_manual">Reset</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
    @include('js.manual_referral')
@stop
