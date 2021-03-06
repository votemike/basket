<?php namespace Votemike\Basket\Tests;

use Mockery;
use PHPUnit_Framework_TestCase;
use Votemike\Basket\Item;
use Votemike\Basket\Row;
use Votemike\Money\Money;

class RowTest extends PHPUnit_Framework_TestCase {

	public function testGetRowGross()
	{
		$item = Mockery::mock(Item::class);
		$item->shouldReceive('getGross')->with('USD')->once()->andReturn(new Money(10, 'USD'));
		$item->shouldReceive('getUniqueIdentifier')->once()->andReturn(uniqid());

		$row = new Row($item, 1);
		$this->assertEquals(10.00, $row->getGross('USD')->getAmount());
	}

	public function testGetRowGrossWithMultipleItems()
	{
		$item = Mockery::mock(Item::class);
		$item->shouldReceive('getGross')->with('USD')->once()->andReturn(new Money(10, 'USD'));
		$item->shouldReceive('getUniqueIdentifier')->once()->andReturn(uniqid());

		$row = new Row($item, 2);
		$this->assertEquals(20.00, $row->getGross('USD')->getAmount());
	}

	public function testGetRowTax()
	{
		$item = Mockery::mock(Item::class);
		$item->shouldReceive('getGross')->with('USD')->once()->andReturn(new Money(10, 'USD'));
		$item->shouldReceive('getUniqueIdentifier')->once()->andReturn(uniqid());
		$item->shouldReceive('getVatRate')->once()->andReturn(20);

		$row = new Row($item, 1);
		// Really 1.666 recurring
		$this->assertEquals(1.67, $row->getTax('USD')->getAmount());
	}

	public function testGetRowTaxWithMultipleItems()
	{
		$item = Mockery::mock(Item::class);
		$item->shouldReceive('getGross')->with('USD')->once()->andReturn(new Money(10, 'USD'));
		$item->shouldReceive('getUniqueIdentifier')->once()->andReturn(uniqid());
		$item->shouldReceive('getVatRate')->once()->andReturn(20);

		$row = new Row($item, 2);
		// Really 3.333 recurring
		$this->assertEquals(3.33, $row->getTax('USD')->getAmount());
	}
}
