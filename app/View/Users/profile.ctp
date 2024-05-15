<div class="container">
    <div class="row">
        <div class="col">
            <h1>Profile</h1>
        </div>
    </div>

    <div class="row col alerts">
    </div>

    <div class="row">
        <div class="col-4">

            <div class="card p-2">
                <form action="#" id="profpic_Form" accept-charset="utf-8" enctype="multipart/form-data">
                    <?php
                    $profile_picture = $user["profile_picture"] ?? 'placeholder.png';
                    echo $this->Html->image($profile_picture, array(
                        'alt' => 'Image Alt Text',
                        'class' => 'img_profile img-responsive card-img-top rounded mx-auto d-block', // Example CSS class
                        'id' => 'my-image', // Example ID
                        'height' => '260',
                        'width' => '225'
                    ));
                    ?>
                    <div class="card-body">
                        <input type="file" name="profile_picture" id="profile_picture" style="display: none;">
                        <button class="btn btn-light btn-sm btn-upload w-100" type="button">Change profile picture</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="col">
            <form id="profileForm">

                <div class="form-group row">
                    <label class="col-sm-2" for="email">Email</label>
                    <input class="col-sm-10 form-control" name="email" type="text" id="email" value="<?= $user["email"] ?? "" ?>">
                </div>
                <div class="form-group row">
                    <label class="col-sm-2" for="birthdate">Birthdate</label>
                    <input class="col-sm-10 form-control" name="birthdate" type="text" id="birthdate" value="<?= $user["birthdate"] ?? "" ?>">
                </div>
                <div class="form-group row">
                    <label class="col-sm-2" for="gender">Gender</label>
                    <input class="col-sm-10 form-control" name="gender" type="text" id="gender" value="<?= $user["gender"] ?? "" ?>">
                </div>
                <div class="form-group row">
                    <label class="col-sm-2" for="hobby">Hobby</label>
                    <textarea class="col-sm-10 form-control" name="hobby" id="hobby"><?= $user["hobby"] ?? "" ?></textarea>
                </div>
                <div class="form-group row d-flex justify-content-end">
                    <button class="btn btn-primary">Save profile</button>
                </div>
            </form>

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