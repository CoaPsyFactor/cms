<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/*
 * Id - Unique user id
 * Email - User email
 * Username - User username
 * Password - User password
 * Gender - User sex
 * First Name
 * Last Name
 * Salt - Salt for password hash
 * Status - Varchar that define is user activated
 */

namespace Backend\Models;

use Backend\Abstraction\Model;

class User extends Model {

    public $username = null;
    public $facebookid = null;
    public $email = null;
    public $firstname = null;
    public $lastname = null;
    public $password = null;
    public $salt = null;
    public $gender = 0;
    public $status = null;
    public $token = null;
    protected $table = \Tables::TABLE_USERS;

    protected function _extract(&$data) {
        if (!is_null($this->email)) {
            $data['email'] = $this->email;
        }

        if (!is_null($this->firstname)) {
            $data['firstname'] = $this->firstname;
        }

        if (!is_null($this->lastname)) {
            $data['lastname'] = $this->lastname;
        }

        if (!is_null($this->password)) {
            $data['password'] = $this->password;
        }

        if (!is_null($this->salt)) {
            $data['salt'] = $this->salt;
        }

        if ($this->gender) {
            $data['gender'] = $this->gender;
        }

        if (!is_null($this->status)) {
            $data['status'] = $this->status;
        }

        if (!is_null($this->username)) {
            $data['username'] = $this->username;
        }

        if (!is_null($this->facebookid)) {
            $data['facebookid'] = $this->facebookid;
        }

        if (!is_null($this->token)) {
            $data['token'] = $this->token;
        }
    }

    public function valid() {
        return $this->uniqueId;
    }

    protected function _build(array $data) {
        if (!empty($data['email'])) {
            $this->email = (filter_var($data['email'], FILTER_VALIDATE_EMAIL) ? $data['email'] : null);
        }

        if (!empty($data['username'])) {
            $this->username = (strlen($username = $data['username']) ? $username : null);
        }

        if (!empty($data['firstname'])) {
            $this->firstname = (strlen($firstname = $data['firstname']) ? $firstname : null);
        }
        if (!empty($data['lastname'])) {
            $this->lastname = (strlen($lastname = $data['lastname']) ? $lastname : null);
        }
        if (!empty($data['password'])) {
            $this->password = (strlen($password = $data['password']) ? $password : null);
        }
        if (!empty($data['salt'])) {
            $this->salt = (strlen($salt = $data['salt']) ? $salt : null);
        }
        if (!empty($data['gender'])) {
            $this->gender = (strlen($gender = $data['gender']) ? $gender : null);
        }
        if (!empty($data['status'])) {
            $this->status = (strlen($status = $data['status']) ? $status : null);
        }

        if (!empty($data['facebookid'])) {
            $this->facebookid = is_numeric($data['facebookid']) ? (int) $data['facebookid'] : null;
        }

        if (!empty($data['token'])) {
            $this->token = $data['token'];
        }
    }

}
