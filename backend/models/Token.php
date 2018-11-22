<?php

namespace Backend\Models;

use Backend\Abstraction\Model;

class Token extends Model {
	public $user;
	protected $table = \Tables::TABLE_TOKENS;
	protected $redis = true;

    protected function _extract(&$data) {
    	if ($this->user) {
    		$data['user'] = $this->user;
    	}
    }

    protected function _build(array $data) {
    	if (isset ($data['user'])) {
    		$this->user = (int) $data['user'];
    	}
    }

    public function valid() {
		return $this->created + 86400 > time();
	}
}