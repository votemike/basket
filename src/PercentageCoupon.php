<?php namespace Votemike\Basket;

use Exception;
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
			throw new Exception('@TODO');//@TODO
		}
		$this->percentage = $percentage;
	}

	public function applyTo(Money $gross)
	{
		return $gross->percentage(100 - $this->percentage);
	}
}
