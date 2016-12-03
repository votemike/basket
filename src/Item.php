<?php namespace Votemike\Basket;

use Votemike\Money\Money;

/**
 * @TODO Way to override VAT (e.g. baby clothes, food)
 * @TODO coupons per item
 * @TODO recurring prices
 * @TODO maybe this should be an interface?
 * @TODO prices in multiple currencies?
 *
 * name
 * description
 * unique item name?
 * icon?
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
