<script>
    let current_list = [];
    let is_init = false;
    let is_searching = false;
    let referral_id;
    let page = 0;
    const patient_modal = new bootstrap.Modal(document.getElementById('patient_modal'));
    $(document).ready(function () {
        $(".track-div").addClass("d-none");
        $("#patient_modal .card-body").prop("disabled", true);
        $("#btn_search").click(function () {
            page = 0;
            getReferrals();
        });

        $("#search_patient").keydown(function (e) {
            if (e.key === "Enter") {
                e.preventDefault();
                $("#btn_search").click();
            }
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

        getReferrals();
    });

    function getReferrals() {
        const patient = $("#search_patient").val().trim().toUpperCase();
        $("#referral_list").empty();
        $("#referral_list").append("<tr><td colspan='7'>Loading data, please wait ...</td></tr>");
        fetch('{{ route('getAllReferrals') }}?search_patient=' + patient +
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
                    const department = (it.department) ? it.department : "";
                    const tr = `<tr id="referral_id_` + it.id + `">
                                    <td>` + it.referring_hospital + `</td>
                                    <td>` + it.patient + `</td>
                                    <td>` + it.age + `</td>
                                    <td>` + it.gender + `</td>
                                    <td class="d-none" style="max-width: 100px;text-overflow:ellipsis;white-space: nowrap;overflow: hidden">` + it.chief_complaint + `</td>
                                    <td class="d-none">` + it.diagnosis + `</td>
                                    <td>` + department + `</td>
                                    <td>` + moment(it.date_created).format("MM/DD/YYYY hh:mm:ss A") + `</td>
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
