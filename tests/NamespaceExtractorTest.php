<?php
/**
 * Copyright (c) 2015 Robin Appelman <icewind@owncloud.com>
 * This file is licensed under the Licensed under the MIT license:
 * http://opensource.org/licenses/MIT
 */

namespace Icewind\Patcher\Tests;

use Icewind\Patcher\NamespaceExtractor;

class NamespaceExtractorTest extends TestCase {

	private function extractNamespaces($files, $listeners = []) {
		$interceptor = new DummyInterceptor();
		$instance = new NamespaceExtractor($interceptor);
		$instance->registerHook();
		list($hook) = $interceptor->getHooks();

		foreach ($listeners as $listener) {
			$instance->addListener($listener);
		}

		foreach ($files as $file) {
			$code = file_get_contents($file);
			$hook($code);
		}

		return $instance->getNamespaces();
	}

	public function testExtractNoNamespace() {
		$this->assertEquals([], $this->extractNamespaces(['data/noNamespace.php']));
	}

	public function testExtractBasicNamespace() {
		$this->assertEquals(['Foo\Bar'], $this->extractNamespaces(['data/basicNamespace.php']));
	}

	public function testExtractBlockNamespace() {
		$this->assertEquals(['Foo\Bar'], $this->extractNamespaces(['data/blockNamespace.php']));
	}

	public function testExtractMultipleNamespaces() {
		$this->assertEquals(['Foo\Bar', 'Qwerty'], $this->extractNamespaces(['data/multipleNamespaces.php']));
	}

	public function testExtractMultipleBlockNamespaces() {
		$this->assertEquals(['Foo\Bar', 'Qwerty'], $this->extractNamespaces(['data/multipleBlockNamespaces.php']));
	}

	public function testExtractDuplicateNamespaces() {
		$this->assertEquals(['Foo\Bar', 'Qwerty'], $this->extractNamespaces(['data/multipleBlockNamespaces.php', 'data/basicNamespace.php']));
	}

	public function testExtractDuplicateNamespacesListener() {
		$foundNamespaces = [];

		$listener = function ($namespace) use (&$foundNamespaces) {
			$foundNamespaces[] = $namespace;
		};

		$this->extractNamespaces(['data/multipleBlockNamespaces.php', 'data/basicNamespace.php'], [$listener]);

		$this->assertEquals(['Foo\Bar', 'Qwerty'], $foundNamespaces);
	}
}
