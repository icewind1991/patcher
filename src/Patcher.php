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
	 * @var FunctionInjector
	 */
	private $functionInjector;

	/**
	 * @var ClassInjector
	 */
	private $classInjector;

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
		$this->functionInjector = new FunctionInjector();
		$this->classInjector = new ClassInjector();
		$this->interceptor = new Interceptor();
		$this->namespaceExtractor = new NamespaceExtractor($this->interceptor);
	}

	/**
	 * Patch a method
	 *
	 * @param string $method the name of the function to be patched
	 * @param callable $handler the function that will handle any calls to the patched function
	 */
	public function patchMethod($method, $handler) {
		$this->functionInjector->addMethod($method, $handler);
	}

	/**
	 * Patch a class
	 *
	 * @param string $class the name of the class to be replaced
	 * @param string $replacement the class name for the replacement class
	 */
	public function patchClass($class, $replacement) {
		$this->classInjector->addClass($class, $replacement);
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
		$this->namespaceExtractor->addListener([$this->functionInjector, 'injectInNamespace']);
		$this->namespaceExtractor->registerHook();
		$this->interceptor->addHook([$this->classInjector, 'injectInCode']);
		$this->interceptor->setUp();
	}
}
