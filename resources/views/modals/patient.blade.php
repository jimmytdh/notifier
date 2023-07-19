<div class="modal fade" id="patient_modal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-body m-0 p-0" style="background: rgb(246,249,255)">
                <section class="section profile">
                    <div class="card">
                        <div class="card-body m-0 p-0">
                            <div class="d-flex justify-content-end" style="font-size: 32px !important;">
                                <button type="button" class="btn" style="background: transparent"
                                        data-bs-dismiss="modal" aria-label="Close">X
                                </button>
                            </div>
                            <div class="d-flex flex-column align-items-center">
                                <img src="assets/img/logo.png" alt="Profile" class="rounded-circle" id="employee_img" style="height: 70px; width:70px">
                                <h2 id="patient" class="fw-bold mt-2" style="font-size: 24px"></h2>
                                <h3 id="age" class="mb-0" style="font-size: 16px"></h3>
                                <h3 id="sex" style="font-size: 16px"></h3>
                            </div>
                        </div>
                    </div>

                    <div class="ps-2 pe-2" style="background: rgb(246,249,255)">
                        <div class="card">
                            <div class="card-body">
                                <div>
                                    <h6 class="card-title pb-0">Diagnosis</h6>
                                    <h4 id="diagnosis" style="font-size: 15px"></h4>
                                </div>
                                <div>
                                    <h6 class="card-title pb-0">Chief Complaint</h6>
                                    <h4 id="chief_complaint" style="font-size: 15px"></h4>
                                </div>
                                <div class="track-div">
                                    <h6 class="card-title pb-0">Remarks</h6>
                                    <textarea class="form-control" style="height: 100px" id="common_remarks"></textarea>
                                </div>
                                <div class="mt-3 track-div">
                                    <h6 class="card-title">Department</h6>
                                    <select class="form-control" id="department">
                                        <option value="">Select department</option>
                                        <option value="GS">GENERAL SURGERY</option>
                                        <option value="IM">INTERNAL MEDICINE</option>
                                        <option value="ER">EMERGENCY ROOM</option>
                                        <option value="SURGERY">SURGERY</option>
                                        <option value="OB">OB GYNE</option>
                                        <option value="PEDIA">PEDIATRICS</option>
                                    </select>
                                </div>
                                <div class="text-end mt-4 track-div">
                                    <button class="btn btn-primary ms-1 me-1" id="btn_done">TAG AS
                                        DONE
                                    </button>
                                    <button class="btn btn-success ms-1 me-1" id="btn_send">FORWARD TO
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ps-2 pe-2" style="background: rgb(246,249,255)" id="activity_container">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Activity Logs</h5>
                                <div class="table-responsive">
                                    <table class="table text-center">
                                        <thead>
                                        <tr>
                                            <th>Origin</th>
                                            <th>Remarks</th>
                                            <th>Destination</th>
                                            <th>Date Forwarded</th>
                                        </tr>
                                        </thead>
                                        <tbody id="activity_logs" style="font-size: 12px"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>
