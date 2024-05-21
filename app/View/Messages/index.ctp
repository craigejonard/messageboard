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
        transform: scale(1.01);
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
        <?php if ($recipients) : ?>
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
                                    <p class="card-text" data-id="<?= $recipient_data['message']['id'] ?>">
                                        <?= $recipient_data['message']['message'] ?>
                                    </p>
                                    <div class="row justify-content-center align-items-center show-more">

                                    </div>
                                </div>
                                <div class="card-footer mt-auto">
                                    <p class="card-text"><small class="text-muted"><?= date("F d, Y h:i a", strtotime($recipient_data['message']['created']))  ?></small></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="row my-2">
                <div class="col p-0">
                    <div class="alert alert-info">No messages found</div>
                </div>
            </div>
        <?php endif; ?>

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

        let text_arr = [];


        $(document).find(".message-row .card-text").each(function() {
            let text = $(this).text();
            let text_id = $(this).data('id');

            if (text.length > 100) {
                let short_text = text.substring(0, 100) + "...";
                let long_text = text;

                $(this).text(short_text);
                $(this).closest(".card-body").find(".show-more").append(`<button data-id="${text_id}" class="btn btn-primary btn-sm">Show more</button>`);

                text_arr.push({
                    id: text_id,
                    text: long_text
                });
            }
        });




        $(document).on("click", ".show-more .btn", function(e) {
            e.preventDefault();
            e.stopPropagation();

            let _this = $(this);
            let text_id = _this.data('id');

            let text = text_arr.find(function(item) {
                return item.id == text_id;
            });

            _this.closest(".card-body").find(".card-text").text(text.text);
            _this.remove();
        });

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

                            temp = temp.replace(/{{profile_picture}}/g, (recipient.user.profile_picture) ?? 'placeholder.png');
                            temp = temp.replace(/{{name}}/g, recipient.user.name);
                            temp = temp.replace(/{{message}}/g, recipient.message.message);
                            temp = temp.replace(/{{created}}/g, recipient.message.created);
                            temp = temp.replace(/{{userId}}/g, recipient.user.id);
                            $(".convo_body").append(temp);
                        });
                    } else {
                        alert("Something went wrong!");
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