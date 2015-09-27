<?php
/**
 * Copyright (c) 2015 Robin Appelman <icewind@owncloud.com>
 * This file is licensed under the Licensed under the MIT license:
 * http://opensource.org/licenses/MIT
 */

namespace Icewind\Patcher\Tests;

use Icewind\Patcher\Injector;

class InjectorTest extends TestCase {
	public function testBasicInject() {
		$instance = new Injector();
		$instance->addMethod('time', function () {
			return 100;
		});
		$instance->injectInNamespace('Icewind\Patcher\Tests');

		$this->assertEquals(100, time());
	}

	public function testUseOriginal() {
		$instance = new Injector();
		$instance->addMethod('count', function ($method, $arguments, $original) {
			return $original() + 1;
		});
		$instance->injectInNamespace('Icewind\Patcher\Tests');

		$this->assertEquals(3, count([1, 2]));
	}

	public function testHandlerParams() {
		$instance = new Injector();
		$instance->addMethod('array_push', function ($method, $arguments, $original) {
			$this->assertEquals('array_push', $method);
			$this->assertEquals([[1], 2], $arguments);
			return [1, 3];
		});
		$instance->injectInNamespace('Icewind\Patcher\Tests');

		$this->assertEquals([1, 3], array_push([1], 2));
	}

	/**
	 * @expectedException \Icewind\Patcher\InjectException
	 */
	public function testInvalidMethodName() {
		$instance = new Injector();
		$instance->addMethod('1', function () {
			return true;
		});
		$instance->injectInNamespace('Icewind\Patcher\Tests');
	}
}
