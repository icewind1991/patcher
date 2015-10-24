<?php
/**
 * Copyright (c) 2015 Robin Appelman <icewind@owncloud.com>
 * This file is licensed under the Licensed under the MIT license:
 * http://opensource.org/licenses/MIT
 */

namespace Icewind\Patcher\Tests\IncludePath;

use Icewind\Patcher\Patcher;
use Icewind\Patcher\Tests\TestCase;

class IncludePathTest extends TestCase {
	/**
	 * @var Patcher
	 */
	private $patcher;

	public function setUp() {
		if ($this->patcher) {
			$this->patcher->__destruct();
			$this->patcher = null;
		}
		$this->patcher = new Patcher();
		$this->patcher->whiteListDirectory(dirname(__DIR__) . '/data');
		$this->patcher->autoPatch();
	}

	public function tearDown() {
		$this->patcher->__destruct();
		$this->patcher = null;
	}

	public function testAutoPatchFunction() {
		$this->patcher->patchMethod('sleep', function () {
			return 100;
		});

		/** @var callable $method */
		$method = include '../data/includePathTest.php';
		$this->assertEquals(100, \Auto\Test\Path\test());
		$this->assertEquals(100, $method());
	}
}
