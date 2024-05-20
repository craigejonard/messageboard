<div class="container">
    <div class="row mb-2">
        <div class="col">
            <div class="d-flex justify-content-start align-items-center">
                <h1 class="mr-2">Profile</h1>
                <?php echo $this->Html->link(
                    "Edit Profile",
                    array(
                        "controller" => "users",
                        "action" => "profile_edit"
                    ),
                    array(
                        "class" => "btn btn-warning btn-sm"
                    )
                ); ?>
            </div>
        </div>
    </div>

    <div class="row col alerts">
    </div>

    <div class="row">
        <div class="col-4">

            <div class="card p-2">
                <?php
                $profile_picture = isset($user["profile_picture"]) ? $user["profile_picture"] : 'placeholder.png';
                echo $this->Html->image($profile_picture, array(
                    'alt' => 'Image Alt Text',
                    'class' => 'img_profile img-responsive card-img-top rounded mx-auto d-block', // Example CSS class
                    'id' => 'my-image', // Example ID
                    'height' => '260',
                    'width' => '225'
                ));
                ?>
            </div>
        </div>
        <div class="col">
            <div class="form-group row">
                <div class="col">
                    <h2><?= isset($user["name"]) ? $user["name"] : "-" ?></h2>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 font-weight-bold">Gender:</label>
                <div class="col-sm-10">
                    <p><?= isset($user["gender"]) ? $user["gender"] : "-" ?></p>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 font-weight-bold">Birthdate:</label>
                <div class="col-sm-10">
                    <p><?= isset($user["birthdate"]) ? $user["birthdate"] : "-" ?></p>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 font-weight-bold">Joined:</label>
                <div class="col-sm-10">
                    <p><?= isset($user["created"]) ? $user["created"] : "-" ?></p>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 font-weight-bold">Last Login:</label>
                <div class="col-sm-10">
                    <p><?= isset($user["last_login_time"]) ? $user["last_login_time"] : "-" ?></p>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 font-weight-bold">Hobby:</label>
                <div class="col-sm-10">
                    <p><?= isset($user["hobby"]) ? $user["hobby"] : "-" ?></p>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    $(document).ready(function() {
        $("#birthdate").datepicker({
            dateFormat: 'M dd, yy'
        });

        $("#profileForm").submit(function(e) {
            e.preventDefault();

            let _form = $(this).serializeArray();

            $.ajax({
                url: "<?= $this->Html->url(array('controller' => 'users', 'action' => 'profile')) ?>",
                type: "POST",
                data: _form,
                dataType: "JSON",
                success: function(response) {

                    displayStatus(response);
                },

            });
        });

        $(".img_profile, .btn-upload").click(function() {
            $("#profile_picture").click();
        });

        $("#profile_picture").change(function() {
            let file = this.files[0];
            let reader = new FileReader();

            reader.onload = function(e) {
                $(".img_profile").attr('src', e.target.result);
            }

            reader.readAsDataURL(file);

            uploadProfilePicture();
        });

        function uploadProfilePicture() {
            let formData = new FormData($("#profpic_Form")[0]);

            $.ajax({
                url: "<?= $this->Html->url(array('controller' => 'users', 'action' => 'uploadPictureToServer')) ?>",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                dataType: "JSON",
                success: function(response) {
                    displayStatus(response);
                }
            });
        }

        function displayStatus(response) {
            let errorHtml = '';

            if (response.status) {
                errorHtml += `<div class="alert alert-success">${response.message}</div>`;
            } else {
                $.each(response, function(field, messages) {
                    $.each(messages, function(index, message) {
                        errorHtml += `<div class="alert alert-danger">${message}</div>`;
                    });
                });
            }

            $('.alerts').html(errorHtml);
        }
    })
</script>