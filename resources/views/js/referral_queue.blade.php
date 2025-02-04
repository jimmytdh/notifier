<script>
    let current_list = [];
    let is_init = false;
    let is_searching = false;
    let referral_id;
    let page = 0;

    $(document).ready(function () {

        $(".btn-filter").click(function () {
            if ($(this).hasClass("btn-success")) {
                $(this).removeClass("btn-success");
                $(this).addClass("btn-secondary");
            } else {
                $(".btn-filter").removeClass("btn-success");
                $(".btn-filter").addClass("btn-secondary");
                $(this).removeClass("btn-secondary");
                $(this).addClass("btn-success");
            }

            getReferrals();
        });

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

        getReferrals();
    });

    function getReferrals() {
        const patient = $("#search_patient").val().trim().toUpperCase();
        const department = ($(".btn-filter.btn-success").attr("data-filter")) ? $(".btn-filter.btn-success").attr("data-filter") : "";

        $("#referral_list").empty();
        $("#referral_list").append("<tr><td colspan='6'>Loading data, please wait ...</td></tr>");
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
                                    <td>` + department + `</td>
                                    <td>` + it.remarks + `</td>
                                    <td>` + moment(it.created_at).format("MM/DD/YYYY hh:mm:ss A") + `</td>
                                    <td>
                                         <button class='btn btn-sm' style="background: transparent" onClick='deleteQueue("` + it.id + `")'>
                                            <i class="bi bi-trash-fill text-danger"></i>
                                         </button>
                                    </td>

                                </tr>`;
                    $("#referral_list").append(tr);
                }

            }).catch(err => {
            $("#referral_list").empty();
            $("#referral_list").append("<tr><td colspan='7'>" + err.message + "</td></tr>");
        });
    }

    function deleteQueue(id) {
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
</script>
