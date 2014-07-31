<ul class="wishListActions clearfix" id="WishListPageActions">
	<% if $CurrentList.Items.Count %>
		<li class="wishListRemoveAll left">
			<a class="btn btn-default action ajax" href="$RemoveAllLink">
				<span><% _t("WishList.REMOVEALL","Remove All Items") %> $Top.WishListAjaxIndicator</span>
			</a>
		</li>
	<% end_if %>
	<% if $CurrentList.Title != 'Wish List' %>
		<li class="deleteWishList left">
			<a class="btn btn-default action ajax" href="$DeleteListLink">
				<span><% _t("WishList.DELETELIST","Delete List") %> $Top.WishListAjaxIndicator</span>
			</a>
		</li>
	<% end_if %>

	<li class="createWishList right">
		<a class="btn btn-default action ajax" href="$CreateListLink">
			<span><% _t("WishList.CREATENEWLIST","Create New List") %> $Top.WishListAjaxIndicator</span>
		</a>
	</li>
</ul>