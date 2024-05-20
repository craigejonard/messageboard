<style>
    .container a {
        text-decoration: none;
        color: black;
    }

    .container a.btn {
        color: white;
    }

    .container a:hover {
        text-decoration: none;
        color: white;
    }

    .container a:hover {
        text-decoration: none;
        color: black;
    }

    .container a .card {
        transition: transform 0.2s;
    }

    .container a:hover .card {
        transform: scale(1.05);
        background-color: #f8f9fa;
    }
</style>

<div class="container">
    <div class="row">
        <div class="col mt-3 p-0">
            <div class="form-inline">
                <label for="search">Search</label>
                <input type="text" id="search" class="form-control form-control-sm ml-2" placeholder="Search for messages">
            </div>
        </div>
        <div class="col mt-3 p-0">
            <div class="d-flex justify-content-end align-items-center">
                <?php echo $this->Html->link(
                    "New Message",
                    array(
                        "controller" => "messages",
                        "action" => "add"
                    ),
                    array(
                        "class" => "btn btn-primary btn-sm"
                    )
                ); ?>
            </div>
        </div>
    </div>

    <div class="convo_body">
        <?php foreach ($recipients as $recipient_data) : ?>
            <a href="<?= $this->Html->url(array("controller" => "messages", "action" => "conversation", $recipient_data['user']['id'])) ?>" class="row d-flex justify-content-center align-items-center my-2 message-row">
                <div class="card mb-3 w-100">
                    <div class="row no-gutters">
                        <div class="col-2 border-right">
                            <?php echo $this->html->image(
                                $recipient_data['user']['profile_picture'] ?? 'placeholder.png',
                                array(
                                    'alt' => 'Image Alt Text',
                                    'class' => 'img-thumbnail border-0', // Example CSS class
                                    'id' => 'my-image', // Example ID
                                )
                            ); ?>
                        </div>
                        <div class="col-10 d-flex flex-column">
                            <div class="card-body">
                                <div class="position-relative">
                                    <div class="position-absolute" style="top: 0; right: 0;">
                                        <button data-userId="<?= $recipient_data['user']['id']; ?>" class="btn btn-danger btn-delete btn-sm">Delete</button>
                                    </div>
                                </div>
                                <h5 class="card-title"><?= $recipient_data['user']['name'] ?? "Unknown user"; ?></h5>
                                <p class="card-text"><?= strlen($recipient_data['message']['message']) > 50 ? substr($recipient_data['message']['message'], 0, 50) . "..." : $recipient_data['message']['message'] ?></p>
                            </div>
                            <div class="card-footer mt-auto">
                                <p class="card-text"><small class="text-muted"><?= date("F d, Y h:i a", strtotime($recipient_data['message']['created']))  ?></small></p>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<template id="message_list_temp">
    <a class="row d-flex justify-content-center align-items-center my-2 message-row" href="/messageboard/messages/conversation/{{userId}}">
        <div class="card mb-3 w-100">
            <div class="row no-gutters">
                <div class="col-2 border-right">
                    <img src="/messageboard/img/{{profile_picture}}" class="img-thumbnail border-0" alt="Image Alt Text" id="my-image">
                </div>
                <div class="col-10 d-flex flex-column">
                    <div class="card-body">
                        <div class="position-relative">
                            <div class="position-absolute" style="top: 0; right: 0;">
                                <button data-userId="{{userId}}" class="btn btn-danger btn-delete btn-sm">Delete</button>
                            </div>
                        </div>
                        <h5 class="card-title">{{name}}</h5>
                        <p class="card-text">{{message}}</p>
                    </div>
                    <div class="card-footer mt-auto">
                        <p class="card-text"><small class="text-muted">{{created}}</small></p>
                    </div>
                </div>
            </div>
        </div>
    </a>
</template>

<script>
    $(document).ready(function() {


        $(".btn-delete").click(function(e) {
            e.preventDefault();
            e.stopPropagation();

            let _this = $(this);

            if (confirm("Are you sure you want to delete this message?")) {
                var userId = $(this).data('userid');
                $.ajax({
                    url: "<?= $this->Html->url(array('controller' => 'messages', 'action' => 'delete_messages')) ?>",
                    type: "GET",
                    data: {
                        userId: userId
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.status) {
                            _this.closest(".message-row").hide('show', function() {
                                $(this).remove();
                            });
                        } else {
                            alert(response.message);
                        }
                    }
                });
            }
        });

        $("#search").on("keyup", debounce(function() {
            let search = $(this).val().toLowerCase();
            $.ajax({
                url: "<?= $this->Html->url(array('controller' => 'messages', 'action' => 'index')) ?>",
                data: {
                    search: search
                },
                type: "GET",
                dataType: "json",
                success: function(response) {
                    if (response.status) {
                        $(".convo_body").empty();
                        let template = $("#message_list_temp").html();
                        response.recipients.forEach(function(recipient) {
                            let temp = template;

                            console.log(recipient.user);

                            temp = temp.replace(/{{profile_picture}}/g, (recipient.user.profile_picture) ?? 'placeholder.png');
                            temp = temp.replace(/{{name}}/g, recipient.user.name);
                            temp = temp.replace(/{{message}}/g, recipient.message.message);
                            temp = temp.replace(/{{created}}/g, recipient.message.created);
                            temp = temp.replace(/{{userId}}/g, recipient.user.id);
                            $(".convo_body").append(temp);
                        });
                    }
                }
            });
        }, 300));

        function debounce(func, wait) {
            let timeout;
            return function() {
                const context = this,
                    args = arguments;
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    func.apply(context, args);
                }, wait);
            };
        }
    })
</script>