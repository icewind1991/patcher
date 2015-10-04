<?php
/**
 * Copyright (c) 2015 Robin Appelman <icewind@owncloud.com>
 * This file is licensed under the Licensed under the MIT license:
 * http://opensource.org/licenses/MIT
 */

namespace Icewind\Patcher\Tests;

use Icewind\Patcher\ClassInjector;

class ReplacementDummy {
	const ISO8601 = DATE_ATOM;

	public $args;

	public static $staticArgs;

	public function __construct() {
		$this->args = func_get_args();
	}

	public static function createFromFormat() {
		self::$staticArgs = func_get_args();
		return new \DateTime("1970-01-01 00:00:00 UTC");
	}
}

class ClassInjectorTest extends TestCase {
	public function testBasicClass() {
		$injector = new ClassInjector();
		$injector->addClass('\DateTime', '\Icewind\Patcher\Tests\ReplacementDummy');
		/** @var ReplacementDummy $class */
		$class = $this->runFileWithInjector('basicClass.php', $injector);
		$this->assertInstanceOf('\Icewind\Patcher\Tests\ReplacementDummy', $class);
	}

	public function testConstructorArguments() {
		$injector = new ClassInjector();
		$injector->addClass('\DateTime', '\Icewind\Patcher\Tests\ReplacementDummy');
		/** @var ReplacementDummy $class */
		$class = $this->runFileWithInjector('basicClass.php', $injector);
		$this->assertEquals([], $class->args);

		$class = $this->runFileWithInjector('constructorArguments.php', $injector);
		$this->assertEquals([100], $class->args);
	}

	public function testUseClass() {
		$injector = new ClassInjector();
		$injector->addClass('\DateTime', '\Icewind\Patcher\Tests\ReplacementDummy');
		/** @var ReplacementDummy $class */
		$class = $this->runFileWithInjector('useClass.php', $injector);
		$this->assertInstanceOf('\Icewind\Patcher\Tests\ReplacementDummy', $class);
	}

	public function testStaticClass() {
		$injector = new ClassInjector();
		$injector->addClass('\DateTime', '\Icewind\Patcher\Tests\ReplacementDummy');
		/** @var \DateTime $class */
		$class = $this->runFileWithInjector('staticMethod.php', $injector);

		$this->assertEquals(0, $class->getTimestamp());
	}

	public function testClassConstant() {
		$injector = new ClassInjector();
		$injector->addClass('\DateTime', '\Icewind\Patcher\Tests\ReplacementDummy');

		$result = $this->runFileWithInjector('classConstant.php', $injector);
		$this->assertEquals(DATE_ATOM, $result);
	}

	private function runFileWithInjector($file, ClassInjector $injector) {
		$code = file_get_contents(__DIR__ . '/data/' . $file);
		$code = $injector->injectInCode($code);
		return $this->runCode($code);
	}

	private function runCode($code) {
		$file = $this->tempNam('.php');
		file_put_contents($file, $code);
		$result = include $file;
		unlink($file);
		return $result;
	}
}
