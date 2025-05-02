$(function () {
    $("#termsModal").modal("show");

    let currentSection = 0;

    const sections = $(".form-section");

    const requiredFields = {
        "section-1": ["dob", "nida_no", "address", "gvt_identification_letter"],
        "section-2": [
            "vehicle_name",
            "vehicle_type",
            "plate_number",
            "fuel_type",
        ],
        "section-3": [
            "gvt_guarantor_first_name",
            "gvt_guarantor_last_name",
            "gvt_guarantor_phone_number",
            "gvt_guarantor_nida_no",
            "gvt_guarantor_letter",
            "private_guarantor_first_name",
            "private_guarantor_last_name",
            "private_guarantor_phone_number",
            "private_guarantor_nida_no",
            "private_guarantor_letter",
        ],
    };

    function showSection(index) {
        sections.hide().eq(index).show();
    }

    function validateSection(sectionId) {
        $(".form-control").removeClass("border-danger");
        let isValid = true;

        requiredFields[sectionId].forEach((field) => {
            const input = $(`#${field}`);

            if (!input.val()) {
                console.log(field);
                input.addClass("border-danger");
                isValid = false;
            }
        });

        if (isValid) {
            calculateProgress();
            currentSection++;
            showSection(currentSection);
        }
    }

    function calculateProgress() {
        let filledInputs = 0;
        const totalFields = Object.values(requiredFields).flat().length;

        $("input, select")
            .not(
                "[id='first_name'],[id='last_name'],[id='phone_number'],[id='dob']"
            )
            .each(function () {
                if ($(this).val()) {
                    filledInputs++;
                }
            });

        let progress = (filledInputs / totalFields) * 100;
        $(".progress-bar").css("width", `${progress}%`);
    }

    $(".next-btn").click(function () {
        const sectionId = $(this).closest(".form-section").attr("id");
        validateSection(sectionId);
    });

    $(".prev-btn").click(function () {
        currentSection--;
        showSection(currentSection);
    });

    $("input, select").on("blur", calculateProgress);

    $("#submit-btn").click(function () {
        $(".form-control").removeClass("border-danger");

        let isValid = true;

        requiredFields["section-3"].forEach((field) => {
            const input = $(`#${field}`);
            if (!input.val()) {
                input.addClass("border-danger");
                isValid = false;
            }
        });

        if (isValid) {
            calculateProgress();

            const formData = new FormData();
            $("input, select").each(function () {
                const input = $(this);
                const name = input.attr("name");
                const type = input.attr("type");

                if (type === "file") {
                    const files = input[0].files;
                    if (files.length > 0) {
                        formData.append(name, files[0]);
                    }
                } else {
                    formData.append(name, input.val());
                }
            });

            $.ajax({
                url: `/store-loan-application/${borrower_id}`,
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    Swal.fire({
                        title: "",
                        icon: "success",
                        showConfirmButton: true,
                        allowEscapeKey: true,
                        text: response.message,
                    }).then(() => {
                        window.location.href = "/";
                    });
                },
                error: function (xhr) {
                    Swal.fire({
                        title: "",
                        icon: "error",
                        showConfirmButton: true,
                        allowEscapeKey: true,
                        text: xhr.responseJSON.message,
                    });
                },
            });
        }
    });

    showSection(currentSection);
});
