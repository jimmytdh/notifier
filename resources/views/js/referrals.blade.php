<script>
    let current_list = [];
    let is_init = false;
    let is_searching = false;
    let referral_id;
    let page = 0;
    const patient_modal = new bootstrap.Modal(document.getElementById('patient_modal'));
    $(document).ready(function () {
        $("#btn_done").addClass("d-none");
        $("#btn_send").addClass("d-none");
        $("#patient_modal .card-body").prop("disabled", true);
        $("#btn_search").click(function () {
            const department = $("#search_department").val().trim().toUpperCase();
        });

        getReferrals();
    });

    function getReferrals() {
        fetch('{{ route('getAllReferrals') }}', {
            method: "GET"
        })
            .then(res => res.json())
            .then(data => {
                $("#referral_list").empty();
                data.results.forEach(it => {
                    const data = it;
                    const tr = `<tr id="referral_id_` + data.id + `">
                                    <td>` + data.referring_hospital + `</td>
                                    <td>` + data.patient + `</td>
                                    <td>` + data.age + `</td>
                                    <td>` + data.gender + `</td>
                                    <td style="max-width: 100px;text-overflow:ellipsis;white-space: nowrap;overflow: hidden">` + data.chief_complaint + `</td>
                                    <td class="d-none">` + data.diagnosis + `</td>
                                    <td>` + data.department + `</td>
                                    <td>` + moment(data.date_referred).format("MM/DD/YYYY hh:mm:ss A") + `</td>
                                    <td>
                                        <button class='btn btn-sm' style="background: transparent" onClick='viewPatient("` + data.id + `")'>
                                            <i class="bi bi-eye-fill text-success"></i>
                                        </button>
                                    </td>
                                </tr>`;
                    $("#referral_list").append(tr);
                })
            }).catch(err => {
            console.log(err);
        });
    }

    function getLogs(id) {
        $("#activity_logs").empty();
        fetch('{{ route('loadLogs') }}?id=' + id, {
            method: "GET"
        })
            .then(res => res.json())
            .then(data => {
                data.results.forEach(it => {
                    const tr = `<tr>
                                    <td>` + it.origin + `</td>
                                    <td>` + it.remarks + `</td>
                                    <td>` + it.destination + `</td>
                                    <td>` + moment(it.date_forwarded).format("MM/DD/YYYY hh:mm:ss A") + `</td>
                               </tr>`;

                    $("#activity_logs").append(tr);
                });
            }).catch(err => {
            toastr.error(err, "Ooops");
        });
    }

    function viewPatient(id) {
        referral_id = id;
        const patient = $("#referral_id_" + id + " td:eq(1)").text();
        const age = $("#referral_id_" + id + " td:eq(2)").text();
        const sex = $("#referral_id_" + id + " td:eq(3)").text();
        const chief_complaint = $("#referral_id_" + id + " td:eq(4)").text();
        const diagnosis = $("#referral_id_" + id + " td:eq(5)").text();

        $("#patient").text(patient);
        $("#age").text(age + " years old");
        $("#sex").text(sex);
        $("#chief_complaint").text(chief_complaint);
        $("#diagnosis").text(diagnosis);

        patient_modal.toggle();
        getLogs(id);
    }

    function populateData(list) {
        for (let i = 0; i < list.length; i++) {
            const data = list[i];
            const tr = `<tr>
                                    <td>` + data.referring_hospital + `</td>
                                    <td>` + data.patient + `</td>
                                    <td>` + data.age + `</td>
                                    <td>` + data.sex + `</td>
                                    <td style="max-width: 100px;text-overflow:ellipsis;white-space: nowrap;overflow: hidden">` + data.chiefComplaint + `</td>
                                    <td>` + data.department + `</td>
                                    <td>` + moment(data.date_referred).format("MM/DD/YYYY hh:mm:ss A") + `</td>
                                    <td>
                                        <button class='btn btn-sm' style="background: transparent">
                                            <i class="bi bi-eye-fill text-success"></i>
                                        </button>
                                    </td>
                                </tr>`;
            $("#referral_list").append(tr);
        }
    }
</script>
