<div class="container">
    <div class="row">
        <div class="justify-content-start">
            <h1>Conversation</h1>
            <span><?= $this->Html->link(
                        "Back to Messages list",
                        array(
                            "controller" => "messages",
                            "action" => "index"
                        )
                    ); ?>
            </span>

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
                            <div class="col-10">
                                <div class="card-body">
                                    <h5 class="card-title"><?= $message['sender']['name'] ?? "Unknown user"; ?></h5>
                                    <p class="card-text"><?= strlen($message['Message']['message']) > 50 ? substr($message['Message']['message'], 0, 50) . "..." : $message['Message']['message'] ?></p>
                                </div>
                                <div class="card-footer">
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
                            <div class="col-10">
                                <div class="card-body">
                                    <h5 class="card-title"><?= $message['sender']['name'] ?? "Unknown user"; ?></h5>
                                    <p class="card-text"><?= $message['Message']['message'] ?></p>
                                </div>
                                <div class="card-footer">
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
                        } else {
                            let template = $("#recipient").html();
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
                        }
                    });
                }
            });
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
                success: function(response) {

                    var message = $("textarea").val();
                    var html = '<div class="col-12 pull-right p-0"><div class="card ml-auto mb-3 w-75 text-right"><div class="row no-gutters"><div class="col-10"><div class="card-body"><h5 class="card-title"><?= $_SESSION['Auth']['User']['name'] ?? "Unknown user"; ?></h5><p class="card-text">' + message + '</p></div><div class="card-footer"><p class="card-text"><small class="text-muted">' + new Date().toLocaleString() + '</small></p></div></div><div class="col-2 border-left"><?php echo $this->html->image($_SESSION['Auth']['User']['profile_picture'] ?? 'placeholder.png', array('alt' => 'Image Alt Text', 'class' => 'img-thumbnail border-0', 'id' => 'my-image', 'width' => '150px', 'height' => '150px')); ?></div></div></div></div>';
                    $(".convo_body").prepend(html);
                    $("textarea").val("");
                }
            });
        });
    });
</script>