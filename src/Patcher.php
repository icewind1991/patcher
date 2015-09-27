<?php
/**
 * Copyright (c) 2015 Robin Appelman <icewind@owncloud.com>
 * This file is licensed under the Licensed under the MIT license:
 * http://opensource.org/licenses/MIT
 */

namespace Icewind\Patcher;

use Icewind\Interceptor\Interceptor;

class Patcher {
	/**
	 * @var Injector
	 */
	private $injector;

	/**
	 * @var Interceptor
	 */
	private $interceptor;

	/**
	 * @var NamespaceExtractor
	 */
	private $namespaceExtractor;

	private $autoPatchEnabled = false;

	/**
	 * Patcher constructor.
	 */
	public function __construct() {
		$this->injector = new Injector();
		$this->interceptor = new Interceptor();
		$this->namespaceExtractor = new NamespaceExtractor($this->interceptor);
	}

	/**
	 * Patch a method
	 *
	 * @param string $method
	 * @param callable $handler
	 */
	public function patchMethod($method, $handler) {
		$this->injector->addMethod($method, $handler);
	}

	/**
	 * Apply all patched methods to a namespace
	 *
	 * @param string $namespace
	 */
	public function patchForNamespace($namespace) {
		$this->injector->injectInNamespace($namespace);
	}

	/**
	 * Add a directory to the autoPatch whitelist
	 *
	 * @param string $path
	 */
	public function whiteListDirectory($path) {
		$this->interceptor->addWhiteList($path);
	}

	/**
	 * Enable automatic patching for all namespaces defined by files included from this point
	 *
	 * Only files within a whitelist directory will be auto patched
	 */
	public function autoPatch() {
		if ($this->autoPatchEnabled) {
			return;
		}
		$this->autoPatchEnabled = true;
		$this->namespaceExtractor->addListener([$this->injector, 'injectInNamespace']);
		$this->namespaceExtractor->registerHook();
		$this->interceptor->setUp();
	}
}
