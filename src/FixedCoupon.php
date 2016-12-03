<?php namespace Votemike\Basket;

use Votemike\Money\Money;

class FixedCoupon implements Coupon {

	/**
	 * @var Money
	 */
	private $discount;

	public function __construct(Money $discount)
	{
		$this->discount = $discount;
	}

	/**
	 * @inheritdoc
	 */
	public function applyTo(Money $gross)
	{
		$newGross = $gross->sub($this->discount);

		if($newGross->getAmount() < 0)
		{
			return new Money(0, $gross->getCurrency());
		}

		return $newGross;
	}
}
