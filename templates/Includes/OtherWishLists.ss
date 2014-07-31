<div id="OtherWishLists">
	<% if $AllLists.Count > 1 %>
		<h3 class="otherLists">Your Other Wish Lists</h3>
		<ul class="wishLists">
			<% loop $AllLists %>
				<% if $ID != $Top.CurrentList.ID %>
					<li><a href="$SetCurrentLink" class="ajax">$Title $Top.WishListAjaxIndicator</a> ($BuyableCount)</li>
				<% end_if %>
			<% end_loop %>
		</ul>
	<% end_if %>
</div>
