<script>
    let current_list = [];
    let is_init = false;
    let is_searching = false;
    let referral_data;
    let page = 0;
    const patient_modal = new bootstrap.Modal(document.getElementById('patient_modal'));
    $(document).ready(function () {
        $("#referral_list").append(`<tr>
                                            <td colspan="8">
                                                <div class="text-center">Loading referrals, please wait ...</div>
                                            </td>
                                        </tr>`);

        $("#btn_search").click(function () {
            const patient = $("#search_patient").val().trim().toUpperCase();
            const department = $("#search_department").val().trim().toUpperCase();

            if (patient == "" && department == "") {
                $("#referral_list").empty();
                // populateData(current_list);
                is_searching = false;
            } else {
                is_searching = true;
            }

            const searched_data = [];
            for (let i = 0; i < current_list.length; i++) {
                if ((current_list[i].patient.toUpperCase()).includes(patient) && (current_list[i].department.toUpperCase()).includes(department)) {
                    searched_data.push(current_list[i]);
                    continue;
                }
            }

            $("#referral_list").empty();
            if (searched_data.length < 1) {
                $("#referral_list").append(`<tr>
                                            <td colspan="8">
                                                <div class="text-center">No record found</div>
                                            </td>
                                        </tr>`);
                return;
            }

            for (let i = 0; i < searched_data.length; i++) {
                const data = searched_data[i];
                const tr = `<tr>
                                    <td>` + data.referring_hospital + `</td>
                                    <td>` + data.patient + `</td>
                                    <td>` + data.age + `</td>
                                    <td>` + data.sex + `</td>
                                    <td style="max-width: 100px;text-overflow:ellipsis;white-space: nowrap;overflow: hidden">` + data.chiefComplaint + `</td>
                                    <td>` + data.department + `</td>
                                    <td>` + moment(data.date_referred).format("MM/DD/YYYY hh:mm:ss A") + `</td>
                                    <td>
                                        <button class='btn btn-sm' style="background: transparent" onClick='viewPatient("` + data.key + `")'>
                                            <i class="bi bi-eye-fill text-success"></i>
                                        </button>
                                    </td>
                                </tr>`;
                $("#referral_list").append(tr);
            }
        });

        observe("23", function (snapshot) {
            if (!is_init) {
                $("#referral_list").empty();
                is_init = true;
            }
            if (snapshot.val()) {
                const item = snapshot.val();
                item.key = snapshot.key;
                current_list.push(item);

                if (!is_searching) {
                    const data = snapshot.val();
                    const tr = `<tr>
                                    <td>` + data.referring_hospital + `</td>
                                    <td>` + data.patient + `</td>
                                    <td>` + data.age + `</td>
                                    <td>` + data.sex + `</td>
                                    <td style="max-width: 100px;text-overflow:ellipsis;white-space: nowrap;overflow: hidden">` + data.chiefComplaint + `</td>
                                    <td>` + data.department + `</td>
                                    <td>` + moment(data.date_referred).format("MM/DD/YYYY hh:mm:ss A") + `</td>
                                    <td>
                                        <button class='btn btn-sm' style="background: transparent" onclick='viewPatient("` + item.key + `")'>
                                            <i class="bi bi-eye-fill text-success"></i>
                                        </button>
                                    </td>
                                </tr>`;
                    $("#referral_list").append(tr);
                }
            }
        });

        $("#btn_send").click(function () {
            const remarks = $("#common_remarks").val().trim();
            const department = $("#department").val();
            const params = {
                firebase_key: referral_data.key,
                patient: referral_data.patient,
                gender: referral_data.sex,
                age: referral_data.age,
                diagnosis: referral_data.diagnosis,
                chief_complaint: referral_data.chiefComplaint,
                referring_hospital: referral_data.referring_hospital,
                department: department,
                remarks: remarks,
                date_created: referral_data.date_referred
            }

            fetch('{{ route('insertReferral') }}', {
                method: 'POST',
                headers: {
                    "Content-Type": "application/json",
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                body: JSON.stringify(params)
            })
                .then(res => res.json())
                .then(data => {
                    if (data.code == 500) {
                        toastr.error(data.message, "Ooops");
                        return;
                    }

                    toastr.success(data.message, "Information");
                    insert(params.department, {id: 1}, function (message) {
                        deleteChild(params.department);
                    });

                    deleteChild("23/" + referral_data.key);

                    const index = getPositionByKey(referral_data.key);
                    if (index > -1) {
                        current_list.splice(index, 1);
                        $("#btn_search").click();
                    }
                }).catch(err => {
                toastr.error(err, "Ooops");
            });
        });
    });

    function viewPatient(key) {
        referral_data = getDataByKey(key);
        if (referral_data) {
            $("#patient").text(referral_data.patient);
            $("#age").text(referral_data.age + " years old");
            $("#sex").text(referral_data.sex);
            $("#diagnosis").text(referral_data.diagnosis);
            $("#chief_complaint").text(referral_data.chiefComplaint);
            patient_modal.toggle();
        }
    }

    function getDataByKey(key) {
        for (let i = 0; i < current_list.length; i++) {
            if (current_list[i].key == key) {
                return current_list[i];
            }
        }
        return null;
    }

    function getPositionByKey(key) {
        for (let i = 0; i < current_list.length; i++) {
            if (current_list[i].key == key) {
                return i;
            }
        }
        return -1;
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
