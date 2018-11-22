<?php

namespace Backend\Controllers;

use Backend\Abstraction\Controllers;
use Backend\Abstraction\Collection;

class Users extends Controllers {

    public function postLogin() {
        /* @var $user \Models\User */
        $userCollection = Collection::instance(\Collections::USERS);

        $user = $userCollection->getOne([
            'username' => $this->post->username
        ]);

        if ($user->valid()) {

            $salted = $user->salt . $this->post->password;
            $checkPassword = sha1($salted);

            if ($user->password == $checkPassword) {

                $token = new \Models\Token([
                    'user' => $user->id
                ]);

                $token->save();

                if (!$token->valid()) {
                    $this->endMessage = 'Token generating failed';
                    $this->endStatus = 500;
                }

                $logged = [
                    'id' => $user->id,
                    'username' => $user->username,
                    'firstname' => $user->firstname,
                    'lastname' => $user->lastname,
                    'facebookid' => $user->facebookid,
                    'email' => $user->email,
                    'gender' => $user->gender,
                    'token' => $token->uniqueId
                ];

                $user->token = $token->uniqueId;
                $user->save();

                $_SESSION['logged'] = $logged;

                $this->endMessage = $logged;
                $this->endStatus = 200;
            } else {
                $this->endMessage = 'Username or password is not valid';
                $this->endStatus = 401;
            }
        } else {
            $this->endMessage = 'Username is not registered';
            $this->endStatus = 404;
        }
    }

    public function postRegister() {
        if ($this->post->password != $this->post->repassword) {
            $this->endMessage = 'Passwords doesn\'t match.';
            $this->endStatus = 401;
        }
        /* @var $userEmail \Models\User */
        $userEmail = $this->userHelper->getOne([
            'email' => $this->post->email
        ]);

        if ($userEmail->valid()) {
            $this->endMessage = 'Email already in use';
            $this->endStatus = 403;
        }
        /* @var $userUsername \Models\User */
        $userUsername = $this->userHelper->getOne([
            'username' => $this->post->username
        ]);

        if ($userUsername->valid()) {
            $this->endMessage = 'Username already in use';
            $this->endStatus = 200;
        }

        $saveData = (array) $this->post;
        $saveData['status'] = \Utils::randomString([
                    \Utils::RANDOM_STRING_LENGHT => 64,
                    \Utils::RANDOM_STRING_CHAR_LEVEL => 7
        ]);

        $saveData['salt'] = \Utils::randomString([
                    \Utils::RANDOM_STRING_LENGHT => 12,
                    \Utils::RANDOM_STRING_CHAR_LEVEL => 7
        ]);

        $salted = $saveData['salt'] . $this->post->password;
        $saveData['password'] = sha1($salted);

        $user = new \Models\User($saveData);
        $user->save();

        if ($user->valid()) {
            $this->endMessage = $user->id;
            $this->endStatus = 200;
        }
    }

    public function postLogout() {
        
        $token = new \Models\Token([]);
        $token->get([
            'uniqueid' => $this->post->token_id
        ]);

        $token->created = -1;
        $token->save();

        $redis = \RedisClient::instance();

        $redis->hDel('tokens', $token->uniqueId);

        if (!$token->valid() && !$redis->hExists('tokens', $token->uniqueId)) {
            $this->endMessage = 'Logged out';
            $this->endStatus = 200;
        } else {
            $this->endMessage = 'Error while removing token';
            $this->endStatus = 500;
        }
    }

    public static function logged() {
        $isSet = (isset($_SESSION['logged']) && is_array($_SESSION['logged']));
        $u = ($isSet ? $_SESSION['logged'] : []);
        $user = new \Models\User($u);
        $user->get();
        return ($user->valid() ? $user : null);
    }

}
