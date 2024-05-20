<div class="container">
    <div class="row">
        <div class="d-flex align-items-center justify-content-start" style="gap: 10px;">
            <h1>Conversation</h1>
            <span><?= $this->Html->link(
                        "Back to Messages list",
                        array(
                            "controller" => "messages",
                            "action" => "index"
                        ),
                        array(
                            "class" => "btn btn-warning btn-sm"
                        )
                    ); ?>
            </span>

        </div>
    </div>

    <div class="row">
        <div class="col my-2 p-0">
            <div class="form-inline">
                <label for="search">Search</label>
                <input type="text" id="search" class="form-control form-control-sm ml-2" placeholder="Search for messages">
            </div>
        </div>
    </div>

    <form id="addMessageForm">
        <div class="form-group row">
            <div class="col-10 pl-0">
                <textarea class="form-control" name="message" id=""></textarea>
            </div>
            <div class="col-2 pr-0">
                <button class="btn btn-primary h-100 w-100">Send</button>
            </div>
        </div>
    </form>

    <div class="row convo_body">
        <?php foreach ($messages as $message) : ?>
            <?php if ($message['Message']['recipient_id'] == $_SESSION['Auth']['User']['id']) : ?>
                <div class="col-12 p-0">
                    <div class="card mb-3 w-75">
                        <div class="row no-gutters">
                            <div class="col-2 border-right">
                                <?php echo $this->html->image(
                                    $message['sender']['profile_picture'] ?? 'placeholder.png',
                                    array(
                                        'alt' => 'Image Alt Text',
                                        'class' => 'img-thumbnail border-0', // Example CSS class
                                        'id' => 'my-image', // Example ID
                                        'width' => '150px',
                                        'height' => '150px'
                                    )
                                ); ?>
                            </div>
                            <div class="col-10 d-flex flex-column">
                                <div class="card-body">
                                    <h5 class="card-title"><?= $message['sender']['name'] ?? "Unknown user"; ?></h5>
                                    <p class="card-text"><?= strlen($message['Message']['message']) > 50 ? substr($message['Message']['message'], 0, 50) . "..." : $message['Message']['message'] ?></p>
                                </div>
                                <div class="card-footer mt-auto">
                                    <p class="card-text"><small class="text-muted"><?= date("F d, Y h:i a", strtotime($message['Message']['created']))  ?></small></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else : ?>
                <div class="col-12 pull-right p-0">
                    <div class="card ml-auto mb-3 w-75 text-right">
                        <div class="row no-gutters">
                            <div class="col-10 d-flex flex-column">
                                <div class="card-body">
                                    <div class="position-relative">
                                        <div class="position-absolute" style="z-index: 1000; top: 0; left: 0;">
                                            <button class="btn btn-danger delete-message btn-sm" data-message-id="<?= $message['Message']['id'] ?>">Delete</button>
                                        </div>
                                    </div>
                                    <h5 class="card-title"><?= $message['sender']['name'] ?? "Unknown user"; ?></h5>
                                    <p class="card-text"><?= $message['Message']['message'] ?></p>
                                </div>
                                <div class="card-footer mt-auto">
                                    <p class="card-text"><small class="text-muted"><?= date("F d, Y h:i a", strtotime($message['Message']['created']))  ?></small></p>
                                </div>
                            </div>
                            <div class="col-2 border-left">
                                <?php echo $this->html->image(
                                    $message['sender']['profile_picture'] ?? 'placeholder.png',
                                    array(
                                        'alt' => 'Image Alt Text',
                                        'class' => 'img-thumbnail border-0', // Example CSS class
                                        'id' => 'my-image', // Example ID
                                        'width' => '150px',
                                        'height' => '150px'
                                    )
                                ); ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        <?php endforeach; ?>
    </div>

    <?php if ($this->Paginator->hasNext()) : ?>
        <div class="row justify-content-center mb-2">
            <button class="btn btn-primary btn-sm" id="load-more">Load more</button>
        </div>
    <?php endif; ?>
</div>

<template id="recipient">
    <div class="col-12 p-0">
        <div class="card mb-3 w-75">
            <div class="row no-gutters">
                <div class="col-2 border-right">
                    <img class="img-thumbnail border-0" src="/messageboard/img/{{profile_picture}}">
                </div>
                <div class="col-10">
                    <div class="card-body">
                        <div class="position-absolute" style="z-index: 1000; top: 0; right: 0;">
                            <button class="btn btn-danger delete-message" data-message-id="{{message_id}}">Delete</button>
                        </div>
                        <h5 class="card-title">{{name}}</h5>
                        <p class="card-text">{{msg}}</p>
                    </div>
                    <div class="card-footer">
                        <p class="card-text"><small class="text-muted">{{datetime}}</small></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<template id="sender">
    <div class="col-12 pull-right p-0">
        <div class="card ml-auto mb-3 w-75 text-right">
            <div class="row no-gutters">
                <div class="col-10">
                    <div class="card-body">
                        <div class="position-relative">
                            <div class="position-absolute" style="z-index: 1000; top: 0; left: 0;">
                                <button class="btn btn-danger btn-sm delete-message" data-message-id="{{message_id}}">Delete</button>
                            </div>
                        </div>
                        <h5 class="card-title">{{name}}</h5>
                        <p class="card-text">{{msg}}</p>
                    </div>
                    <div class="card-footer">
                        <p class="card-text"><small class="text-muted">{{datetime}}</small></p>
                    </div>
                </div>
                <div class="col-2 border-left">
                    <img class="img-thumbnail border-0" src="/messageboard/img/{{profile_picture}}">
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    $(document).ready(function() {

        let currentPage = 1;

        $(document).on("click", "#load-more", function() {
            currentPage++;
            $.ajax({
                url: "<?= $this->Html->url(array('controller' => 'messages', 'action' => 'load_messages')) ?>",
                type: "GET",
                data: {
                    page: currentPage,
                    recipient: <?= $recipient ?>
                },
                dataType: "json",
                success: function(response) {
                    if (response.hasNext == false) {
                        $("#load-more").remove();
                    }

                    $.each(response.messages, function(index, message) {
                        if (message.Message.sender_id == <?= $_SESSION['Auth']['User']['id'] ?>) {
                            let template = $("#sender").html();

                        } else {
                            let template = $("#recipient").html();

                        }

                        template = template.replace("{{message_id}}", message.Message.id);
                        template = template.replace("{{name}}", message.sender.name);
                        template = template.replace("{{msg}}", message.Message.message);
                        template = template.replace("{{datetime}}", new Date(message.Message.created).toLocaleDateString("en-US", {
                            year: "numeric",
                            month: "long",
                            day: "numeric",
                            hour: "numeric",
                            minute: "numeric"
                        }));
                        template = template.replace("{{profile_picture}}", message.sender.profile_picture);
                        $(".convo_body").append(template);
                    });
                }
            });
        });

        $("#search").on("keyup", debounce(function() {
            let search = $(this).val().toLowerCase();
            $.ajax({
                url: "<?= $this->Html->url(array('controller' => 'messages', 'action' => 'conversation', $recipient)) ?>",
                data: {
                    search: search
                },
                type: "GET",
                dataType: "json",
                success: function(response) {
                    if (response.status) {
                        $(".convo_body").empty();
                        response.messages.forEach(function(message) {
                            let template = message.Message.sender_id == <?= $_SESSION['Auth']['User']['id'] ?> ? $("#sender").html() : $("#recipient").html();

                            template = template.replace("{{message_id}}", message.Message.id);
                            template = template.replace("{{name}}", message.sender.name);
                            template = template.replace("{{msg}}", message.Message.message);
                            template = template.replace("{{datetime}}", new Date(message.Message.created).toLocaleDateString("en-US", {
                                year: "numeric",
                                month: "long",
                                day: "numeric",
                                hour: "numeric",
                                minute: "numeric"
                            }));
                            template = template.replace("{{profile_picture}}", message.sender.profile_picture);
                            $(".convo_body").append(template);
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

        $(document).on("click", ".delete-message", function() {
            let messageId = $(this).data("message-id");
            let card = $(this).closest(".card");

            if (confirm("Are you sure you want to delete this message?")) {
                $.ajax({
                    url: "<?= $this->Html->url(array('controller' => 'messages', 'action' => 'delete_messages')) ?>",
                    type: "GET",
                    data: {
                        messageId: messageId
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.status) {
                            card.closest(".card").hide("slow", function() {
                                $(this).remove();
                            });
                        } else {
                            alert(response.message);
                        }
                    }
                });
            }


        });

        $("#addMessageForm").on("submit", function(event) {
            event.preventDefault();
            var message = $("textarea").val();
            $.ajax({
                url: "<?= $this->Html->url(array('controller' => 'messages', 'action' => 'conversation_send', $recipient)) ?>",
                type: "POST",
                data: {
                    message: message
                },
                dataType: "json",
                success: function(response) {

                    var message = $("textarea").val();
                    let template = $("#sender").html();

                    template = template.replace("{{message_id}}", response.id);
                    template = template.replace("{{name}}", "<?= $_SESSION['Auth']['User']['name']; ?>");
                    template = template.replace("{{msg}}", message);
                    template = template.replace("{{datetime}}", new Date().toLocaleDateString("en-US", {
                        year: "numeric",
                        month: "long",
                        day: "numeric",
                        hour: "numeric",
                        minute: "numeric"
                    }));
                    template = template.replace("{{profile_picture}}", "<?= $_SESSION['Auth']['User']['profile_picture'] ?>");

                    $(".convo_body").prepend(template);
                    $("textarea").val("");
                }
            });
        });
    });
</script>