<?php
/**
 * Unit tests for wish list features.
 *
 * @author Mark Guinn <mark@adaircreative.com>
 * @date 08.07.2013
 * @package ecommerce_wishlist
 * @subpackage tests
 */
class WishListTest extends SapphireTest
{
	static $fixture_file = 'WishListTest.yml';

	function setUpOnce() {
		ini_set('memory_limit', '256M');
//		error_reporting(E_ALL);
		$p = singleton('Product');
		if (!$p->hasExtension('CanAddToWishList')) {
			Product::add_extension('CanAddToWishList');
		}
	}

	function testLinks() {
		$p = $this->objFromFixture('Product', 'p1');
		$this->assertStringStartsWith('/wish-list/add/' . $p->ID . '/Product', $p->WishListAddLink());
		$this->assertStringStartsWith('/wish-list/remove/' . $p->ID . '/Product', $p->WishListRemoveLink());
		$this->assertContains('?SecurityID=', $p->WishListAddLink());
		$this->assertContains('?SecurityID=', $p->WishListRemoveLink());
	}

	function testAddRemove() {
		$m1 = $this->objFromFixture('Member', 'm1');
		$m1->logIn();

		$p1 = $this->objFromFixture('Product', 'p1');
		$p2 = $this->objFromFixture('Product', 'p2');
		$p3 = $this->objFromFixture('Product', 'p3');

		// should be able to get a default empty list
		$list = WishList::current();
		$this->assertNotNull($list);
		$this->assertEquals(0, $list->getBuyableCount());

		// product should initially not be in a list
		$this->assertFalse($p1->IsInWishList());

		// after adding a product it should be in a list
		$list->addBuyable($p1);
		$this->assertTrue($list->hasBuyable($p1));
		$this->assertTrue($p1->IsInWishList());

		// a different product should still not be in the list
		$this->assertFalse($list->hasBuyable($p2));
		$this->assertFalse($p2->IsInWishList());

		// adding a second product should be no problem
		$list->addBuyable($p2);
		$this->assertTrue($p2->IsInWishList());

		// wishlist should now have 2 items
		$this->assertEquals(2, $list->getBuyableCount());

		// after removing an item should register as not being in the list
		$r = $list->removeBuyable($p1);
		$this->assertFalse($p1->IsInWishList());
		$this->assertTrue($p2->IsInWishList());
		$this->assertTrue($r);

		// wishlist should now have 1 item
		$this->assertEquals(1, $list->getBuyableCount());

		// removing an item that's not in the list should return false and not cause problems
		$r = $list->removeBuyable($p3);
		$this->assertEquals(1, $list->getBuyableCount());
		$this->assertFalse($r);

		// after removing the second item the count should be 0
		$list->removeBuyable($p2);
		$this->assertFalse($p2->IsInWishList());
		$this->assertEquals(0, $list->getBuyableCount());
	}

	function testMultipleLists() {
		WishList::set_current(null);
		$m1 = $this->objFromFixture('Member', 'm1');
		$m2 = $this->objFromFixture('Member', 'm2');
		$m1->logIn();

		$p1 = $this->objFromFixture('Product', 'p1');
		$p2 = $this->objFromFixture('Product', 'p2');
		$p3 = $this->objFromFixture('Product', 'p3');

		// should be able to retrieve a list of lists
		$allLists = WishList::get_for_user();
		$this->assertNotNull($allLists);
		$this->assertTrue($allLists instanceof DataList);

		// should initially be 0 lists
		$this->assertEquals(0, WishList::get_for_user()->count());
		$this->assertEquals(0, WishList::get_for_user($m2)->count());

		// current method should create one list
		$l1 = WishList::current();
		$l1->write();
		$l1->addBuyable($p1);
		//Debug::dump(array($l1, WishList::get_for_user()->sql(), WishList::get_for_user()->count(), WishList::get_for_user()->getIDList()));
		$this->assertEquals(1, WishList::get_for_user()->count());
		$this->assertEquals(0, WishList::get_for_user($m2)->count());

		// after manually creating a list there should be two lists
		$l2 = new WishList(array(
			'OwnerID'   => $m1->ID,
			'Title'     => 'Christmas',
		));
		$l2->write();
		$this->assertEquals(2, WishList::get_for_user()->count());

		// after adding a product to one list, it should not be present in the other one
		// but should still report that it is in a list
		$l2->addBuyable($p2);
		$this->assertTrue($p1->IsInWishList());
		$this->assertTrue($l1->hasBuyable($p1));
		$this->assertFalse($l2->hasBuyable($p1));
		$this->assertTrue($p2->IsInWishList());
		$this->assertFalse($l1->hasBuyable($p2));
		$this->assertTrue($l2->hasBuyable($p2));
		$this->assertFalse($p3->IsInWishList());
		$this->assertFalse($l1->hasBuyable($p3));
		$this->assertFalse($l2->hasBuyable($p3));

		// after creating a list for the a different user and adding
		// an item to that list, the item should not report that it is
		// in a list and should not be present in any of the other lists
		$l3 = new WishList(array(
			'OwnerID'   => $m2->ID,
			'Title'     => 'Christmas for someone else',
		));
		$l3->write();
		$l3->addBuyable($p3);
		$this->assertEquals(2, WishList::get_for_user()->count());
		$this->assertEquals(1, WishList::get_for_user($m2)->count());
		$this->assertFalse($p3->IsInWishList());
		$this->assertFalse($l1->hasBuyable($p3));
		$this->assertFalse($l2->hasBuyable($p3));

		// Buyable should be able to exist in two lists at once
		$l2->addBuyable($p1);
		$this->assertTrue($p1->IsInWishList());
		$this->assertTrue($l1->hasBuyable($p1));
		$this->assertTrue($l2->hasBuyable($p1));

		// removing an item from one list should not remove it from the other
		$l1->removeBuyable($p1);
		$this->assertTrue($p1->IsInWishList());
		$this->assertFalse($l1->hasBuyable($p1));
		$this->assertTrue($l2->hasBuyable($p1));

		// after removing item from both lists it should report as not being in a list
		$l2->removeBuyable($p1);
		$this->assertFalse($p1->IsInWishList());
		$this->assertFalse($l1->hasBuyable($p1));
		$this->assertFalse($l2->hasBuyable($p1));
	}
}