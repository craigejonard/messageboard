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

    //beforeSave, get IP address of user and place it on created_ip field and also updates the modified_ip field when updating the record.
    public function beforeSave($options = array())
    {
        if (isset($this->data[$this->alias]['message'])) {
            $this->data[$this->alias]['message'] = htmlspecialchars($this->data[$this->alias]['message']);
        }
        return true;
    }
}
