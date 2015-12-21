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
    public static function inst()
    {
        return self::get()->first();
    }

    /**
     * This matches the pattern used elsewhere in ecommerce
     * @return mixed
     */
    public static function singleton()
    {
        return self::inst();
    }

    /**
     * @param $id
     * @param $className
     * @return string
     */
    public static function add_item_link($id, $className)
    {
        $url = sprintf('%sadd/%d/%s', self::inst()->Link(), $id, $className);
        return SecurityToken::inst()->addToUrl($url);
    }

    /**
     * @param $id
     * @param $className
     * @return string
     */
    public static function remove_item_link($id, $className)
    {
        $url = sprintf('%sremove/%d/%s', self::inst()->Link(), $id, $className);
        return SecurityToken::inst()->addToUrl($url);
    }

    /**
     * Create the page if needed
     */
    public function requireDefaultRecords()
    {
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
    public function init()
    {
        parent::init();
        $this->wishList = WishList::current();
        Requirements::javascript(THIRDPARTY_DIR . '/jquery/jquery.js');
        Requirements::javascript(ECOMMERCE_WISHLIST_FOLDER . '/javascript/WishListPage.js');
    }


    /**
     * @param SS_HTTPRequest $req
     * @return SS_HTTPResponse
     */
    public function add(SS_HTTPRequest $req)
    {
        // check out the inputs
        $id = (int)$req->param('ID');
        $className = $req->param('OtherID');
        if (!$id || !$className) {
            $this->httpError(400);
        } // bad request
        if (!SecurityToken::inst()->checkRequest($req)) {
            $this->httpError(403);
        }

        // look up the item
        $item = DataObject::get($className)->byID($id);
        if (!$item || !$item->exists()) {
            $this->httpError(404);
        }

        // add it to the list
        $list = WishList::current();
        $list->addBuyable($item);

        // return control
        $this->extend('updateAddResponse', $req, $response, $list, $item);
        return $response ? $response : $this->redirectBack();
    }


    /**
     * @param SS_HTTPRequest $req
     * @return SS_HTTPResponse
     */
    public function remove(SS_HTTPRequest $req)
    {
        // check out the inputs
        $id = (int)$req->param('ID');
        $className = $req->param('OtherID');
        if (!$id || !$className) {
            $this->httpError(400);
        } // bad request
        if (!SecurityToken::inst()->checkRequest($req)) {
            $this->httpError(403);
        }

        // look up the item
        $item = DataObject::get($className)->byID($id);
        if (!$item || !$item->exists()) {
            $this->httpError(404);
        }

        // remove it from the list
        $list = WishList::current();
        $list->removeBuyable($item);

        // return control
        $this->extend('updateRemoveResponse', $req, $response, $list, $item);
        return $response ? $response : $this->redirectBack();
    }


    /**
     * @param SS_HTTPRequest $req
     * @return bool|SS_HTTPResponse
     */
    public function remove_all(SS_HTTPRequest $req)
    {
        if (!SecurityToken::inst()->checkRequest($req)) {
            $this->httpError(403);
        }
        $this->wishList->removeAllBuyables();
        $this->extend('updateRemoveAllResponse', $req, $response, $this->wishList);
        return $response ? $response : $this->redirectBack();
    }


    /**
     * @param SS_HTTPRequest $req
     * @return bool|SS_HTTPResponse
     */
    public function delete_list(SS_HTTPRequest $req)
    {
        if (!SecurityToken::inst()->checkRequest($req)) {
            $this->httpError(403);
        }
        $this->wishList->removeAllBuyables();
        $this->wishList->delete();

        WishList::set_current(null);
        $this->wishList = WishList::current();

        $this->extend('updateDeleteListResponse', $req, $response);
        return $response ? $response : $this->redirectBack();
    }


    /**
     * @param SS_HTTPRequest $req
     * @return bool|SS_HTTPResponse
     */
    public function create_list(SS_HTTPRequest $req)
    {
        if (!SecurityToken::inst()->checkRequest($req)) {
            $this->httpError(403);
        }

        $this->wishList = new WishList();
        $this->wishList->Title = 'New Wish List';
        $this->wishList->write();

        // Will automatically be the 'current' list because it will have the most
        // recent LastEdited. This needs to be explicit for ajax responses though.
        WishList::set_current($this->wishList);

        $this->extend('updateCreateListResponse', $req, $response, $this->wishList);
        return $response ? $response : $this->redirectBack();
    }


    /**
     * @param SS_HTTPRequest $req
     * @return bool|SS_HTTPResponse
     */
    public function set_current_list(SS_HTTPRequest $req)
    {
        $list = WishList::get()->byID((int)$req->param('ID'));
        if (!$list || !$list->exists()) {
            $this->httpError(404);
        }
        if ($list->OwnerID != Member::currentUserID()) {
            $this->httpError(403);
        }

        $this->wishList = $list;
        WishList::set_current($list);

        $this->extend('updateSetCurrentListResponse', $req, $response, $list);
        return $response ? $response : $this->redirectBack();
    }


    /**
     * @return Form
     */
    public function WishListForm()
    {
        $form = new Form($this, 'WishListForm',
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

        $this->extend('updateWishListForm', $form);
        return $form;
    }


    /**
     * @param array $data
     * @param Form  $form
     * @return SS_HTTPResponse
     */
    public function saveList(array $data, Form $form)
    {
        if (!isset($data['Title']) || trim($data['Title']) == '') {
            $this->wishList->Title = 'Wish List';
        } else {
            $this->wishList->Title = $data['Title'];
        }

        $this->wishList->write();

        $request = $this->getRequest();
        $this->extend('updateSaveListResponse', $request, $response, $this->wishList, $data);
        return $response ? $response : $this->redirectBack();
    }


    /**
     * Not really used
     */
    public function cancelEdit()
    {
        return $this->redirectBack();
    }


    /**
     * @return WishList
     */
    public function CurrentList()
    {
        if (!isset($this->wishList)) {
            $this->wishList = WishList::current();
        }
        return $this->wishList;
    }

    /**
     * @return DataList
     */
    public function AllLists()
    {
        return WishList::get_for_user();
    }



    /**
     * @return String
     */
    public function RemoveAllLink()
    {
        return SecurityToken::inst()->addToUrl($this->Link('remove-all'));
    }

    /**
     * @return String
     */
    public function DeleteListLink()
    {
        return SecurityToken::inst()->addToUrl($this->Link('delete-list'));
    }

    /**
     * @return String
     */
    public function CreateListLink()
    {
        return SecurityToken::inst()->addToUrl($this->Link('create-list'));
    }

    /**
     * @return bool
     */
    public function IsShopModule()
    {
        return defined('SHOP_PATH');
    }
}
