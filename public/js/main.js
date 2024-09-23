$(function () {
    $(".select2").select2({
        placeholder: null,
    });

    $(document).on("input", ".amount", function () {
        let inputValue = $(this).val();
        let sanitizedValue = inputValue.replace(/[^0-9]/g, ""); // Remove all non-digit characters

        // Parse the sanitized value to an integer
        let parsedValue = parseInt(sanitizedValue, 10);

        if (isNaN(parsedValue)) {
            $(this).val(null);
        } else {
            let formattedValue = parsedValue.toLocaleString("en-US", {
                useGrouping: true,
                minimumFractionDigits: 0,
                maximumFractionDigits: 0,
            });
            $(this).val(formattedValue);
        }
    });

    const dataTableConfig = (
        title,
        exportColumns = null,
        ordering = false
    ) => ({
        ordering: ordering,
        pageLength: 100,
        responsive: true,
        dom: '<"html5buttons"B>lTfgitp',
        buttons: [
            {
                extend: "excel",
                title: title,
                exportOptions: exportColumns ? { columns: exportColumns } : {},
            },
        ],
    });

    const tables = [
        { id: "#users-table", title: "Users" },
        { id: "#repayments-table", title: "repayments" }
    ];

    tables.forEach((table) => {
        $(table.id).DataTable(
            dataTableConfig(table.title, table.exportColumns, table.ordering)
        );
    });
});

document.addEventListener("DOMContentLoaded", function (event) {
    function OTPInput() {
        const inputs = document.querySelectorAll("#otp > *[id]");
        for (let i = 0; i < inputs.length; i++) {
            inputs[i].addEventListener("keydown", function (event) {
                if (event.key === "Backspace") {
                    inputs[i].value = "";
                    if (i !== 0) inputs[i - 1].focus();
                } else {
                    if (i === inputs.length - 1 && inputs[i].value !== "") {
                        return true;
                    } else if (event.keyCode > 47 && event.keyCode < 58) {
                        inputs[i].value = event.key;
                        if (i !== inputs.length - 1) inputs[i + 1].focus();
                        event.preventDefault();
                    } else if (event.keyCode > 64 && event.keyCode < 91) {
                        inputs[i].value = String.fromCharCode(event.keyCode);
                        if (i !== inputs.length - 1) inputs[i + 1].focus();
                        event.preventDefault();
                    }
                }
            });
        }
    }
    OTPInput();
});
