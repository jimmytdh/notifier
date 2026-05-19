<script>
    (function () {
        let page = 0;

        function formatTurnaround(queuedAt, confirmedAt) {
            const diff = moment(confirmedAt).diff(moment(queuedAt), 'seconds');
            const h = Math.floor(diff / 3600);
            const m = Math.floor((diff % 3600) / 60);
            const s = diff % 60;
            if (h > 0) return h + 'h ' + m + 'm ' + s + 's';
            if (m > 0) return m + 'm ' + s + 's';
            return s + 's';
        }

        function bindEvents() {
            $("#spa-content").off("click", ".btn-filter").on("click", ".btn-filter", function () {
                if ($(this).hasClass("btn-success")) {
                    $(this).removeClass("btn-success").addClass("btn-secondary");
                } else {
                    $(".btn-filter").removeClass("btn-success").addClass("btn-secondary");
                    $(this).removeClass("btn-secondary").addClass("btn-success");
                }
                page = 0;
                $("#btn_prev").addClass("d-none");
                getArchives();
            });

            $("#spa-content").off("click", "#btn_search").on("click", "#btn_search", function () {
                page = 0;
                getArchives();
            });

            $("#spa-content").off("keydown", "#search_patient").on("keydown", "#search_patient", function (e) {
                if (e.key === "Enter") {
                    e.preventDefault();
                    $("#btn_search").click();
                }
            });

            $("#spa-content").off("click", "#btn_next").on("click", "#btn_next", function () {
                page++;
                getArchives();
                $("#btn_prev").removeClass("d-none");
            });

            $("#spa-content").off("click", "#btn_prev").on("click", "#btn_prev", function () {
                if (page == 0) return;
                if (page == 1) $("#btn_prev").addClass("d-none");
                page--;
                getArchives();
            });

            getArchives();
        }

        $(document).ready(function () {
            bindEvents();
        });

        function getArchives() {
            const patient = $("#search_patient").val().trim().toUpperCase();
            const department = $(".btn-filter.btn-success").attr("data-filter") || "";

            $("#archive_list").empty();
            $("#archive_list").append("<tr><td colspan='10'>Loading data, please wait ...</td></tr>");

            fetch('{{ route('getQueueArchives') }}?search_patient=' + patient +
                "&search_department=" + department +
                "&page=" + page, {
                method: "GET"
            })
                .then(res => res.json())
                .then(data => {
                    $("#pagination_container").addClass("d-none");
                    $("#archive_list").empty();
                    $("#btn_next").addClass("d-none");

                    if (data.results.length < 1) {
                        $("#archive_list").append("<tr><td colspan='10'>No record found</td></tr>");
                        return;
                    }

                    $("#pagination_container").removeClass("d-none");

                    let max_visible = data.results.length;
                    if (max_visible == 11) {
                        max_visible = 10;
                        $("#btn_next").removeClass("d-none");
                    }

                    $("#page_items_count").text(((page * 10) + 1) + " - " + ((page * 10) + max_visible));

                    for (let i = 0; i < max_visible; i++) {
                        const it = data.results[i];
                        const queueNumber = (page * 10) + i + 1;
                        const department = it.department || "";
                        const tat = formatTurnaround(it.created_at, it.confirmed_at);

                        const tr = `<tr>
                                        <td>${queueNumber}</td>
                                        <td>${it.referring_hospital}</td>
                                        <td>${it.patient}</td>
                                        <td>${it.age || ""}</td>
                                        <td>${it.gender || ""}</td>
                                        <td>${department}</td>
                                        <td>${it.remarks || ""}</td>
                                        <td>${moment(it.created_at).format("MM/DD/YYYY hh:mm:ss A")}</td>
                                        <td>${moment(it.confirmed_at).format("MM/DD/YYYY hh:mm:ss A")}</td>
                                        <td><span class="badge bg-success">${tat}</span></td>
                                    </tr>`;
                        $("#archive_list").append(tr);
                    }
                }).catch(err => {
                $("#archive_list").empty();
                $("#archive_list").append("<tr><td colspan='10'>" + err.message + "</td></tr>");
            });
        }
    })();
</script>
