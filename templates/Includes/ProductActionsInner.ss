<% if $HasVariations %>
<ul class="productActions <% if $VariationIsInCart %>inCart<% else %>notInCart<% end_if %>" id="$AJAXDefinitions.UniqueIdentifier">
	<li class="variationsLink">
		<a class="selectVariation btn action ajaxAddToCartLink" href="{$AddVariationsLink}"rel="VariationsTable{$ID}" title="<% _t("Product.UPDATECART","update cart for") %> $Title.ATT">
			<span class="removeLink"><% _t("Product.INCART","In Cart") %></span>
			<span class="addLink"><% _t("Product.ADDLINK","Add to cart") %></span>
		</a>
	</li>
</ul>
<% else %>
<ul class="productActions <% if $IsInCart %>inCart<% else %>notInCart<% end_if %> <% if $IsInWishList %>inWishList<% else %>notInWishList<% end_if %>" id="$AJAXDefinitions.UniqueIdentifier">
	<li class="removeLink">
		<a class="goToCartLink btn action" href="$EcomConfig.CheckoutLink" title="<% _t("Product.GOTOCHECKOUTLINK","Go to the checkout") %>">
			<span class="removeLink goToCartLink"><% _t("Product.GOTOCHECKOUTLINK","Go to the checkout") %></span>
		</a>
		<a class="ajaxBuyableRemove ajaxRemoveFromCartLink" href="$RemoveAllLink" title="<% _t("Product.REMOVELINK","Remove from Cart") %>">
			<span class="removeLink"><% _t("Product.REMOVELINK","Remove from Cart") %></span>
		</a>
	</li>
	<li class="addLink">
		<a class="ajaxBuyableAdd btn action ajaxAddToCartLink" href="$AddLink" title="<% _t("Product.ADDLINK","Add to Cart") %>">
			<span class="addLink"><% _t("Product.ADDLINK","Add to Cart") %></span>
		</a>
	</li>
	<li class="wishListAddLink">
		<a class="ajaxBuyableAdd btn action ajaxAddToWishListLink" href="$WishListAddLink" title="<% _t("WishList.ADDLINK","Add to Wish List") %>">
			<span class="wishListAddLink"><% _t("WishList.ADDLINK","Add to Wish List") %></span>
		</a>
	</li>
	<li class="wishListRemoveLink">
		<a class="ajaxBuyableRemove btn action ajaxRemoveFromWishListLink" href="$WishListRemoveLink" title="<% _t("WishList.REMOVELINK","Remove from Wish List") %>">
			<span class="wishListRemoveLink"><% _t("WishList.REMOVELINK","Remove from Wish List") %></span>
		</a>
		<% if $WishListItem.WishList.Title != 'Wish List' %>
			<small>This item is on your wish list called {$WishListItem.WishList.Title}.</small>
		<% end_if %>
	</li>
</ul>
<% end_if %>
