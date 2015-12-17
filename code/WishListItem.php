<?php
/**
 * Links a buyable record to a wishlist
 *
 * @author Mark Guinn <mark@adaircreative.com>
 * @date 08.07.2013
 * @package shop_wishlist
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

    private static $casting = array(
        'UnitPrice'         => 'Currency',
    );


    /**
     * @param Buyable|BuyableModel $item
     * @return $this
     */
    public function setBuyable($item)
    {
        $this->BuyableID = $item->ID;
        $this->BuyableClassName = $item->ClassName;
        return $this;
    }


    /**
     * @return DataObject|null
     */
    public function getBuyable()
    {
        if (!$this->BuyableClassName || !$this->BuyableID) {
            return null;
        }
        return DataObject::get($this->BuyableClassName)->byID($this->BuyableID);
    }

    public function Buyable()
    {
        return $this->getBuyable();
    }


    /**
     * @return Float
     */
    public function getUnitPrice()
    {
        $product = $this->getBuyable();
        if ($product && $product->exists()) {
            return $product->sellingPrice();
        }
    }

    public function UnitPrice()
    {
        return $this->getUnitPrice();
    }


    /**
     * @return Currency
     */
    public function getUnitPriceAsMoney()
    {
        $out = new Currency('UnitPrice');
        $out->setValue($this->getUnitPrice());
        return $out;
    }

    public function UnitPriceAsMoney()
    {
        return $this->getUnitPriceAsMoney();
    }


    /**
     * @return string
     */
    public function TableTitle()
    {
        $buyable = $this->getBuyable();
        $item = $buyable->hasMethod('Item') ? $buyable->Item() : null;
        return $item ? $item->TableTitle() : $buyable->Title;
    }


    /**
     * @return String
     */
    public function SubTitle()
    {
        $buyable = $this->getBuyable();
        return $buyable->hasMethod('Item') ? $buyable->Item()->SubTitle() : '';
    }
}
