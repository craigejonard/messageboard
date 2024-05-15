<?php

class UsersController extends AppController
{
    public $components = array('Paginator', 'Flash');

    public function beforeFilter()
    {
        $this->Auth->allow('add');
    }

    public function index()
    {
    }

    public function login()
    {
        if ($this->request->is('post')) {
            if ($this->Auth->login()) {
                return $this->redirect($this->Auth->redirectUrl());
            } else {
                $this->Flash->error('Invalid Username or Password');
            }
        }
    }

    public function logout()
    {
        $this->redirect($this->Auth->logout());
    }

    public function add()
    {
        if ($this->request->is("post")) {
            $this->User->create();

            if ($this->User->save($this->request->data)) {
                die(json_encode(
                    array(
                        "status" => true,
                        "message" => "User has been registered!"
                    )
                ));
            } else {
                die(json_encode(
                    array(
                        "status" => false,
                        "message" => $this->User->validationErrors
                    )
                ));
            }
        }
    }

    public function profile()
    {
        $loggedInUser = $this->Auth->user();

        //format birthdate into MONTH DAY, YEAR
        if (!empty($loggedInUser)) {
            $loggedInUser['birthdate'] = date('F d, Y', strtotime($loggedInUser['birthdate']));

            $this->set('user', $loggedInUser);
        }


        if ($this->request->is('post')) {
            $userId = $loggedInUser['id'];

            $this->User->id = $userId;

            $request = $this->formatRequest($this->request->data);

            if ($this->User->save($request)) {
                $updatedUserData = $this->User->findById($userId);

                $this->Auth->login($updatedUserData['User']);

                $this->autoRender = false;
                echo json_encode([
                    "status" => true,
                    "message" => "Profile has been updated!"
                ]);
                return;
            } else {
                $this->autoRender = false;
                echo json_encode([
                    "status" => false,
                    "message" => $this->User->validationErrors
                ]);
                return;
            }
        }
    }

    public function uploadPictureToServer()
    {
        $loggedInUser = $this->Auth->user();

        if ($this->request->is('post')) {
            $userId = $loggedInUser['id'];

            $this->User->id = $userId;

            $request = $this->request->data;

            $profilePicture = $request['profile_picture'];

            $profilePictureName = $profilePicture['name'];
            $profilePictureTmpName = $profilePicture['tmp_name'];

            $profilePicturePath = WWW_ROOT . 'img' . DS . 'profile_pictures' . DS . $profilePictureName;

            if (move_uploaded_file($profilePictureTmpName, $profilePicturePath)) {
                $this->User->saveField('profile_picture', $profilePictureName);

                $this->autoRender = false;
                echo json_encode([
                    "status" => true,
                    "message" => "Profile picture has been updated!"
                ]);
                return;
            } else {
                $this->autoRender = false;
                echo json_encode([
                    "status" => false,
                    "message" => "Failed to upload profile picture"
                ]);
                return;
            }
        }
    }

    public function formatRequest($request)
    {
        //change birthdate format from M dd, yy to yyyy-mm-dd

        $request['birthdate'] = date('Y-m-d', strtotime($request['birthdate']));
        return $request;
    }
}
