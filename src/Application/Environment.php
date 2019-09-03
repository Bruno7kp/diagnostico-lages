<?php


namespace App\Application;


class Environment {

	static function inDevelopment(): bool {
		return getenv('DEV') === 'dev';
	}

	static function inProduction(): bool  {
		return getenv('ENV') === 'prod';
	}
}