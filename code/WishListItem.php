<?php
/**
 * Links a buyable record to a wishlist
 *
 * @author Mark Guinn <mark@adaircreative.com>
 * @date 08.07.2013
 * @package ecommerce_wishlist
 */
class WishListItem extends DataObject
{
	private static $db = array(
		'BuyableID'         => 'Int',
		'BuyableClassName'  => 'Varchar(60)',
	);

	private static $indexes = array(
		"BuyableID"         => true,
		"BuyableClassName"  => true
	);

	private static $has_one = array(
		'WishList'          => 'WishList',
	);

	/**
	 * @param BuyableModel $item
	 * @return $this
	 */
	function setBuyable(BuyableModel $item) {
		$this->BuyableID = $item->ID;
		$this->BuyableClassName = $item->ClassName;
		return $this;
	}

	/**
	 * @return DataObject|null
	 */
	function getBuyable() {
		if (!$this->BuyableClassName || !$this->BuyableID) return null;
		return DataObject::get($this->BuyableClassName)->byID($this->BuyableID);
	}
}