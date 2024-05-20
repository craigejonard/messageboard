<style>
    .wrapper {
        height: 100vh;
    }
</style>

<div class="container d-flex justify-content-center align-items-center h-100">
    <div class="col-md-6">
        <div class="row justify-content-center">
            <h1>Thank you for registering!</h1>
        </div>
        <div class="row justify-content-center">
            <?php
            echo $this->Html->link(
                "Proceed to homepage",
                array(
                    "controller" => "messages",
                    "action" => "index"
                ),
                array(
                    "class" => "btn btn-primary"
                )
            );
            ?>
        </div>
    </div>




</div>