<?php
App::uses('AppModel', 'Model');
class User extends AppModel
{
	public $validate = array(
		'name' => array(
			'required' => array(
				'rule' => 'notBlank',
				'message' => 'Please input your name.'
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'between' => array(
				'rule' => array('between', 5, 20),
				'message' => 'Name must be between 5 to 20 characters.'
			)
		),
		'gender' => array(
			'required' => array(
				'rule' => 'notBlank',
				'message' => 'Please input your gender',
			)
		),
		'email' => array(
			'email' => array(
				'rule' => array('email'),
				'message' => 'Please input a valid email.'
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'required' => array(
				'rule' => 'notBlank',
				'message' => 'Please input your email.'
			),
			'unique' => array(
				'rule' => 'isUnique',
				'message' => 'This email has already been taken.'
			)
		),
		'password' => array(
			'required' => array(
				'rule' => 'notBlank',
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'confirm_password' => array(
			'required' => array(
				'rule' => "notBlank",
				'message' => "Please confirm your password"
			),
			'compare' => array(
				'rule' => array('comparePasswords', 'password'),
				'message' => 'Passwords do not match'
			)
		),
		'profile_picture' => array(
			'valid' => array(
				'rule' => array('validateImage'),
				'message' => 'Please upload a valid image.'
			),
			'fileSize' => array(
				'rule' => array('fileSize', '<=', '1MB'),
				'message' => 'Image must be less than 1MB.'
			),
			'fileExtension' => array(
				'rule' => array('fileExtension', array('jpg', 'jpeg', 'png', 'gif')),
				'message' => 'Please upload a valid image file.'
			)

		)
	);

	public function beforeSave($options = array())
	{
		if (isset($this->data[$this->alias]['password'])) {
			$this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password']);
		}

		return true;
	}

	public function comparePasswords($check, $field)
	{
		return $this->data[$this->alias][$field] === current($check);
	}
}
