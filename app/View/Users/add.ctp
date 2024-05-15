<style>
    .wrapper {
        height: 100vh;
    }
</style>

<div class="container d-flex justify-content-center align-items-center h-100">
    <div class="col-md-6">
        <div class="row">
            <div class="col-6">
                <h1 id="title">Register</h1>
            </div>
            <div class="col-6 d-flex align-items-center justify-content-end">
                <small><a href="../users/login">Already registered? Click me to login!</a></small>
            </div>
        </div>
        <div class="row error-messages d-block">

        </div>
        <form action="" method="post" id="UserAddForm">
            <div class="form-group">
                <input class="form-control" name="name" type="text" placeholder="Name" required>
            </div>
            <div class="form-group">
                <input class="form-control" name="email" type="text" placeholder="Email" required>
            </div>
            <div class="form-group">
                <input class="form-control" name="password" type="password" placeholder="Password" required>
            </div>
            <div class="form-group">
                <input class="form-control" name="confirm_password" type="password" placeholder="Confirm Password" required>
            </div>
            <button class="btn btn-primary w-100 mt-3" type="submit">Register</button>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        $("#UserAddForm").on("submit", function(e) {
            e.preventDefault();

            let _form = $(this).serializeArray();

            $.ajax({
                url: "add",
                data: _form,
                type: "POST",
                dataType: "JSON",
                success: function(response) {
                    displayStatus(response);
                }
            })
        });

        function displayStatus(response) {
            let errorHtml = '';

            if (response.status) {
                errorHtml += `<div class="alert alert-success">${response.message}</div>`;
            } else {
                $.each(response, function(field, messages) {
                    $.each(messages, function(index, message) {
                        errorHtml += `<div class="alert alert-danger d-block">${message}</div>`;
                    });
                });
            }

            $('.error-messages').html(errorHtml);
        }
    });
</script>