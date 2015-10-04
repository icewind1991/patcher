<?php
/**
 * Copyright (c) 2015 Robin Appelman <icewind@owncloud.com>
 * This file is licensed under the Licensed under the MIT license:
 * http://opensource.org/licenses/MIT
 */

namespace Icewind\Patcher;

abstract class AbstractInjector {
	/**
	 * @var string
	 */
	protected $injectorId;

	public function __construct() {
		$this->injectorId = uniqid();
	}

	/**
	 * register this injector as global so we can access it from inside the injected method
	 */
	protected function registerGlobal() {
		if (!isset($GLOBALS[$this->injectorId])) {
			$GLOBALS[$this->injectorId] = $this;
		}
	}
}
