<?php
/**
 * Extension
 *
 * @author Mark Guinn <mark@adaircreative.com>
 * @date 07.31.2014
 * @package wishlist
 */
class WishListAjax extends Extension
{
	private static $indicator = '<i class="icon-spin icon-spinner show-when-loading ib"></i>';


	/**
	 * This is for template use
	 * @return string
	 */
	public function WishListAjaxIndicator() {
		return Config::inst()->get('WishListAjax', 'indicator');
	}


	/**
	 * Add some ajax stuff to the form for renaming items
	 * @param Form $form
	 */
	public function updateWishListForm(Form &$form) {
		$form->addExtraClass('ajax');
		$form->Actions()->push(new LiteralField('ajaxindicator', Config::inst()->get('WishListAjax', 'indicator')));
	}


	/**
	 * @param SS_HTTPRequest $request
	 * @param AjaxHttpResponse $response
	 * @param WishList $list
	 * @param Buyable $item
	 */
	public function updateRemoveResponse($request, &$response, $list, $item) {
		if ($request->isAjax() && $this->owner->hasExtension('AjaxControllerExtension')) {
			if (!$response) $response = $this->owner->getAjaxResponse();
			$response->addRenderContext('WISHLIST', $list);
			$response->pushRegion('WishListItems', $this->owner);
			$response->triggerEvent('wishlistchanged');
			$response->triggerEvent('wishlistremove');
			if ($list->Items()->count() == 0) {
				$response->triggerEvent('wishlistempty');
				$response->pushRegion('WishListPageActions', $this->owner);
			}

		}
	}


	/**
	 * @param SS_HTTPRequest $request
	 * @param AjaxHttpResponse $response
	 * @param WishList $list
	 */
	public function updateRemoveAllResponse($request, &$response, $list) {
		if ($request->isAjax() && $this->owner->hasExtension('AjaxControllerExtension')) {
			if (!$response) $response = $this->owner->getAjaxResponse();
			$response->addRenderContext('WISHLIST', $list);
			$response->pushRegion('WishListItems', $this->owner);
			$response->pushRegion('WishListPageActions', $this->owner);
			$response->triggerEvent('wishlistchanged');
			$response->triggerEvent('wishlistremove');
			$response->triggerEvent('wishlistempty');
		}
	}


	/**
	 * @param SS_HTTPRequest $request
	 * @param AjaxHttpResponse $response
	 * @param WishList $list
	 * @param Buyable $item
	 */
	public function updateAddResponse($request, &$response, $list, $item) {
		if ($request->isAjax() && $this->owner->hasExtension('AjaxControllerExtension')) {
			if (!$response) $response = $this->owner->getAjaxResponse();
			$response->addRenderContext('WISHLIST', $list);
			$response->triggerEvent('wishlistchanged');
			$response->triggerEvent('wishlistadd');
		}
	}


	/**
	 * @param SS_HTTPRequest $request
	 * @param AjaxHttpResponse $response
	 * @param WishList $list
	 * @param array $data
	 */
	public function updateSaveListResponse($request, &$response, $list, $data) {
		if ($request->isAjax() && $this->owner->hasExtension('AjaxControllerExtension')) {
			if (!$response) $response = $this->owner->getAjaxResponse();
			$response->addRenderContext('WISHLIST', $list);
			$response->pushRegion('WishListPageHeader', $this->owner);
			$response->pushRegion('WishListPageActions', $this->owner);
			$response->pushRegion('OtherWishLists', $this->owner);
			$response->triggerEvent('wishlistchanged');
		}
	}

	/**
	 * @param SS_HTTPRequest $request
	 * @param AjaxHttpResponse $response
	 * @param WishList $list
	 */
	public function updateCreateListResponse($request, &$response, $list) {
		if ($request->isAjax() && $this->owner->hasExtension('AjaxControllerExtension')) {
			if (!$response) $response = $this->owner->getAjaxResponse();
			$response->addRenderContext('WISHLIST', $list);
			$response->pushRegion('WishListPageHeader', $this->owner);
			$response->pushRegion('WishListItems', $this->owner);
			$response->pushRegion('WishListPageActions', $this->owner);
			$response->pushRegion('OtherWishLists', $this->owner);
			$response->triggerEvent('wishlistchanged');
			$response->triggerEvent('wishlistcreated');
		}
	}


	/**
	 * @param SS_HTTPRequest $request
	 * @param AjaxHttpResponse $response
	 */
	public function updateDeleteListResponse($request, &$response) {
		if ($request->isAjax() && $this->owner->hasExtension('AjaxControllerExtension')) {
			if (!$response) $response = $this->owner->getAjaxResponse();
			$response->pushRegion('WishListPageHeader', $this->owner);
			$response->pushRegion('WishListItems', $this->owner);
			$response->pushRegion('WishListPageActions', $this->owner);
			$response->pushRegion('OtherWishLists', $this->owner);
			$response->triggerEvent('wishlistchanged');
			$response->triggerEvent('wishlistdeleted');
		}
	}


	/**
	 * @param SS_HTTPRequest $request
	 * @param AjaxHttpResponse $response
	 * @param WishList $list
	 */
	public function updateSetCurrentListResponse($request, &$response, $list) {
		if ($request->isAjax() && $this->owner->hasExtension('AjaxControllerExtension')) {
			if (!$response) $response = $this->owner->getAjaxResponse();
			$response->pushRegion('WishListPageHeader', $this->owner);
			$response->pushRegion('WishListItems', $this->owner);
			$response->pushRegion('WishListPageActions', $this->owner);
			$response->pushRegion('OtherWishLists', $this->owner);
			$response->triggerEvent('wishlistchanged');
		}
	}

}