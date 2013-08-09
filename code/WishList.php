<?php
/**
 * Wishlist model
 *
 * @author Mark Guinn <mark@adaircreative.com>
 * @date 08.07.2013
 * @package ecommerce_wishlist
 */
class WishList extends DataObject
{
	private static $db = array(
		'Title'     => 'Varchar(255)',
	);

	private static $has_one = array(
		'Owner'     => 'Member',
	);

	private static $has_many = array(
		'Items'     => 'WishListItem',
	);

	protected static $current;

	/**
	 * Returns the last used wish list or creates one if needed.
	 * NOTE: we don't actually save it until they add something to it
	 *
	 * @return WishList
	 */
	public static function current() {
		if (!isset(self::$current) || !self::$current) {
			$lastUsed = WishList::get()
				->filter('OwnerID', Member::currentUserID())
				->sort('LastEdited DESC')
				->first();

			if (!$lastUsed || !$lastUsed->exists()) {
				self::$current = new WishList(array(
					'Title'     => 'Wish List',
					'OwnerID'   => Member::currentUserID(),
				));
			} else {
				self::$current = $lastUsed;
			}
		}

		return self::$current;
	}

	/**
	 * @param WishList $list
	 */
	public static function set_current($list) {
		self::$current = $list;
	}

	/**
	 * @param Member $member [optional] - defaults to current user
	 * @return DataList
	 */
	public static function get_for_user(Member $member=null) {
		if (!$member) $member = Member::currentUser();
		if (!$member) return null;
		return WishList::get()->filter('OwnerID', $member->ID);
	}


	/**
	 * @return int
	 */
	public function getItemCount() {
		return $this->Items()->count();
	}

	/**
	 * @param BuyableModel $item
	 * @return bool
	 */
	public function hasBuyable(BuyableModel $item) {
		if (!$this->ID) return false;

		$existing = WishListItem::get()->filter(array(
			'WishListID'        => $this->ID,
			'BuyableClassName'  => $item->ClassName,
			'BuyableID'         => $item->ID,
		));

		return ($existing->count() > 0);
	}

	/**
	 * @param BuyableModel $item
	 * @return bool
	 */
	public function addBuyable(BuyableModel $item) {
		if (!$this->ID) $this->write();
		if ($this->hasBuyable($item)) return false;

		$myItem = new WishListItem();
		$myItem->setBuyable($item);
		$myItem->WishListID = $this->ID;
		$myItem->write();

		return true;
	}

	/**
	 * @param BuyableModel $item
	 * @return bool
	 */
	public function removeBuyable(BuyableModel $item) {
		if (!$this->ID) return false;
		if (!$this->hasBuyable($item)) return false;
		$item->WishListItem()->delete();
		return true;
	}
}


class WishList_Controller extends Controller
{
	private static $allowed_actions = array('add', 'remove');
	private static $url_segment = 'wishlist';

	/**
	 * @param $id
	 * @param $className
	 * @return string
	 */
	static function add_item_link($id, $className) {
		return sprintf('%s/add/%d/%s', Config::inst()->get('WishList_Controller', 'url_segment'), $id, $className);
	}

	/**
	 * @param $id
	 * @param $className
	 * @return string
	 */
	static function remove_item_link($id, $className) {
		return sprintf('%s/remove/%d/%s', Config::inst()->get('WishList_Controller', 'url_segment'), $id, $className);
	}


	/**
	 * @param SS_HTTPRequest $req
	 * @return SS_HTTPResponse
	 */
	function add(SS_HTTPRequest $req) {
		// check out the inputs
		$buyables = EcommerceConfig::get("EcommerceDBConfig", "array_of_buyables");
		$id = (int)$req->param('ID');
		$className = $req->param('OtherID');
		if (!$id || !$className || !in_array($className, $buyables)) $this->httpError(403); // bad request

		// look up the item
		$item = DataObject::get($className)->byID($id);
		if (!$item || !$item->exists()) $this->httpError(404);

		// add it to the list
		WishList::current()->addBuyable($item);

		// return control
		return $this->redirectBack();
	}

	/**
	 * @param SS_HTTPRequest $req
	 * @return SS_HTTPResponse
	 */
	function remove(SS_HTTPRequest $req) {
		// check out the inputs
		$buyables = EcommerceConfig::get("EcommerceDBConfig", "array_of_buyables");
		$id = (int)$req->param('ID');
		$className = $req->param('OtherID');
		if (!$id || !$className || !in_array($className, $buyables)) $this->httpError(403); // bad request

		// look up the item
		$item = DataObject::get($className)->byID($id);
		if (!$item || !$item->exists()) $this->httpError(404);

		// add it to the list
		WishList::current()->removeBuyable($item);

		// return control
		return $this->redirectBack();
	}
}