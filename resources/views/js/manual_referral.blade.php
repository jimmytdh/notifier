<script>
    (function () {
        function resetForm() {
            $("#destination_department").val("");
            $("#patient_name").val("");
            $("#patient_age").val("");
            $("#patient_gender").val("");
            $("#patient_ward").val("");
            $("#patient_bed_no").val("");
            $("#chief_complaint").val("");
            $("#diagnosis").val("");
            $("#remarks").val("");
            $(".is-invalid").removeClass("is-invalid");
        }

        function bindManualReferralEvents() {
            const $root = $("#spa-content");

            $root.off("click", "#btn_reset_manual");
            $root.off("click", "#btn_submit_manual");

            $root.on("click", "#btn_reset_manual", function () {
                resetForm();
            });

            $root.on("click", "#btn_submit_manual", function () {
                $(".is-invalid").removeClass("is-invalid");

                const patient = $("#patient_name").val().trim();
                const age = $("#patient_age").val().trim();
                const gender = $("#patient_gender").val().trim();
                const ward = $("#patient_ward").val().trim();
                const bed_no = $("#patient_bed_no").val().trim();
                const chief_complaint = $("#chief_complaint").val().trim();
                const diagnosis = $("#diagnosis").val().trim();
                const remarks = $("#remarks").val().trim();
                const code = $("#destination_department").val();
                const department = $("#destination_department option:selected").text();

                let is_valid = true;
                if (patient === "") {
                    $("#patient_name").addClass("is-invalid");
                    is_valid = false;
                }
                if (age === "") {
                    $("#patient_age").addClass("is-invalid");
                    is_valid = false;
                }
                if (gender === "") {
                    $("#patient_gender").addClass("is-invalid");
                    is_valid = false;
                }
                if (code === "") {
                    $("#destination_department").addClass("is-invalid");
                    is_valid = false;
                }
                if (remarks === "") {
                    $("#remarks").addClass("is-invalid");
                    is_valid = false;
                }

                if (!is_valid) {
                    toastr.error("Please complete the required fields.", "Validation");
                    return;
                }

                const params = {
                    patient: patient,
                    age: age,
                    gender: gender,
                    ward: ward,
                    bed_no: bed_no,
                    chief_complaint: chief_complaint,
                    diagnosis: diagnosis,
                    remarks: remarks,
                    department: department,
                    code: code
                };

                $(this).prop("disabled", true);
                $(this).html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...`);

                fetch('{{ route('storeManualReferral') }}', {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    body: JSON.stringify(params)
                })
                    .then(res => res.json())
                    .then(data => {
                        $("#btn_submit_manual").prop("disabled", false);
                        $("#btn_submit_manual").html(`<i class="bi bi-send-check me-1"></i> Submit Manual Referral`);
                        if (data.code == 500) {
                            toastr.error(data.message || "Unable to create manual referral", "Error");
                            return;
                        }

                        toastr.success(data.message, "Information");
                        insert(code, patient, function () {
                            deleteChild(code);
                        });
                        resetForm();
                    })
                    .catch(err => {
                        $("#btn_submit_manual").prop("disabled", false);
                        $("#btn_submit_manual").html(`<i class="bi bi-send-check me-1"></i> Submit Manual Referral`);
                        toastr.error(err.message || "Unable to create manual referral", "Error");
                    });
            });
        }

        bindManualReferralEvents();
    })();
</script>
