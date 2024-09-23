$(function () {
    $(".select-role").select2({
        placeholder: null,
    });

    // Store User
    $("#createModal").on("hidden.bs.modal", function () {
        $("#first_name").val(null);
        $("#last_name").val(null);
        $("#phone_number").val(null);

        $("#store-btn").text("Add User");
        $("#store-btn").prop("disabled", false);

        $(".form-control").removeClass("border-danger");
    });

    $("#store-btn").on("click", async function () {
        let requiredFields = ["first_name", "last_name", "phone_number"];

        $(".form-control").removeClass("border-danger");

        for (const field of requiredFields) {
            const $field = $(`#${field}`);

            if ($field.val() == "") {
                $field.addClass("border-danger");
                return;
            } else {
                $field.removeClass("border-danger");
            }
        }

        $("#store-btn").prop("disabled", true);
        $("#store-btn").text("Loading ....");

        $.ajax({
            url: "/store-user",
            type: "POST",
            data: {
                draw: Math.floor(Math.random() * 1000),
                _token: $('meta[name="csrf-token"]').attr("content"),
                first_name: $("#first_name").val(),
                last_name: $("#last_name").val(),
                phone_number: $("#phone_number").val(),
            },
            success: function () {
                window.location.reload();
            },
            error: function (xhr) {
                $("#createModal").modal("hide");

                Swal.fire({
                    title: "",
                    icon: "error",
                    showConfirmButton: true,
                    allowEscapeKey: true,
                    text: xhr.responseJSON.message,
                });
            },
        });
    });

    // Update User
    $("#updateModal").on("hidden.bs.modal", function () {
        $("#patch_first_name").val(null);
        $("#patch_last_name").val(null);
        $("#patch_phone_number").val(null);

        $("#update-btn").text("Save Changes");
        $("#update-btn").prop("disabled", false);

        $(".form-control").removeClass("border-danger");
    });

    $("#update-btn").on("click", async function () {
        let requiredFields = [
            "patch_first_name",
            "patch_last_name",
            "patch_phone_number",
        ];

        $(".form-control").removeClass("border-danger");

        for (const field of requiredFields) {
            const $field = $(`#${field}`);

            if ($field.val() == "") {
                $field.addClass("border-danger");
                return;
            } else {
                $field.removeClass("border-danger");
            }
        }

        $("#update-btn").text("Save Changes");
        $("#update-btn").prop("disabled", false);

        $.ajax({
            url: `/update-user/${user_id}`,
            type: "PUT",
            data: {
                draw: Math.floor(Math.random() * 1000),
                _token: $('meta[name="csrf-token"]').attr("content"),
                patch_first_name: $("#patch_first_name").val(),
                patch_last_name: $("#patch_last_name").val(),
                patch_phone_number: $("#patch_phone_number").val(),
            },
            success: function (response) {
                $("#users-table").load(
                    location.href + " #users-table>*",
                    function () {
                        $("#users-table").DataTable();
                    }
                );

                $("#updateModal").modal("hide");

                Swal.fire({
                    title: "",
                    icon: "success",
                    showConfirmButton: true,
                    allowEscapeKey: true,
                    text: response.message,
                });
            },
            error: function (xhr) {
                $("#updateModal").modal("hide");

                Swal.fire({
                    title: "",
                    icon: "error",
                    showConfirmButton: true,
                    allowEscapeKey: true,
                    text: xhr.responseJSON.message,
                });
            },
        });
    });
});
