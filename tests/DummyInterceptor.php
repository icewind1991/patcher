<?php
/**
 * Copyright (c) 2015 Robin Appelman <icewind@owncloud.com>
 * This file is licensed under the Licensed under the MIT license:
 * http://opensource.org/licenses/MIT
 */

namespace Icewind\Patcher\Tests;

use Icewind\Interceptor\Interceptor;

class DummyInterceptor extends Interceptor {
	protected $hooks;

	public function addHook(callable $hook) {
		$this->hooks[] = $hook;
	}

	/**
	 * @return callable[]
	 */
	public function getHooks() {
		return $this->hooks;
	}
}
