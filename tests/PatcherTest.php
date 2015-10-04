<?php
/**
 * Copyright (c) 2015 Robin Appelman <icewind@owncloud.com>
 * This file is licensed under the Licensed under the MIT license:
 * http://opensource.org/licenses/MIT
 */

namespace Icewind\Patcher\Tests;

use Icewind\Patcher\Patcher;

class AutoPatchClassDummy extends \DateTime {
	public function __construct() {
		parent::__construct("1970-01-01 00:00:00 UTC");
	}
}

class PatcherTest extends TestCase {
	/**
	 * @var Patcher
	 */
	private $patcher;

	public function setUp() {
		$this->patcher = new Patcher();
		$this->patcher->whiteListDirectory(__DIR__ . '/data');
		$this->patcher->autoPatch();
	}

	public function testAutoPatchFunction() {
		$this->patcher->patchMethod('sleep', function () {
			return 100;
		});

		/** @var callable $method */
		$method = include 'data/autoPatchFunctionTest.php';
		$this->assertEquals(100, \Auto\Test\test());
		$this->assertEquals(100, $method());
	}

	public function testAutoPatchClass() {
		$this->patcher->patchClass('\DateTime', '\Icewind\Patcher\Tests\AutoPatchClassDummy');

		/** @var AutoPatchClassDummy $class */
		$class = include 'data/autoPatchClassTest.php';
		$this->assertInstanceOf('\Icewind\Patcher\Tests\AutoPatchClassDummy', $class);
		$this->assertEquals(0, $class->getTimestamp());
	}

	public function testAutoPatchOnlyOnce() {
		$instance = new Patcher();
		$instance->autoPatch();
		$instance->autoPatch();
	}
}
