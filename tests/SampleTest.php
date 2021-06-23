<?php
declare(strict_types = 1);

use PHPUnit\Framework\TestCase;

/**
 * Class SampleTest
 */
class SampleTest extends TestCase {
	public function testSample():void {
		$stack = [];
		self::assertCount(0, $stack);

		$stack[] = 'foo';
		self::assertSame('foo', $stack[count($stack) - 1]);
		self::assertCount(1, $stack);

		self::assertSame('foo', array_pop($stack));
		self::assertCount(0, $stack);

	}
}