<?php namespace Votemike\Basket;

use Votemike\Money\Money;

interface Coupon {

	/**
	 * @param Money $gross
	 * @return Money
	 */
	public function getDiscount(Money $gross);
}
