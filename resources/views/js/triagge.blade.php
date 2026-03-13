<script>
    (function () {
        let current_list = [];
        let is_init = false;
        let referral_data;
        let page = 0;
        const page_size = 10;
        let page_cache = [];
        let next_page_available = false;
        function getPatientModal() {
            const el = document.getElementById('patient_modal');
            if (!el || !window.bootstrap || !window.bootstrap.Modal) return null;
            return window.bootstrap.Modal.getOrCreateInstance(el);
        }

        function getForwardModal() {
            const el = document.getElementById('forward_modal');
            if (!el || !window.bootstrap || !window.bootstrap.Modal) return null;
            return window.bootstrap.Modal.getOrCreateInstance(el);
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

    function setLoadingState() {
        $("#referral_list").empty();
        $("#referral_list").append(`<tr>
                <td colspan="8"><div class="text-center">Loading referrals, please wait ...</div></td>
            </tr>`);
    }

    function resetPaginationAndReload() {
        page = 0;
        page_cache = [];
        next_page_available = false;
        setLoadingState();
        loadPage(0);
    }

    function loadPage(targetPage) {
        if (targetPage < 0) return;

        if (page_cache[targetPage]) {
            page = targetPage;
            current_list = page_cache[targetPage].items;
            next_page_available = page_cache[targetPage].hasMore;
            renderReferrals();
            return;
        }

        if (targetPage !== page_cache.length) return;

        const beforeKey = targetPage > 0 ? page_cache[targetPage - 1].oldestKey : null;
        if (targetPage > 0 && !beforeKey) return;

        fetchPageFromFirebase(beforeKey, function (result) {
            const pageData = {
                items: result.items,
                oldestKey: result.oldestKey,
                hasMore: result.hasMore
            };

            page_cache[targetPage] = pageData;
            page = targetPage;
            current_list = result.items;
            next_page_available = result.hasMore;
            renderReferrals();
        });
    }

    function fetchPageFromFirebase(beforeKey, callback) {
        let query = firebase.database().ref("23").orderByKey();
        if (beforeKey) {
            query = query.endAt(beforeKey).limitToLast(page_size + 1);
        } else {
            query = query.limitToLast(page_size + 1);
        }

        query.once("value", function (snapshot) {
            const rows = [];
            snapshot.forEach(function (child) {
                const item = child.val() || {};
                item.key = child.key;
                rows.push(item);
            });

            if (beforeKey && rows.length > 0 && rows[rows.length - 1].key === beforeKey) {
                rows.pop();
            }

            if (!beforeKey && rows.length > page_size) {
                rows.shift();
            }

            const hasMore = snapshot.numChildren() === (page_size + 1);

            callback({
                items: rows,
                oldestKey: rows.length > 0 ? rows[0].key : null,
                hasMore: hasMore
            });
        });
    }

    function toUpper(value) {
        return (value || "").toString().trim().toUpperCase();
    }

    function getFilteredReferrals() {
        const patient = toUpper($("#search_patient").val());
        const department = toUpper($("#search_department").val());

        return current_list.filter(function (item) {
            const patientName = toUpper(item.patient);
            const departmentName = toUpper(item.department);
            return patientName.includes(patient) && departmentName.includes(department);
        });
    }

    function renderReferrals() {
        const filtered = getFilteredReferrals();
        const total = filtered.length;
        $("#referral_list").empty();

        if (total < 1) {
            $("#referral_list").append("<tr><td colspan='8'><div class='text-center'>No record found</div></td></tr>");
            $("#page_items_count").text("0");
            $("#btn_prev").toggleClass("d-none", page === 0);
            $("#btn_next").toggleClass("d-none", !next_page_available);
            return;
        }

        filtered.forEach(function (data) {
            const sex = data.sex || data.gender || "";
            const chiefComplaint = data.chiefComplaint || data.chief_complaint || "";
            const tr = `<tr>
                            <td>` + (data.referring_hospital || "") + `</td>
                            <td>` + (data.patient || "") + `</td>
                            <td>` + (data.age || "") + `</td>
                            <td>` + sex + `</td>
                            <td class="d-none" style="max-width: 100px;text-overflow:ellipsis;white-space: nowrap;overflow: hidden">` + chiefComplaint + `</td>
                            <td>` + (data.department || "") + `</td>
                            <td>` + moment(data.date_referred).format("MM/DD/YYYY hh:mm:ss A") + `</td>
                            <td>
                                <button class='btn btn-sm' style="background: transparent" onClick='viewPatient("` + data.key + `")'>
                                    <i class="bi bi-eye-fill text-success"></i>
                                </button>
                            </td>
                        </tr>`;
            $("#referral_list").append(tr);
        });

        const start = (page * page_size) + 1;
        const end = (page * page_size) + filtered.length;
        $("#page_items_count").text(start + " - " + end);
        $("#btn_prev").toggleClass("d-none", page === 0);
        $("#btn_next").toggleClass("d-none", !next_page_available);
    }
        window.viewPatient = function (key) {
            referral_data = getDataByKey(key);
            if (referral_data) {
                $("#patient").text(referral_data.patient);
                $("#age").text(referral_data.age + " years old");
                $("#sex").text(referral_data.sex);
                $("#diagnosis").text(referral_data.diagnosis);
                $("#chief_complaint").text(referral_data.chiefComplaint);
            const modal = getPatientModal();
            if (modal) modal.toggle();
        }
        }

        function bindEvents() {
            $("#activity_container").addClass("d-none");
            setLoadingState();
            $("#pagination_container").removeClass("d-none");

            $("#btn_search").off("click").on("click", function () {
                page = 0;
                renderReferrals();
            });

            $("#search_patient, #search_department").off("keydown").on("keydown", function (e) {
                if (e.key === "Enter") {
                    e.preventDefault();
                    $("#btn_search").click();
                }
            });

            $("#btn_next").off("click").on("click", function () {
                loadPage(page + 1);
            });

            $("#btn_prev").off("click").on("click", function () {
                loadPage(page - 1);
            });

            $("#btn_done").off("click").on("click", function () {
                if (!confirm("Are you sure you want to tagged this as DONE?")) return;

                let remarks = prompt("Remarks:", "TAGGED AS DONE");
                if (remarks === null) return;
                remarks = remarks.trim();
                if (remarks == "") {
                    alert("Please specify remarks");
                    return;
                }

                const params = {
                    firebase_key: referral_data.key,
                    patient: referral_data.patient,
                    gender: referral_data.sex,
                    age: referral_data.age,
                    diagnosis: referral_data.diagnosis,
                    chief_complaint: referral_data.chiefComplaint,
                    referring_hospital: referral_data.referring_hospital,
                    department: "",
                    topic: "",
                    remarks: remarks,
                    date_created: referral_data.date_referred
                }

                $(this).prop("disabled", true);
                $(this).html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...`);
                fetch('{{ route('tagAsDoneTriagge') }}', {
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

                        const modal = getPatientModal();
                        if (modal) modal.toggle();
                        toastr.success(data.message, "Information");

                        deleteChild("23/" + referral_data.key);
                        const index = getPositionByKey(referral_data.key);
                        if (index > -1) {
                            resetPaginationAndReload();
                        }

                    }).catch(err => {
                    toastr.error(err, "Ooops");
                }).finally(() => {
                    $("#btn_done").prop("disabled", false);
                    $("#btn_done").html("TAG AS DONE");
                });
            });

            $("#btn_queue").off("click").on("click", function () {
                if (!referral_data) {
                    toastr.error("No selected patient", "Error");
                    return;
                }

                const remarks = "QUEUED FROM TRIAGGE";

                const params = {
                    referring_hospital: (referral_data.referring_hospital || "TRIAGGE"),
                    patient: (referral_data.patient || ""),
                    age: (referral_data.age || ""),
                    gender: (referral_data.sex || referral_data.gender || ""),
                    department: (referral_data.department || ""),
                    remarks: remarks
                };

                $(this).prop("disabled", true);
                $(this).html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...`);
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
                        $("#btn_queue").prop("disabled", false);
                        $("#btn_queue").html("QUEUE PATIENT");
                        if (data.code == 500) {
                            toastr.error(data.message || "Unable to queue patient", "Error");
                            return;
                        }

                        toastr.success(data.message, "Information");
                    }).catch(err => {
                    $("#btn_queue").prop("disabled", false);
                    $("#btn_queue").html("QUEUE PATIENT");
                    toastr.error(err.message || "Unable to queue patient", "Error");
                });
            });

            $("#btn_send").off("click").on("click", function () {
                $("#forward_department").val("");
                $("#forward_remarks").val("");
                const modal = getForwardModal();
                if (modal) modal.show();
            });

            $("#btn_confirm_forward").off("click").on("click", function () {
                $(".is-invalid").removeClass("is-invalid");
                const remarks = $("#forward_remarks").val().trim();
                const topic = $("#forward_department").val();
                const department = $("#forward_department option:selected").text();
                if (topic == "") {
                    $("#forward_department").addClass("is-invalid");
                    return;
                }
                if (remarks == "") {
                    $("#forward_remarks").addClass("is-invalid");
                    return;
                }
                const params = {
                    firebase_key: referral_data.key,
                    patient: referral_data.patient,
                    gender: referral_data.sex,
                    age: referral_data.age,
                    diagnosis: referral_data.diagnosis,
                    chief_complaint: referral_data.chiefComplaint,
                    referring_hospital: referral_data.referring_hospital,
                    department: department,
                    topic: topic,
                    remarks: remarks,
                    date_created: referral_data.date_referred
                }

                $(this).prop("disabled", true);
                $(this).html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...`);
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
                        $("#btn_confirm_forward").prop("disabled", false);
                        $("#btn_confirm_forward").html("Confirm Forward");
                        if (data.code == 500) {
                            toastr.error(data.message, "Ooops");
                            return;
                        }

                        const forwardModal = getForwardModal();
                        if (forwardModal) forwardModal.hide();
                        const modal = getPatientModal();
                        if (modal) modal.toggle();
                        toastr.success(data.message, "Information");
                        insert(params.topic, params.patient, function (message) {
                            deleteChild(params.topic);
                        });

                        deleteChild("23/" + referral_data.key);
                        const index = getPositionByKey(referral_data.key);
                        if (index > -1) {
                            resetPaginationAndReload();
                        }

                    }).catch(err => {
                    $("#btn_confirm_forward").prop("disabled", false);
                    $("#btn_confirm_forward").html("Confirm Forward");
                    toastr.error(err, "Ooops");
                });
            });

            observe("23", function (snapshot) {
                if (!is_init || !snapshot.val()) return;
                toastr.success(snapshot.val().patient, "New referral");
                var msg = new SpeechSynthesisUtterance();
                msg.text = "New referral";
                window.speechSynthesis.speak(msg);

                resetPaginationAndReload();
            });

            observeRemoved("23", function () {
                if (!is_init) return;
                resetPaginationAndReload();
            });

            get("23", function (snapshots) {
                is_init = true;
                resetPaginationAndReload();
            });
        }

        $(document).ready(function () {
            bindEvents();
        });
    })();
</script>
