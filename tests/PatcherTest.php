<?php
/**
 * Copyright (c) 2015 Robin Appelman <icewind@owncloud.com>
 * This file is licensed under the Licensed under the MIT license:
 * http://opensource.org/licenses/MIT
 */

namespace Icewind\Patcher\Tests;

use Icewind\Patcher\Patcher;

class PatcherTest extends TestCase {
	public function testPatchNamespace() {
		$instance = new Patcher();
		$instance->patchMethod('microtime', function () {
			return 100;
		});
		$instance->patchForNamespace('Icewind\Patcher\Tests');

		$this->assertEquals(100, microtime());
	}

	public function testAutoPatch() {
		$instance = new Patcher();
		$instance->patchMethod('sleep', function () {
			return 100;
		});
		$instance->whiteListDirectory(__DIR__ . '/data');
		$instance->autoPatch();

		/** @var callable $method */
		$method = include 'data/autoPatchTest.php';
		$this->assertEquals(100, \Auto\Test\test());
		$this->assertEquals(100, $method());
	}

	public function testAutoPatchOnlyOnce() {
		$instance = new Patcher();
		$instance->autoPatch();
		$instance->autoPatch();
	}
}
