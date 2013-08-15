<?php
/**
 * WishListPage is where a customer can view his/her wish list(s)
 * and edit them. The controller also handles add/remove links
 * from the product pages. I chose to make this a pagetype instead
 * of a bare controller because a common usage pattern would be
 * to have this page as prt of the heirarchy of the account system.
 *
 * @author Mark Guinn <mark@adaircreative.com>
 * @date 08.09.2013
 * @package ecommerce_wishlist
 */
class WishListPage extends Page
{
	/**
	 * @var bool - allows you to override requireDefualtRecords if needed via config
	 */
	private static $auto_create_page = true;

	/**
	 * This matches the pattern used elsewhere in silverstripe
	 * @return mixed
	 */
	static function inst() {
		return self::get()->first();
	}

	/**
	 * This matches the pattern used elsewhere in ecommerce
	 * @return mixed
	 */
	static function singleton() {
		return self::inst();
	}

	/**
	 * @param $id
	 * @param $className
	 * @return string
	 */
	static function add_item_link($id, $className) {
		$url = sprintf('%sadd/%d/%s', self::inst()->Link(), $id, $className);
		return SecurityToken::inst()->addToUrl($url);
	}

	/**
	 * @param $id
	 * @param $className
	 * @return string
	 */
	static function remove_item_link($id, $className) {
		$url = sprintf('%sremove/%d/%s', self::inst()->Link(), $id, $className);
		return SecurityToken::inst()->addToUrl($url);
	}

	/**
	 * Create the page if needed
	 */
	function requireDefaultRecords() {
		if (!self::inst() && Config::inst()->get('WishListPage', 'auto_create_page')) {
			$rec = new WishListPage();
			$rec->Title = 'Wish List';
			$rec->ShowInSearch = false;
			$rec->ShowInMenu = false;
			$rec->CanViewType = 'LoggedInUsers';
			$rec->write();
			$rec->publish('Stage', 'Live');
			$rec->flushCache();
		}
	}
}



class WishListPage_Controller extends Page_Controller
{
	private static $allowed_actions = array(
		'add', 'remove', 'remove_all',
		'delete_list', 'create_list', 'WishListForm',
		'set_current_list',
	);

	/** @var WishList */
	protected $wishList;


	/**
	 * Initialize the controller
	 */
	function init(){
		parent::init();
		$this->wishList = WishList::current();
		Requirements::javascript(THIRDPARTY_DIR . '/jquery/jquery.js');
		Requirements::javascript(ECOMMERCE_WISHLIST_FOLDER . '/javascript/WishListPage.js');
	}


	/**
	 * @return array
	 */
	private function lookupBuyables() {
		return class_exists('EcommerceConfig')
			? EcommerceConfig::get("EcommerceDBConfig", "array_of_buyables")
			: SS_ClassLoader::instance()->getManifest()->getImplementorsOf('Buyable');
	}


	/**
	 * @param SS_HTTPRequest $req
	 * @return SS_HTTPResponse
	 */
	function add(SS_HTTPRequest $req) {
		// check out the inputs
		$buyables = $this->lookupBuyables();
		$id = (int)$req->param('ID');
		$className = $req->param('OtherID');
		if (!$id || !$className || !in_array($className, $buyables)) $this->httpError(400); // bad request
		if (!SecurityToken::inst()->checkRequest($req)) $this->httpError(403);

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
		$buyables = $this->lookupBuyables();
		$id = (int)$req->param('ID');
		$className = $req->param('OtherID');
		if (!$id || !$className || !in_array($className, $buyables)) $this->httpError(400); // bad request
		if (!SecurityToken::inst()->checkRequest($req)) $this->httpError(403);

		// look up the item
		$item = DataObject::get($className)->byID($id);
		if (!$item || !$item->exists()) $this->httpError(404);

		// add it to the list
		WishList::current()->removeBuyable($item);

		// return control
		return $this->redirectBack();
	}


	/**
	 * @param SS_HTTPRequest $req
	 * @return bool|SS_HTTPResponse
	 */
	function remove_all(SS_HTTPRequest $req) {
		if (!SecurityToken::inst()->checkRequest($req)) $this->httpError(403);
		$this->wishList->removeAllBuyables();
		return $this->redirectBack();
	}


	/**
	 * @param SS_HTTPRequest $req
	 * @return bool|SS_HTTPResponse
	 */
	function delete_list(SS_HTTPRequest $req) {
		if (!SecurityToken::inst()->checkRequest($req)) $this->httpError(403);
		$this->wishList->removeAllBuyables();
		$this->wishList->delete();
		return $this->redirectBack();
	}


	/**
	 * @param SS_HTTPRequest $req
	 * @return bool|SS_HTTPResponse
	 */
	function create_list(SS_HTTPRequest $req) {
		if (!SecurityToken::inst()->checkRequest($req)) $this->httpError(403);

		$list = new WishList();
		$list->Title = 'New Wish List';
		$list->write();

		// Will automatically be the 'current' list because it will have the most
		// recent LastEdited. If the criteria for that ever changes this should
		// be uncommented:
		//WishList::set_current($list);

		return $this->redirectBack();
	}


	/**
	 * @param SS_HTTPRequest $req
	 * @return bool|SS_HTTPResponse
	 */
	function set_current_list(SS_HTTPRequest $req) {
		$list = WishList::get()->byID((int)$req->param('ID'));
		if (!$list || !$list->exists()) $this->httpError(404);
		if ($list->OwnerID != Member::currentUserID()) $this->httpError(403);

		WishList::set_current($list);
		return $this->redirectBack();
	}


	/**
	 * @return Form
	 */
	function WishListForm() {
		return new Form($this, 'WishListForm',
			new FieldList(array(
				TextField::create('Title', '', $this->wishList->Title)
					->setAttribute('placeholder', 'Name for List')
			)),
			new FieldList(array(
				new FormAction('saveList', 'Save'),
				new FormAction('cancelEdit', 'Cancel'),
			)),
			new RequiredFields('Title')
		);
	}


	/**
	 * @param array $data
	 * @param Form  $form
	 * @return SS_HTTPResponse
	 */
	function saveList(array $data, Form $form) {
		if (!isset($data['Title']) || trim($data['Title']) == '') {
			$this->wishList->Title = 'Wish List';
		} else {
			$this->wishList->Title = $data['Title'];
		}

		$this->wishList->write();

		return $this->redirectBack();
	}


	/**
	 * Not really used
	 */
	function cancelEdit() {
		return $this->redirectBack();
	}


	/**
	 * @return WishList
	 */
	function CurrentList() {
		if (!isset($this->wishList)) $this->wishList = WishList::current();
		return $this->wishList;
	}

	/**
	 * @return DataList
	 */
	function AllLists() {
		return WishList::get_for_user();
	}



	/**
	 * @return String
	 */
	function RemoveAllLink() {
		return SecurityToken::inst()->addToUrl($this->Link('remove-all'));
	}

	/**
	 * @return String
	 */
	function DeleteListLink() {
		return SecurityToken::inst()->addToUrl($this->Link('delete-list'));
	}

	/**
	 * @return String
	 */
	function CreateListLink() {
		return SecurityToken::inst()->addToUrl($this->Link('create-list'));
	}

	/**
	 * @return bool
	 */
	function IsShopModule() {
		return defined('SHOP_PATH');
	}
}

