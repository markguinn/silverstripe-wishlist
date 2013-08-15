<div class="wishListButtons">
	<% if $IsInWishList %>
		<a class="wishListRemoveLink btn action" href="$WishListRemoveLink" title="<% _t("WishList.REMOVELINK","Remove from Wish List") %>">
			<% _t("WishList.REMOVELINK","Remove from Wish List") %>
		</a>
		<% if $WishListItem.WishList.Title != 'Wish List' %>
			<small>This item is on your wish list called {$WishListItem.WishList.Title}.</small>
		<% end_if %>
	<% else %>
		<a class="wishListAddLink btn action" href="$WishListAddLink" title="<% _t("WishList.ADDLINK","Add to Wish List") %>">
			<% _t("WishList.ADDLINK","Add to Wish List") %>
		</a>
	<% end_if %>
</div>
