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
    public static function current()
    {
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
    public static function set_current($list)
    {
        if ($list) {
            $list->write(false, false, true);
        } // force LastEdited to change
        self::$current = $list;
    }


    /**
     * @param Member $member [optional] - defaults to current user
     * @return DataList
     */
    public static function get_for_user(Member $member=null)
    {
        if (!$member) {
            $member = Member::currentUser();
        }
        if (!$member) {
            return null;
        }
        return WishList::get()->filter('OwnerID', $member->ID);
    }


    /**
     * Set the owner automatically if needed
     */
    protected function onBeforeWrite()
    {
        parent::onBeforeWrite();
        if (!$this->OwnerID) {
            $this->OwnerID = Member::currentUserID();
        }
    }


    /**
     * @return int
     */
    public function getBuyableCount()
    {
        return $this->Items()->count();
    }

    /**
     * @param BuyableModel $item
     * @return bool
     */
    public function hasBuyable($item)
    {
        if (!$this->ID) {
            return false;
        }

        $existing = WishListItem::get()->filter(array(
            'WishListID'        => $this->ID,
            'BuyableClassName'  => $item->ClassName,
            'BuyableID'         => $item->ID,
        ));

        return ($existing->count() > 0);
    }

    /**
     * @param Buyable|BuyableModel $item
     * @return bool
     */
    public function addBuyable($item)
    {
        if (!$this->ID) {
            $this->write();
        }
        if ($this->hasBuyable($item)) {
            return false;
        }

        $myItem = new WishListItem();
        $myItem->setBuyable($item);
        $myItem->WishListID = $this->ID;
        $myItem->write();

        return true;
    }

    /**
     * @param Buyable|BuyableModel $item
     * @return bool
     */
    public function removeBuyable($item)
    {
        if (!$this->ID) {
            return false;
        }
        if (!$this->hasBuyable($item)) {
            return false;
        }
        $item->WishListItem()->delete();
        return true;
    }

    /**
     * @return int - number of items removed
     */
    public function removeAllBuyables()
    {
        $items = $this->Items();
        $count = 0;

        foreach ($items as $item) {
            $item->delete();
            $count++;
        }

        return $count;
    }


    /**
     * @return String
     */
    public function getSetCurrentLink()
    {
        $url = WishListPage::inst()->Link('set-current-list/' . $this->ID);
        return SecurityToken::inst()->addToUrl($url);
    }
}
