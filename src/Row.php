<?php namespace Votemike\Basket;

use Votemike\Money\Money;

class Row {

	/**
	 * @var ItemPercentageCoupon
	 */
	private $coupon;

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
	 * @param ItemPercentageCoupon $coupon
	 */
	public function addCoupon(ItemPercentageCoupon $coupon)
	{
		$this->coupon = $coupon;
	}

	/**
	 * @return ItemPercentageCoupon
	 */
	public function getCoupon()
	{
		return $this->coupon;
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

		if (is_null($this->coupon))
		{
			return $itemsGross;
		}

		return $itemsGross->sub($this->coupon->getDiscount($itemsGross));
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

	public function removeCoupon()
	{
		$this->coupon = null;
	}
}
