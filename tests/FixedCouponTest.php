<?php namespace Votemike\Basket\Tests;

use DomainException;
use PHPUnit_Framework_TestCase;
use Votemike\Basket\FixedCoupon;
use Votemike\Money\Money;

class FixedCouponTest extends PHPUnit_Framework_TestCase {

	public function testFixedAmountIsDiscounted()
	{
		$gross = new Money(10.002, 'USD');
		$coupon = new FixedCoupon(new Money(2, 'USD'));

		$this->assertEquals(2.00, $coupon->getDiscount($gross)->getAmount());
	}
	public function testFixedDiscountDoesntGoBelowZero()
	{
		$gross = new Money(10.002, 'GBP');
		$coupon = new FixedCoupon(new Money(12.004, 'GBP'));

		$this->assertEquals(10.00, $coupon->getDiscount($gross)->getAmount());
	}

	public function testCouponForDifferentCurrencyThrowsException()
	{
		$gross = new Money(10.00, 'GBP');
		$coupon = new FixedCoupon(new Money(2.00, 'USD'));

		$this->expectException(DomainException::class);
		$this->expectExceptionMessage('Currencies must match');
		$coupon->getDiscount($gross);
	}
}
