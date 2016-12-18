<?php namespace Votemike\Basket;

use Votemike\Money\Money;

class Row {

	/**
	 * @var Item
	 */
	private $item;

	/**
	 * @var int
	 */
	private $quantity;

	public function __construct(Item $item, $quantity)
	{
		$this->item = $item;
		$this->quantity = $quantity;
	}

	/**
	 * Returns a rounded Money object
	 *
	 * @param $currencyCode
	 * @return Money
	 */
	public function getGross($currencyCode)
	{
		$itemGross = $this->getItem()->getGross($currencyCode);
		$itemsGross = $itemGross->multiply($this->getQuantity())->round();

		return $itemsGross;
	}

	/**
	 * @return Item
	 */
	public function getItem()
	{
		return $this->item;
	}

	/**
	 * @return int
	 */
	public function getQuantity()
	{
		return $this->quantity;
	}

	/**
	 * Returns a rounded Money object
	 *
	 * @param $currencyCode
	 * @return Money mixed
	 */
	public function getTax($currencyCode)
	{
		$rowGross = $this->getGross($currencyCode);
		return $rowGross->divide(1 + ($this->item->getVatRate() / 100))->sub($rowGross)->multiply(-1)->round();
	}
}
