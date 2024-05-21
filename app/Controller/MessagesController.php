<?php

class MessagesController extends AppController
{
    public $components = array('Paginator', 'Flash');

    public function index()
    {
        $search = $this->request->query('search') ?? "";

        // Define the raw SQL query
        $sql = "
            SELECT user.id, user.profile_picture, user.name, message.id, message.message, message.created
            FROM 
                (SELECT IF(recipient_id = {$this->Auth->user('id')}, sender_id, recipient_id) AS other_user
                FROM messages
                WHERE messages.status = 1 AND (recipient_id = {$this->Auth->user('id')} OR sender_id = {$this->Auth->user('id')})
                GROUP BY other_user) AS subquery
            LEFT JOIN users user ON subquery.other_user = user.id
            JOIN messages message ON message.id = (
                SELECT m2.id
                FROM messages m2
                WHERE (m2.sender_id = user.id OR m2.recipient_id = user.id) AND m2.status = 1 AND m2.message LIKE '%$search%'
                ORDER BY m2.created DESC
                LIMIT 1
            )
            GROUP BY user.id
        ";


        // Execute the query
        $results = $this->Message->query($sql);

        // Pass the results to the view if needed
        $this->set('recipients', $results);

        if ($this->request->is('ajax')) {
            $this->autoRender = false;

            die(json_encode(
                array(
                    'status' => true,
                    'recipients' => $results
                )
            ));
        }
    }

    public function delete_messages()
    {
        if ($this->request->is("ajax") === false) {
            throw new MethodNotAllowedException();
        }

        $this->autoRender = false;

        if ($this->request->query("userId")) {
            $recipient = $this->request->query("userId");
            //soft delete
            $delete = $this->Message->updateAll(
                array('status' => 0),
                array(
                    'OR' => array(
                        array(
                            'Message.sender_id' => $this->Auth->user('id'),
                            'Message.recipient_id' => $recipient
                        ),
                        array(
                            'Message.sender_id' => $recipient,
                            'Message.recipient_id' => $this->Auth->user('id')
                        )
                    )
                )
            );

            if ($delete) {
                echo json_encode(
                    array(
                        'status' => true,
                        'message' => 'Messages deleted successfully'
                    )
                );
            } else {
                echo json_encode(
                    array(
                        'status' => false,
                        'message' => 'Messages could not be deleted',
                    )
                );
            }
        } elseif ($this->request->query("messageId")) {
            $message = $this->request->query("messageId");
            $this->Message->id = $message;
            $delete = $this->Message->save(array(
                'status' => 0
            ));

            if ($delete) {
                echo json_encode(
                    array(
                        'status' => true,
                        'message' => 'Message deleted successfully'
                    )
                );
            } else {
                echo json_encode(
                    array(
                        'status' => false,
                        'message' => 'Message could not be deleted',
                    )
                );
            }
        } else {
            echo json_encode(
                array(
                    'status' => false,
                    'message' => 'Message could not be deleted',
                )
            );
        }
    }

    public function conversation($recipient)
    {
        $search = $this->request->query('search') ?? "";

        $this->Paginator->settings = array(
            'fields' => array('Message.*', 'sender.id', 'sender.name', 'sender.profile_picture', 'recipient.id', 'recipient.name', 'recipient.profile_picture'),
            'joins' => array(
                array(
                    'table' => 'users',
                    'alias' => 'sender',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'sender.id = Message.sender_id'
                    )
                ),
                array(
                    'table' => 'users',
                    'alias' => 'recipient',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'recipient.id = Message.recipient_id'
                    )
                )
            ),
            'conditions' => array(
                "status" => 1,
                "message LIKE" => "%$search%",
                'OR' => array(
                    array(
                        'Message.sender_id' => $this->Auth->user('id'),
                        'Message.recipient_id' => $recipient
                    ),
                    array(
                        'Message.sender_id' => $recipient,
                        'Message.recipient_id' => $this->Auth->user('id')
                    )
                )
            ),
            'order' => array('Message.created' => 'DESC'),
            'limit' => 10  // Adjust the limit as needed
        );

        $messages = $this->Paginator->paginate('Message');
        $this->set('recipient', $recipient); // Pass the recipient ID to the view (optional
        $this->set('messages', $messages);

        if ($this->request->is('ajax')) {
            $this->autoRender = false;

            die(json_encode(
                array(
                    'status' => true,
                    'messages' => $messages,
                    'hasNext' => $this->request->params['paging']['Message']['nextPage']
                )
            ));
        }
    }

    public function load_messages()
    {
        if ($this->request->is("ajax") === false) {
            throw new MethodNotAllowedException();
        }

        $this->autoRender = false;

        $page = $this->request->query('page') ?? 1;
        $recipient = $this->request->query('recipient');
        $this->Paginator->settings = array(
            'fields' => array('Message.*', 'sender.id', 'sender.name', 'sender.profile_picture', 'recipient.id', 'recipient.name', 'recipient.profile_picture'),
            'joins' => array(
                array(
                    'table' => 'users',
                    'alias' => 'sender',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'sender.id = Message.sender_id'
                    )
                ),
                array(
                    'table' => 'users',
                    'alias' => 'recipient',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'recipient.id = Message.recipient_id'
                    )
                )
            ),
            'conditions' => array(
                'OR' => array(
                    array(
                        'Message.sender_id' => $this->Auth->user('id'),
                        'Message.recipient_id' => $recipient
                    ),
                    array(
                        'Message.sender_id' => $recipient,
                        'Message.recipient_id' => $this->Auth->user('id')
                    )
                )
            ),
            'order' => array('Message.created' => 'DESC'),
            'limit' => 10,  // Adjust the limit as needed
            'page' => $page
        );

        $messages = $this->Paginator->paginate('Message');

        die(json_encode(array(
            'status' => true,
            'messages' => $messages,
            'hasNext' => $this->request->params['paging']['Message']['nextPage']
        )));
    }

    public function conversation_send($recipient)
    {
        if ($this->request->is('post')) {
            $this->Message->create();

            $this->autoRender = false;

            $userId = $this->Auth->user('id');
            $this->request->data['Message']['message'] = $this->request->data['message'];
            $this->request->data['Message']['sender_id'] = $userId;
            $this->request->data['Message']['recipient_id'] = $recipient;

            if ($this->Message->save($this->request->data)) {

                echo json_encode(
                    array(
                        'status' => true,
                        'message' => 'Message sent successfully',
                        'id' => $this->Message->id
                    )
                );
            } else {
                echo json_encode(
                    array(
                        'status' => false,
                        'message' => 'Message could not be sent',
                    )
                );
            }
        }
    }

    public function add()
    {
        if ($this->request->is('post')) {
            $this->Message->create();

            $userId = $this->Auth->user('id');
            $this->request->data['Message']['sender_id'] = $userId;

            if ($this->Message->save($this->request->data)) {
                $this->autoRender = false;

                echo json_encode(
                    array(
                        'status' => true,
                        'message' => 'Message sent successfully'
                    )
                );
            } else {
                echo json_encode(
                    array(
                        'status' => false,
                        'message' => 'Message could not be sent',
                    )
                );
            }
        }
    }
}
