<?php
/**
 * Copyright (c) 2015 Robin Appelman <icewind@owncloud.com>
 * This file is licensed under the Licensed under the MIT license:
 * http://opensource.org/licenses/MIT
 */

namespace Icewind\Patcher;

use Icewind\Interceptor\Interceptor;

class NamespaceExtractor {
	/**
	 * @var Interceptor
	 */
	private $interceptor;

	/**
	 * @var string[]
	 */
	private $namespaces = [];

	/**
	 * @var callable[]
	 */
	private $listeners = [];

	/**
	 * NamespaceExtractor constructor.
	 *
	 * @param Interceptor $interceptor
	 */
	public function __construct(Interceptor $interceptor) {
		$this->interceptor = $interceptor;
	}

	/**
	 * Add a listener which is to be called for every new namespace
	 *
	 * @param callable $callback
	 */
	public function addListener($callback) {
		$this->listeners[] = $callback;
	}

	/**
	 * Add the namespace extractor hook to the interceptor
	 */
	public function registerHook() {
		$regex = '/namespace\s+([^;{\s]+)/i';
		$this->interceptor->addHook(function ($code) use ($regex) {
			$matches = [];
			preg_match_all($regex, $code, $matches);
			foreach ($matches[1] as $namespace) {
				$this->handleNamespace($namespace);
			}
		});
	}

	private function handleNamespace($namespace) {
		if (!in_array($namespace, $this->namespaces)) {
			$this->newNamespace($namespace);
		}
	}

	private function newNamespace($namespace) {
		$this->namespaces[] = trim($namespace, '\\');
		foreach ($this->listeners as $listener) {
			$listener($namespace);
		}
	}

	/**
	 * Get all namespaces extracted so far
	 *
	 * @return string[]
	 */
	public function getNamespaces() {
		return $this->namespaces;
	}
}
