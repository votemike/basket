<?php namespace Votemike\Basket;

use Exception;
use InvalidArgumentException;
use Votemike\Money\Money;

class PercentageCoupon implements Coupon {

	/**
	 * Float between 0 and 100
	 *
	 * @var int
	 */
	private $percentage;

	/**
	 * @param float $percentage
	 * @throws Exception
	 */
	public function __construct($percentage)
	{
		if ($percentage <= 0 || $percentage > 100)
		{
			throw new InvalidArgumentException('Percentage must be a value between 0 and 100');
		}
		$this->percentage = $percentage;
	}

	public function getDiscount(Money $gross)
	{
		return $gross->percentage($this->percentage)->round();
	}
}
