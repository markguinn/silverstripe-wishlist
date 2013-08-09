<?php
/**
 * DataExtension for buyable objects to all them to be
 * on a wishlist.
 *
 * @author Mark Guinn <mark@adaircreative.com>
 * @date 08.07.2013
 * @package ecommerce_wishlist
 */
class CanAddToWishList extends DataExtension
{
	/**
	 * @return string
	 */
	function WishListAddLink() {
		return WishList_Controller::add_item_link($this->getOwner()->ID, $this->getOwner()->ClassName);
	}

	/**
	 * @return string
	 */
	function WishListRemoveLink() {
		return WishList_Controller::remove_item_link($this->getOwner()->ID, $this->getOwner()->ClassName);
	}

	/**
	 * @return bool
	 */
	function IsInWishList() {
		$item = $this->WishListItem();
		return $item && $item->exists();
	}

	/**
	 * IF this item is an any of the current member's wishlists,
	 * returns the wishlist item record.
	 *
	 * NOTE: in order to be consistent with Product::OrderItem
	 * this method returns a record NO MATTER WHAT, so you'll
	 * have to check $rec->exists() to see if it's in the cart.
	 *
	 * @return WishListItem
	 */
	function WishListItem() {
		$listWhere = sprintf(
			'"WishList"."ID" = "WishListItem"."WishListID" AND "WishList"."OwnerID" = \'%d\'',
			Member::currentUserID());

		return WishListItem::get()
			->innerJoin('WishList', $listWhere)
			->filter(array(
				'BuyableID'         => $this->getOwner()->ID,
				'BuyableClassName'  => $this->getOwner()->ClassName,
			))->first();
	}
}