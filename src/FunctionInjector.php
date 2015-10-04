<?php
/**
 * Copyright (c) 2015 Robin Appelman <icewind@owncloud.com>
 * This file is licensed under the Licensed under the MIT license:
 * http://opensource.org/licenses/MIT
 */

namespace Icewind\Patcher;

class FunctionInjector extends AbstractInjector {
	/**
	 * The methods we inject and the handlers for them
	 *
	 * @var callable[]
	 */
	private $methods = [];

	/**
	 * @var string
	 */
	private $functionTemplate;

	public function __construct() {
		parent::__construct();
		$this->functionTemplate = file_get_contents(__DIR__ . '/FunctionInjectTemplate.php');
	}

	/**
	 * Register a method to be injected
	 *
	 * @param string $method
	 * @param callable $callback the handler for the injected method
	 */
	public function addMethod($method, $callback) {
		$this->methods[$method] = $callback;
	}

	/**
	 * Inject all registered methods into a namespace
	 *
	 * @param string $namespace
	 * @throws InjectException
	 */
	public function injectInNamespace($namespace) {
		$this->registerGlobal();
		foreach ($this->methods as $method => $handler) {
			$this->injectMethodInNamespace($namespace, $method);
		}
	}

	/**
	 * Inject a method into a namespace
	 *
	 * @param string $namespace
	 * @param string $method
	 * @throws InjectException
	 */
	protected function injectMethodInNamespace($namespace, $method) {
		$code = str_replace('__NAMESPACE__', $namespace, $this->functionTemplate);
		$code = str_replace('__METHOD__', $method, $code);
		$code = str_replace('__INJECTORID__', $this->injectorId, $code);
		$result = $this->loadCode($code);
		if ($result === false) {
			throw new InjectException('Failed to inject method "' . $method . '" into namespace "' . $namespace . '"');
		}
	}

	private function loadCode($code) {
		if (class_exists('\ParseError')) {
			try {
				return @eval($code);
			} catch (\ParseError $e) {
				return false;
			}
		} else {
			return @eval($code);
		}
	}

	/**
	 * Handle a call to the injected method
	 *
	 * @param string $method the method that was called
	 * @param array $arguments the arguments the method is called with
	 * @return mixed
	 */
	public function handleCall($method, array $arguments) {
		$original = function () use ($method, $arguments) {
			return call_user_func_array('\\' . $method, $arguments);
		};
		$handler = $this->methods[$method];
		return $handler($method, $arguments, $original);
	}
}
