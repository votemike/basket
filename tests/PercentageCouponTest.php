<?php namespace Votemike\Basket\Tests;

use Exception;
use PHPUnit_Framework_TestCase;
use Votemike\Basket\PercentageCoupon;
use Votemike\Money\Money;

class PercentageCouponTest extends PHPUnit_Framework_TestCase{
	public function testPercentageAmountIsDiscounted()
	{
		$gross = new Money(10.00, 'USD');
		$coupon = new PercentageCoupon(20);

		$this->assertEquals(8.00, $coupon->applyTo($gross)->getAmount());
	}


	public function testCouponWithPercentageZeroOrLessThrowsException()
	{
		$this->markTestIncomplete('Need to decide on Exception type and message.');
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Some message');
		new PercentageCoupon(0);
	}

	public function testCouponWithPercentageOverOneHundredThrowsException()
	{
		$this->markTestIncomplete('Need to decide on Exception type and message.');
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Some message');
		new PercentageCoupon(101);
	}
}
