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

	/**
	 * @var bool
	 */
	private $autoPatchEnabled = false;

	/**
	 * Patcher constructor.
	 *
	 * @param Interceptor|null $interceptor
	 */
	public function __construct($interceptor = null) {
		$this->functionInjector = new FunctionInjector();
		$this->classInjector = new ClassInjector();
		if ($interceptor instanceof Interceptor) {
			$this->interceptor = $interceptor;
		} else {
			$this->interceptor = new Interceptor();
		}
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
	 * Add a directory to the autoPatch white list
	 *
	 * @param string $path
	 */
	public function whiteListDirectory($path) {
		$this->interceptor->addWhiteList($path);
	}

	/**
	 * Add a directory to the autoPatch black list
	 *
	 * @param string $path
	 */
	public function blackListDirectory($path) {
		$this->interceptor->addBlackList($path);
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

	/**
	 * @return Interceptor
	 */
	public function getInterceptor() {
		return $this->interceptor;
	}

	public function __destruct() {
		$this->interceptor->tearDown();
	}
}
