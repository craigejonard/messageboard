<style>
    .wrapper {
        height: 100vh;
    }
</style>

<div class="container d-flex justify-content-center align-items-center h-75">
    <div class="col-md-6">
        <div class="row mb-2">
            <div class="col">
                <h2>New Message</h1>
            </div>
            <div class="col d-inline-flex align-items-center justify-content-end">
                <small><?php
                        echo $this->Html->link(
                            "Back to Messages list",
                            array(
                                "controller" => "messages",
                                "action" => "index"
                            )
                        ); ?></small>
            </div>
        </div>
        <form method="post" id="addMessageForm">
            <div class="form-group row">
                <label class="col-sm-2 col-form-label" for="recipient">Recipient</label>
                <div class="col-sm-10">
                    <select class="form-control" name="data[Message][recipient_id]" id="recipient">

                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label" for="recipient">Message</label>
                <div class="col-sm-10">
                    <textarea name="data[Message][message]" class="form-control"></textarea>
                </div>
            </div>
            <div class="status">

            </div>
            <button class="btn btn-primary w-100 mt-3" type="submit" id="login-button">Send</button>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        $("#recipient").select2({
            ajax: {
                theme: 'bootstrap',
                url: "<?= $this->Html->url(array('controller' => 'users', 'action' => 'get_users')) ?>",
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.User.name,
                                id: item.User.id
                            }
                        })
                    };
                },
                cache: true,
                placeholder: "Search for a recipient",
                minimumInputLength: 1
            }
        });

        $("#addMessageForm").submit(function(e) {
            e.preventDefault();
            var form = $(this);

            $.ajax({
                url: "<?= $this->Html->url(array('controller' => 'messages', 'action' => 'add')) ?>",
                type: "POST",
                data: form.serializeArray(),
                dataType: "JSON",
                success: function(response) {
                    displayStatus(response);

                    if (response.status) {
                        form.trigger("reset");
                    }
                }
            })
        });

        function displayStatus(response) {
            var status = $(".status");
            status.empty();

            if (response.status) {
                status.append(
                    "<div class='alert alert-success'>" + response.message + "</div>"
                );
            } else {
                status.append(
                    "<div class='alert alert-danger'>" + response.message + "</div>"
                );
            }
        }
    });
</script>