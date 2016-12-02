<?php namespace Votemike\Basket\Tests;

use Exception;
use LogicException;
use Mockery;
use PHPUnit_Framework_TestCase;
use Votemike\Basket\Basket;
use Votemike\Basket\Item;
use Votemike\Money\Money;

class BasketTest extends PHPUnit_Framework_TestCase {

	public function testItemCanBeAdded()
	{
		$item = Mockery::mock(Item::class);
		$item->shouldReceive('getGross')->with('USD')->once()->andReturn(new Money(1, 'USD'));
		$item->shouldReceive('getUniqueIdentifier')->once()->andReturn(uniqid());

		$basket = new Basket('USD');
		$basket->addItem($item);
		$this->assertCount(1, $basket->getRows());

		$basket = new Basket('USD');
		$basket->addItem($item, 2);
		$this->assertCount(1, $basket->getRows());
	}

	public function testItemAddedWithQuantityLessThanOneThrowsException()
	{
		$item = Mockery::mock(Item::class);

		$basket = new Basket('USD');
		$this->expectException(LogicException::class);
		$this->expectExceptionMessage('Cannot have a quantity less than 1');
		$basket->addItem($item, 0);
	}

	public function testItemAddedRequiresWithoutPriceInCurrencyOfTheCurrentBasketThrowsException()
	{
		$item = Mockery::mock(Item::class);
		$item->shouldReceive('getGross')->with('USD')->once()->andReturnNull();
		$item->shouldReceive('getName')->once()->andReturn('Something');

		$basket = new Basket('USD');
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Item Something does not have a USD price');
		$basket->addItem($item);
	}

	public function testItemAlreadyAddedThrowsException()
	{
		$item = Mockery::mock(Item::class);
		$item->shouldReceive('getGross')->with('USD')->once()->andReturn(new Money(1, 'USD'));
		$item->shouldReceive('getUniqueIdentifier')->once()->andReturn(uniqid());

		$basket = new Basket('USD');
		$basket->addItem($item);
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Item is already in basket. Use updateQuantity() instead.');
		$basket->addItem($item);
	}
}
