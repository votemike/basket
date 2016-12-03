<?php namespace Votemike\Basket\Tests;

use DomainException;
use PHPUnit_Framework_TestCase;
use Votemike\Basket\FixedCoupon;
use Votemike\Money\Money;

class FixedCouponTest extends PHPUnit_Framework_TestCase {

	public function testFixedAmountIsDiscounted()
	{
		$gross = new Money(10.00, 'USD');
		$coupon = new FixedCoupon(new Money(2, 'USD'));

		$this->assertEquals(8.00, $coupon->applyTo($gross)->getAmount());
	}
	public function testFixedDiscountDoesntGoBelowZero()
	{
		$gross = new Money(10.00, 'GBP');
		$coupon = new FixedCoupon(new Money(12.00, 'GBP'));

		$this->assertEquals(0, $coupon->applyTo($gross)->getAmount());
	}

	public function testCouponForDifferentCurrencyThrowsException()
	{
		$gross = new Money(10.00, 'GBP');
		$coupon = new FixedCoupon(new Money(2.00, 'USD'));

		$this->expectException(DomainException::class);
		$this->expectExceptionMessage('Currencies must match');
		$coupon->applyTo($gross);
	}
}
