<?php namespace Votemike\Basket;

use Votemike\Money\Money;

/**
 * @TODO recurring prices
 *
 * DB id?
 * Allow setting net instead of gross?
 */
interface Item {

	/**
	 * @param $currencyCode
	 * @return Money
	 */
	public function getGross($currencyCode);

	/**
	 * @return string
	 */
	public function getName();

	/**
	 * @return mixed
	 */
	public function getUniqueIdentifier();

	/**
	 * Float between 0 and 100
	 *
	 * @return int
	 */
	public function getVatRate();
}
