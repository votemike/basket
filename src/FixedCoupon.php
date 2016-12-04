<?php namespace Votemike\Basket;

use DomainException;
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
	public function getDiscount(Money $gross)
	{
		if ($this->discount->getCurrency() !== $gross->getCurrency())
		{
			throw new DomainException('Currencies must match');
		}

		if ($gross->getAmount() < $this->discount->getAmount())
		{
			return $gross->round();
		}

		return $this->discount->round();
	}
}
