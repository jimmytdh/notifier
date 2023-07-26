<script>
    let current_list = [];
    let is_init = false;
    let is_searching = false;
    let referral_id;
    let page = 0;
    const patient_modal = new bootstrap.Modal(document.getElementById('patient_modal'));
    $(document).ready(function () {
        console.log($("#user_code").text().trim());
        observe($("#user_code").text().trim(), function (snapshot) {
            console.log(snapshot.val());
            if (!is_init) return;
            toastr.success(snapshot.val(), "New referral");
            var msg = new SpeechSynthesisUtterance();
            msg.text = "New referral";
            window.speechSynthesis.speak(msg);

            getReferrals();
        })

        get($("#user_code").text().trim(), function () {
            is_init = true;
        });

        $("#patient_modal .card-body").prop("disabled", true);
        $("#btn_search").click(function () {
            page = 0;
            getReferrals();
        });

        $("#btn_next").click(function () {
            page++;
            getReferrals();
            $("#btn_prev").removeClass("d-none");
        });

        $("#btn_prev").click(function () {
            if (page == 0) return;
            if (page == 1) {
                $("#btn_prev").addClass("d-none");
            }
            page--;
            getReferrals();
        });

        $("#btn_done").click(function () {
            let remarks = $("#common_remarks").val().trim();
            if(remarks == "")
                remarks = "TAGGED AS DONE";

            if (confirm("Are you sure you want to tag this as done?")) {
                $(this).prop("disabled", true);
                $(this).html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...`);
                fetch('{{ route('tagAsDone') }}', {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    body: JSON.stringify({id: referral_id, remarks: remarks})
                })
                    .then(res => res.json())
                    .then(data => {
                        $("#btn_done").prop("disabled", false);
                        $("#btn_done").html("TAG AS DONE");
                        if (data.code == 500) {
                            toastr.error(data.message, "Ooops");
                            return;
                        }

                        patient_modal.toggle();
                        toastr.success(data.message, "Information");
                        getReferrals();
                    }).catch(err => {
                    console.log(err);
                })
            }
        });

        $("#btn_send").click(function () {
            $(".is-invalid").removeClass("is-invalid");
            const referring_hospital = $("#referral_id_" + referral_id + " td:eq(0)").text();
            const patient = $("#referral_id_" + referral_id + " td:eq(1)").text();
            const age = $("#referral_id_" + referral_id + " td:eq(2)").text();
            const gender = $("#referral_id_" + referral_id + " td:eq(3)").text();
            const remarks = $("#common_remarks").val().trim();
            const topic = $("#department").val();
            const department = $("#department option:selected").text();

            if (topic == "") {
                $("#department").addClass("is-invalid");
                return;
            }

            const params = {
                id: referral_id,
                patient: patient,
                gender: gender,
                age: age,
                diagnosis: "",
                referring_hospital: referring_hospital,
                department: department,
                topic: topic,
                remarks: remarks
            }

            $(this).prop("disabled", true);
            $(this).html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...`);
            fetch('{{ route('forwardReferral') }}', {
                method: 'POST',
                headers: {
                    "Content-Type": "application/json",
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                body: JSON.stringify(params)
            })
                .then(res => res.json())
                .then(data => {
                    $("#btn_send").prop("disabled", false);
                    $("#btn_send").html("FORWARD TO");
                    if (data.code == 500) {
                        toastr.error(data.message, "Ooops");
                        return;
                    }

                    patient_modal.toggle();
                    $("#common_remarks").val("");
                    $("#department").val("");
                    toastr.success(data.message, "Information");
                    insert(params.topic, params.patient, function (message) {
                        deleteChild(params.topic);
                    });
                    getReferrals();
                }).catch(err => {
                toastr.error(err, "Ooops");
            });
        });
        getReferrals();
    });

    function getReferrals() {
        const patient = $("#search_patient").val().trim().toUpperCase();
        $("#referral_list").empty();
        $("#referral_list").append("<tr><td colspan='7'>Loading data, please wait ...</td></tr>");
        fetch('{{ route('getReferrals') }}?search_patient=' + patient +
            "&page=" + page, {
            method: "GET"
        })
            .then(res => res.json())
            .then(data => {
                $("#pagination_container").addClass("d-none")
                $("#referral_list").empty();
                $("#btn_next").addClass("d-none");
                if (data.results.length < 1) {
                    $("#referral_list").append("<tr><td colspan='7'>No record found</td></tr>");
                    return;
                }

                $("#pagination_container").removeClass("d-none")

                let max_visible = data.results.length;
                if (max_visible == 11) {
                    max_visible = 10;
                    $("#btn_next").removeClass("d-none");
                }

                $("#page_items_count").text(((page * 10) + 1) + " - " + ((page * 10) + max_visible));
                for (let i = 0; i < max_visible; i++) {
                    const it = data.results[i];
                    const tr = `<tr id="referral_id_` + it.id + `">
                                    <td>` + it.referring_hospital + `</td>
                                    <td>` + it.patient + `</td>
                                    <td>` + it.age + `</td>
                                    <td>` + it.gender + `</td>
                                    <td class="d-none" style="max-width: 100px;text-overflow:ellipsis;white-space: nowrap;overflow: hidden">` + it.chief_complaint + `</td>
                                    <td class="d-none">` + it.diagnosis + `</td>
                                    <td>` + it.department + `</td>
                                    <td>` + moment(it.date_referred).format("MM/DD/YYYY hh:mm:ss A") + `</td>
                                    <td>
                                        <button class='btn btn-sm' style="background: transparent" onClick='viewPatient("` + it.id + `")'>
                                            <i class="bi bi-eye-fill text-success"></i>
                                        </button>
                                    </td>
                                </tr>`;
                    $("#referral_list").append(tr);
                }
                data.results.forEach(it => {

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
