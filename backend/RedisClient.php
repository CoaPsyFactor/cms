<?php

class RedisClient {
	static $instance;

	public static function instance() {
		if (!self::$instance) {
			$redis = new Redis();
			$redis->connect('127.0.0.1');

			self::$instance = $redis;
		}

		return self::$instance;
	}
}