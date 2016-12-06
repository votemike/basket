<?php namespace Votemike\Basket\Tests;

use InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use Votemike\Basket\PercentageCoupon;
use Votemike\Money\Money;

class PercentageCouponTest extends PHPUnit_Framework_TestCase {

	public function testPercentageAmountIsDiscounted()
	{
		$gross = new Money(10.024, 'USD');
		$coupon = new PercentageCoupon(20);

		$this->assertEquals(2.00, $coupon->getDiscount($gross)->getAmount());
	}

	public function testCouponWithPercentageZeroOrLessThrowsException()
	{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('Percentage must be a value between 0 and 100');
		new PercentageCoupon(0);
	}

	public function testCouponWithPercentageOverOneHundredThrowsException()
	{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('Percentage must be a value between 0 and 100');
		new PercentageCoupon(100.1);
	}
}
