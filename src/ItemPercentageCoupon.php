<?php namespace Votemike\Basket;

class ItemPercentageCoupon extends PercentageCoupon {

	/**
	 * @var mixed
	 */
	protected $itemUniqueIdentifier;

	/**
	 * @param float $percentage
	 * @param $itemUniqueIdentifier
	 */
	public function __construct($percentage, $itemUniqueIdentifier)
	{
		parent::__construct($percentage);
		$this->itemUniqueIdentifier = $itemUniqueIdentifier;
	}

	public function getItemUniqueIdentifier()
	{
		return $this->itemUniqueIdentifier;
	}
}
