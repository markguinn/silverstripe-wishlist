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

	protected static $calculated_buyable_price = array();

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
	function Buyable(){ return $this->getBuyable(); }
	function getBuyable() {
		if (!$this->BuyableClassName || !$this->BuyableID) return null;
		return DataObject::get($this->BuyableClassName)->byID($this->BuyableID);
	}

	/**
	 * @param bool $recalculate [optional]
	 * @return Float
	 */
	function UnitPrice($recalculate=false){ return $this->getUnitPrice($recalculate); }
	function getUnitPrice($recalculate=false) {
		$product = $this->getBuyable();
		if ($product) {
			if(!isset(self::$calculated_buyable_price[$this->ID]) || $recalculate) {
				self::$calculated_buyable_price[$this->ID] = $product->getCalculatedPrice();
			}

			return self::$calculated_buyable_price[$this->ID];
		}
	}

	/**
	 * @param bool $recalculate
	 * @return Money
	 */
	public function UnitPriceAsMoney($recalculate = false) {return $this->getUnitPriceAsMoney($recalculate);}
	public function getUnitPriceAsMoney($recalculate = false) {
		return EcommerceCurrency::get_money_object_from_order_currency($this->getUnitPrice($recalculate));
	}
}