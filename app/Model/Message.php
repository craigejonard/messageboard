<?php
App::uses('AppModel', 'Model');
class Message extends AppModel
{
    public $validate = array(
        "message" => array(
            "required" => array(
                "rule" => "notBlank",
                "message" => "Please input your message."
            )
        ),
    );
}
