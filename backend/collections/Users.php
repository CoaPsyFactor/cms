<?php

namespace Backend\Collections;

use Backend\Abstraction\Collection;

class Users extends Collection {
    protected $model = \Models::USER;
    
    public function getUsernameById($userId) {
        if (!$this[$userId]) {
            $this[$userId] = $this->getOne(['id' => (int) $userId]);
        }

        return $this[$userId]->username;
    }
}
