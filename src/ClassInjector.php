<?php
/**
 * Copyright (c) 2015 Robin Appelman <icewind@owncloud.com>
 * This file is licensed under the Licensed under the MIT license:
 * http://opensource.org/licenses/MIT
 */

namespace Icewind\Patcher;

class ClassInjector extends AbstractInjector {

	/**
	 * The classes we inject and their handlers
	 *
	 * @var callable[]
	 */
	private $classes = [];

	/**
	 * Register a class to be replaced
	 *
	 * @param string $class the class the be replaced
	 * @param string $targetClass
	 */
	public function addClass($class, $targetClass) {
		$class = '\\' . ltrim($class, '\\');
		$targetClass = '\\' . ltrim($targetClass, '\\');
		$this->classes[$class] = $targetClass;
	}

	public function injectInCode($code) {
		$this->registerGlobal();
		return array_reduce(array_keys($this->classes), function ($code, $class) {
			return $this->injectClassInCode($code, $class, $this->classes[$class]);
		}, $code);
	}

	/**
	 * Change code to inject a class
	 *
	 * @param string $code
	 * @param string $class
	 * @param string $targetClass
	 * @return string
	 */
	private function injectClassInCode($code, $class, $targetClass) {
		// direct calls
		$search = '/([^a-zA-Z0-9\\_])(' . preg_quote($class) . ')([^a-zA-Z0-9\\_])/i';
		$code = preg_replace($search, '$1' . $targetClass . '$3', $code);

		// use statements
		$search = '/(use\s+)(\\\\?' . preg_quote(trim($class, '\\')) . ')(\s*;)/i';
		$parts = explode('\\', $class);
		$code = preg_replace($search, '$1' . $targetClass . ' as ' . array_pop($parts) . ' $3', $code);

		return $code;
	}
}
