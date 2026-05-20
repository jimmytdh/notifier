<script>
    (function () {
        let current_list = [];
        let is_init = false;
        let is_searching = false;
        let referral_id;
        let page = 0;

        function bindEvents() {
            $("#spa-content").off("click", "#btn_add_queue").on("click", "#btn_add_queue", function () {
                $(".is-invalid").removeClass("is-invalid");

                const referring_hospital = $("#queue_referrer").val().trim();
                const patient = $("#queue_patient").val().trim();
                const age = $("#queue_age").val().trim();
                const gender = $("#queue_gender").val().trim();
                const department = $("#queue_department").val().trim();
                const remarks = $("#queue_remarks").val().trim();
                let is_valid = true;

                if (referring_hospital === "") {
                    $("#queue_referrer").addClass("is-invalid");
                    is_valid = false;
                }

                if (patient === "") {
                    $("#queue_patient").addClass("is-invalid");
                    is_valid = false;
                }

                if (!is_valid) {
                    toastr.error("Referrer and patient are required", "Validation");
                    return;
                }

                const params = {
                    referring_hospital: referring_hospital,
                    patient: patient,
                    age: age,
                    gender: gender,
                    department: department,
                    remarks: remarks
                };

                const btn = $(this);
                btn.prop("disabled", true);
                btn.html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...`);

                fetch('{{ route('insertQueue') }}', {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    body: JSON.stringify(params)
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.code === 500) {
                            toastr.error(data.message || "Failed to queue patient", "Error");
                            return;
                        }

                        toastr.success(data.message, "Information");
                        $("#queue_referrer").val("");
                        $("#queue_patient").val("");
                        $("#queue_age").val("");
                        $("#queue_gender").val("");
                        $("#queue_department").val("");
                        $("#queue_remarks").val("");
                        page = 0;
                        $("#btn_prev").addClass("d-none");
                        getReferrals();
                    })
                    .catch(err => {
                        toastr.error(err.message || "Something went wrong", "Error");
                    })
                    .finally(() => {
                        btn.prop("disabled", false);
                        btn.html(`<i class="bi bi-plus-circle me-1"></i> Add to Queue`);
                    });
            });

            $("#spa-content").off("click", ".btn-filter").on("click", ".btn-filter", function () {
                if ($(this).hasClass("btn-success")) {
                    $(this).removeClass("btn-success");
                    $(this).addClass("btn-secondary");
                } else {
                    $(".btn-filter").removeClass("btn-success");
                    $(".btn-filter").addClass("btn-secondary");
                    $(this).removeClass("btn-secondary");
                    $(this).addClass("btn-success");
                }

                page = 0;
                $("#btn_prev").addClass("d-none");
                getReferrals();
            });

            $("#spa-content").off("click", "#btn_search").on("click", "#btn_search", function () {
                page = 0;
                getReferrals();
            });

            $("#spa-content").off("keydown", "#search_patient").on("keydown", "#search_patient", function (e) {
                if (e.key === "Enter") {
                    e.preventDefault();
                    $("#btn_search").click();
                }
            });

            $("#spa-content").off("click", "#btn_next").on("click", "#btn_next", function () {
                page++;
                getReferrals();
                $("#btn_prev").removeClass("d-none");
            });

            $("#spa-content").off("click", "#btn_prev").on("click", "#btn_prev", function () {
                if (page == 0) return;
                if (page == 1) {
                    $("#btn_prev").addClass("d-none");
                }
                page--;
                getReferrals();
            });

            getReferrals();
        }

        $(document).ready(function () {
            bindEvents();
        });

        function getReferrals() {
        const patient = $("#search_patient").val().trim().toUpperCase();
        const department = ($(".btn-filter.btn-success").attr("data-filter")) ? $(".btn-filter.btn-success").attr("data-filter") : "";

        $("#referral_list").empty();
        $("#referral_list").append("<tr><td colspan='9'>Loading data, please wait ...</td></tr>");
        fetch('{{ route('getQueue') }}?search_patient=' + patient +
            "&search_department=" + department +
            "&page=" + page, {
            method: "GET"
        })
            .then(res => res.json())
            .then(data => {
                $("#pagination_container").addClass("d-none")
                $("#referral_list").empty();
                $("#btn_next").addClass("d-none");
                if (data.results.length < 1) {
                    $("#referral_list").append("<tr><td colspan='9'>No record found</td></tr>");
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
                                    <td>` + it.queue_position + `</td>
                                    <td>` + it.referring_hospital + `</td>
                                    <td>` + it.patient + `</td>
                                    <td>` + it.age + `</td>
                                    <td>` + it.gender + `</td>
                                    <td>` + department + `</td>
                                    <td>` + it.remarks + `</td>
                                    <td>` + moment(it.created_at).format("MM/DD/YYYY hh:mm:ss A") + `</td>
                                    <td class="d-flex gap-1 justify-content-center">
                                         <button class='btn btn-sm' style="background: transparent" title="Confirm received" onClick='confirmQueue("` + it.id + `")'>
                                            <i class="bi bi-check-circle-fill text-success"></i>
                                         </button>
                                         <button class='btn btn-sm' style="background: transparent" title="Delete" onClick='deleteQueue("` + it.id + `")'>
                                            <i class="bi bi-trash-fill text-danger"></i>
                                         </button>
                                    </td>

                                </tr>`;
                    $("#referral_list").append(tr);
                }

            }).catch(err => {
            $("#referral_list").empty();
            $("#referral_list").append("<tr><td colspan='9'>" + err.message + "</td></tr>");
        });
        }

        window.confirmQueue = function confirmQueue(id) {
        if (confirm("Confirm that this patient has been received by the department?")) {
            fetch('{{ route('confirmQueue') }}', {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                body: JSON.stringify({id: id})
            })
                .then(res => res.json())
                .then(data => {
                    if (data.code !== 200) {
                        toastr.error(data.message || "Failed to confirm", "Error");
                        return;
                    }
                    const tat = data.turnaround_minutes + "m " + data.turnaround_seconds + "s";
                    toastr.success(data.patient + " confirmed. Turnaround time: " + tat, "Confirmed");
                    getReferrals();
                }).catch(err => {
                toastr.error(err.message || "Something went wrong", "Error");
            });
        }
        }

        window.deleteQueue = function deleteQueue(id) {
        console.log(id);
        if (confirm("Are you sure you want to delete this queue?")) {

            fetch('{{ route('deleteQueue') }}', {
                method: "DELETE",
                headers: {
                    "Content-Type": "application/json",
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                body: JSON.stringify({id: id})
            })
                .then(res => res.json())
                .then(data => {
                    toastr.success(data.message, "Information");
                    getReferrals();
                }).catch(err => {
                toastr.error(err, "Ooops");
            })
        }
        }
    })();
</script>
