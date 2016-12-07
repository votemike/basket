<?php namespace Votemike\Basket;

use Exception;
use LogicException;
use Votemike\Money\Money;

/**
 * Gross is found for all products/coupons.
 * Tax is then calculated
 * Net is Gross minus Tax
 *
 * @TODO Recurring items
 * @TODO basket wide coupons
 * @TODO how do we add item/row specific coupons?
 * @TODO Number of unique items, total items
 * @TODO getItems
 */
class Basket {

	/**
	 * @var string
	 */
	private $currencyCode;

	/**
	 * @var Row[]
	 */
	private $rows;

	/**
	 * @param string $currencyCode
	 */
	public function __construct($currencyCode)
	{
		$this->currencyCode = $currencyCode;
	}

	/**
	 * @param Item $item
	 * @param int $quantity
	 * @throws Exception
	 */
	public function addItem(Item $item, $quantity = 1)
	{
		if ($quantity < 1)
		{
			throw new LogicException('Cannot have a quantity less than 1');
		}
		if (empty($item->getGross($this->currencyCode)))
		{
			throw new Exception('Item ' . $item->getName() . ' does not have a ' . $this->currencyCode . ' price');
		}

		$uid = $item->getUniqueIdentifier();
		if (isset($this->rows[$uid]))
		{
			throw new Exception('Item is already in basket. Use updateQuantity() instead.');
		}

		$this->rows[$uid] = new Row($item, $quantity);
	}

	/**
	 * Returns a rounded Money object
	 *
	 * @return Money
	 */
	public function getGross()
	{
		$gross = new Money(0, $this->currencyCode);

		foreach ($this->rows as $row)
		{
			$gross = $gross->add($row->getGross($this->currencyCode));
		}

		return $gross;
	}

	/**
	 * Returns a rounded Money object
	 *
	 * @return Money
	 */
	public function getNet()
	{
		return $this->getGross()->sub($this->getTax());
	}

	public function getRows()
	{
		return $this->rows;
	}

	/**
	 * Returns a rounded Money object
	 *
	 * @return Money
	 */
	public function getTax()
	{
		$tax = new Money(0, $this->currencyCode);

		foreach ($this->rows as $row)
		{
			$tax = $tax->add($row->getTax($this->currencyCode));
		}

		return $tax;
	}

	/**
	 * @param string $currencyCode
	 * @throws Exception
	 */
	public function setCurrencyCode($currencyCode)
	{
		foreach ($this->rows as $row)
		{
			$item = $row->getItem();
			if (empty($item->getGross($currencyCode)))
			{
				throw new Exception('Item ' . $item->getName() . ' does not have a ' . $currencyCode . ' price');
			}
		}
		$this->currencyCode = $currencyCode;
	}

	/**
	 * Replace them item
	 * Because the Country has changed (and therefore the VAT)
	 * Or a VAT registered number has been supplied to take off VAT
	 * Only really required if prices are set on Item and not retrieved dynamically
	 *
	 * @param Item $item
	 * @throws Exception
	 */
	public function updateItem(Item $item)
	{
		$uid = $item->getUniqueIdentifier();
		if (!isset($this->rows[$uid]))
		{
			throw new Exception('Item is not yet in basket. Use addItem() instead.');
		}
		$currentRow = $this->rows[$uid];
		$this->rows[$uid] = new Row($item, $currentRow->getQuantity());
	}

	public function updateQuantity($itemIdentifier, $quantity)
	{
		if ($quantity < 0)
		{
			throw new LogicException('Cannot have a quantity less than 0');
		}
		if ($quantity === 0)
		{
			unset($this->rows[$itemIdentifier]);
		}
		else
		{
			$currentRow = $this->rows[$itemIdentifier];
			$this->rows[$itemIdentifier] = new Row($currentRow->getItem(), $quantity);
		}
	}
}
