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

	public function testCurrencyOfBasketCanBeChanged()
	{
		$itemA = Mockery::mock(Item::class);
		$itemA->shouldReceive('getGross')->with('USD')->once()->andReturn(new Money(1, 'USD'));
		$itemA->shouldReceive('getGross')->with('GBP')->once()->andReturn(new Money(1.5, 'GBP'));
		$itemA->shouldReceive('getUniqueIdentifier')->once()->andReturn(uniqid());

		$itemB = Mockery::mock(Item::class);
		$itemB->shouldReceive('getGross')->with('USD')->once()->andReturn(new Money(2, 'USD'));
		$itemB->shouldReceive('getGross')->with('GBP')->once()->andReturn(new Money(3, 'GBP'));
		$itemB->shouldReceive('getUniqueIdentifier')->once()->andReturn(uniqid());

		$basket = new Basket('USD');
		$basket->addItem($itemA);
		$basket->addItem($itemB);
		$basket->setCurrencyCode('GBP');
	}

	public function testChangingBasketCurrencyWhenNotAllItemsSupportThatCurrencyThrowsException()
	{
		$itemA = Mockery::mock(Item::class);
		$itemA->shouldReceive('getGross')->with('USD')->once()->andReturn(new Money(1, 'USD'));
		$itemA->shouldReceive('getGross')->with('GBP')->once()->andReturn(new Money(1.5, 'GBP'));
		$itemA->shouldReceive('getUniqueIdentifier')->once()->andReturn(uniqid());

		$itemB = Mockery::mock(Item::class);
		$itemB->shouldReceive('getGross')->with('USD')->once()->andReturn(new Money(2, 'USD'));
		$itemB->shouldReceive('getGross')->with('GBP')->once()->andReturnNull();
		$itemB->shouldReceive('getUniqueIdentifier')->once()->andReturn(uniqid());
		$itemB->shouldReceive('getName')->once()->andReturn('No GBP');

		$basket = new Basket('USD');
		$basket->addItem($itemA);
		$basket->addItem($itemB);
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Item No GBP does not have a GBP price');
		$basket->setCurrencyCode('GBP');
	}

	public function testGetGross()
	{
		$itemA = Mockery::mock(Item::class);
		$itemA->shouldReceive('getGross')->with('USD')->once()->andReturn(new Money(10, 'USD'));
		$itemA->shouldReceive('getUniqueIdentifier')->once()->andReturn(uniqid());

		$itemB = Mockery::mock(Item::class);
		$itemB->shouldReceive('getGross')->with('USD')->once()->andReturn(new Money(8, 'USD'));
		$itemB->shouldReceive('getUniqueIdentifier')->once()->andReturn(uniqid());

		$basket = new Basket('USD');
		$basket->addItem($itemA, 2);
		$basket->addItem($itemB);
		$this->assertEquals(28.00, $basket->getGross()->getAmount());
	}

	public function testGetNet()
	{
		$itemA = Mockery::mock(Item::class);
		$itemA->shouldReceive('getGross')->with('USD')->once()->andReturn(new Money(10, 'USD'));
		$itemA->shouldReceive('getUniqueIdentifier')->once()->andReturn(uniqid());
		$itemA->shouldReceive('getVatRate')->once()->andReturn(20);

		$itemB = Mockery::mock(Item::class);
		$itemB->shouldReceive('getGross')->with('USD')->once()->andReturn(new Money(10, 'USD'));
		$itemB->shouldReceive('getUniqueIdentifier')->once()->andReturn(uniqid());
		$itemB->shouldReceive('getVatRate')->once()->andReturn(5);

		$basket = new Basket('USD');
		$basket->addItem($itemA);
		$this->assertEquals(8.33, $basket->getNet()->getAmount());

		$basket = new Basket('USD');
		$basket->addItem($itemB);
		$this->assertEquals(9.52, $basket->getNet()->getAmount());

		$basket = new Basket('USD');
		$basket->addItem($itemA, 2);
		//Because it's rounded after it has been multiplied by the quantity
		$this->assertEquals(16.67, $basket->getNet()->getAmount());

		$basket = new Basket('USD');
		$basket->addItem($itemA);
		$basket->addItem($itemB);
		$this->assertEquals(17.85, $basket->getNet()->getAmount());
	}

	public function testGetTax()
	{
		$itemA = Mockery::mock(Item::class);
		$itemA->shouldReceive('getGross')->with('USD')->once()->andReturn(new Money(10, 'USD'));
		$itemA->shouldReceive('getUniqueIdentifier')->once()->andReturn(uniqid());
		$itemA->shouldReceive('getVatRate')->once()->andReturn(20);

		$itemB = Mockery::mock(Item::class);
		$itemB->shouldReceive('getGross')->with('USD')->once()->andReturn(new Money(10, 'USD'));
		$itemB->shouldReceive('getUniqueIdentifier')->once()->andReturn(uniqid());
		$itemB->shouldReceive('getVatRate')->once()->andReturn(5);

		$basket = new Basket('USD');
		$basket->addItem($itemA);
		$this->assertEquals(1.67, $basket->getTax()->getAmount());

		$basket = new Basket('USD');
		$basket->addItem($itemB);
		$this->assertEquals(0.48, $basket->getTax()->getAmount());

		$basket = new Basket('USD');
		$basket->addItem($itemA, 2);
		//Because it's rounded after it has been multiplied by the quantity
		$this->assertEquals(3.33, $basket->getTax()->getAmount());

		$basket = new Basket('USD');
		$basket->addItem($itemA);
		$basket->addItem($itemB);
		$this->assertEquals(2.15, $basket->getTax()->getAmount());
	}

	public function testTaxPlusNetAddsBackUpToGross()
	{
		$itemA = Mockery::mock(Item::class);
		$itemA->shouldReceive('getGross')->with('USD')->once()->andReturn(new Money(10, 'USD'));
		$itemA->shouldReceive('getUniqueIdentifier')->once()->andReturn(uniqid());
		$itemA->shouldReceive('getVatRate')->once()->andReturn(20);

		$itemB = Mockery::mock(Item::class);
		$itemB->shouldReceive('getGross')->with('USD')->once()->andReturn(new Money(10, 'USD'));
		$itemB->shouldReceive('getUniqueIdentifier')->once()->andReturn(uniqid());
		$itemB->shouldReceive('getVatRate')->once()->andReturn(5);

		$basket = new Basket('USD');
		$basket->addItem($itemA);
		$this->assertEquals($basket->getGross(), $basket->getTax()->add($basket->getNet()));

		$basket = new Basket('USD');
		$basket->addItem($itemB);
		$this->assertEquals($basket->getGross(), $basket->getTax()->add($basket->getNet()));

		$basket = new Basket('USD');
		$basket->addItem($itemA, 2);
		$this->assertEquals($basket->getGross(), $basket->getTax()->add($basket->getNet()));

		$basket = new Basket('USD');
		$basket->addItem($itemA);
		$basket->addItem($itemB);
		$this->assertEquals($basket->getGross(), $basket->getTax()->add($basket->getNet()));
	}

	public function testUpdatingAnItem()
	{
		$item = Mockery::mock(Item::class);
		$item->shouldReceive('getGross')->with('USD')->once()->andReturn(new Money(10, 'USD'));
		$item->shouldReceive('getUniqueIdentifier')->once()->andReturn(uniqid());
		$item->shouldReceive('getName')->once()->andReturn('New');

		$basket = new Basket('USD');
		$basket->addItem($item, 2);

		$updatedItem = Mockery::mock(Item::class);
		$updatedItem->shouldReceive('getUniqueIdentifier')->once()->andReturn($item->getUniqueIdentifier());
		$updatedItem->shouldReceive('getName')->once()->andReturn('Updated');

		$basket->updateItem($updatedItem);
	}

	public function testUpdatingAnItemIfItHasNotYetBeenAdded()
	{
		$item = Mockery::mock(Item::class);
		$item->shouldReceive('getUniqueIdentifier')->once()->andReturn(uniqid());
		$item->shouldReceive('getName')->once()->andReturn('New');

		$basket = new Basket('USD');

		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Item is not yet in basket. Use addItem() instead.');
		$basket->updateItem($item);
	}

	public function testUpdatingQuantity()
	{
		$item = Mockery::mock(Item::class);
		$item->shouldReceive('getUniqueIdentifier')->once()->andReturn(uniqid());
		$item->shouldReceive('getGross')->with('USD')->once()->andReturn(new Money(10, 'USD'));

		$basket = new Basket('USD');
		$basket->addItem($item);

		$this->assertEquals(1, array_values($basket->getRows())[0]->getQuantity());

		$basket->updateQuantity($item->getUniqueIdentifier(), 42);

		$this->assertEquals(42, array_values($basket->getRows())[0]->getQuantity());
	}

	public function testUpdatingQuantityToZeroRemovesItem()
	{
		$item = Mockery::mock(Item::class);
		$item->shouldReceive('getUniqueIdentifier')->once()->andReturn(uniqid());
		$item->shouldReceive('getGross')->with('USD')->once()->andReturn(new Money(10, 'USD'));

		$basket = new Basket('USD');
		$basket->addItem($item);

		$this->assertEquals(1, array_values($basket->getRows())[0]->getQuantity());

		$basket->updateQuantity($item->getUniqueIdentifier(), 0);

		$this->assertEmpty($basket->getRows());
	}

	public function testUpdatingQuantityToLessThanZeroThrowsException()
	{
		$item = Mockery::mock(Item::class);
		$item->shouldReceive('getUniqueIdentifier')->once()->andReturn(uniqid());
		$item->shouldReceive('getGross')->with('USD')->once()->andReturn(new Money(10, 'USD'));

		$basket = new Basket('USD');
		$basket->addItem($item);

		$this->assertEquals(1, array_values($basket->getRows())[0]->getQuantity());

		$this->expectException(LogicException::class);
		$this->expectExceptionMessage('Cannot have a quantity less than 0');
		$basket->updateQuantity($item->getUniqueIdentifier(), -1);
	}
}
