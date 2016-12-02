<?php namespace Votemike\Basket;

/**
 * @TODO Should rows have grosses, nets and taxes?
 */
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
}
