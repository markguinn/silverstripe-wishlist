<% require css(shop_wishlist/css/wishlist.css) %>
<% with $CurrentList %>
	<h1 id="WishListTitle">
		$Title
		<a href="javascript:;" id="EditWishList">Rename</a>
	</h1>
	$Top.WishListForm
	$Top.Content

	<% if $Items.Count %>
		<table id="InformationTable" class="editable infotable wishListTable">
<%--
			<thead>
				<tr>
					<th scope="col" class="left"><% _t("Order.PRODUCT","Image") %></th>
					<th scope="col" class="left"><% _t("Order.PRODUCT","Product") %></th>
					<th scope="col" class="center"><% _t("Order.PRICE","Price") %></th>
					<th scope="col" class="right"></th>
				</tr>
			</thead>
--%>
			<tbody>
				<% loop $Items %>
					<tr>
						<% with $Buyable %>
							<td class="image">
								<% if $Image %>
									<a href="$Link" title="<% sprintf(_t("READMORE","View &quot;%s&quot;"),$Title) %>">
										<img src="$Image.Thumbnail.AbsoluteURL" alt="$Title"/>
									</a>
								<% end_if %>
							</td>
							<td class="title">
								<a href="$Link">$Up.TableTitle</a>
								<div class="subtitle">$Up.SubTitle</div>
							</td>
							<td class="center unitprice">$Price.Nice</td>
						<% end_with %>
						<td class="right remove">
							<a class="ajax" href="$Buyable.WishListRemoveLink" title="<% _t("WishList.REMOVELINK","Remove from Wish List") %>">
								<img src="shop/images/remove.gif" alt="x"/>
							</a>
						</td>
					</tr>
				<% end_loop %>
			</tbody>
		</table>
	<% else %>
		<p class="noItems">This list contains no items.</p>
	<% end_if %>
<% end_with %>

<ul class="wishListActions clearfix">
	<% if $CurrentList.Items.Count %>
		<li class="wishListRemoveAll left">
			<a class="btn btn-default action" href="$RemoveAllLink">
				<span><% _t("WishList.REMOVEALL","Remove All Items") %></span>
			</a>
		</li>
	<% end_if %>
	<% if $CurrentList.Title != 'Wish List' %>
		<li class="deleteWishList left">
			<a class="btn btn-default action" href="$DeleteListLink">
				<span><% _t("WishList.DELETELIST","Delete List") %></span>
			</a>
		</li>
	<% end_if %>

	<li class="createWishList right">
		<a class="btn btn-default action" href="$CreateListLink">
			<span><% _t("WishList.CREATENEWLIST","Create New List") %></span>
		</a>
	</li>
</ul>

<% if $AllLists.Count > 1 %>
	<h3 class="otherLists">Your Other Wish Lists</h3>
	<ul class="wishLists">
		<% loop $AllLists %>
			<% if $ID != $Top.CurrentList.ID %>
				<li><a href="$SetCurrentLink">$Title</a> ($BuyableCount)</li>
			<% end_if %>
		<% end_loop %>
	</ul>
<% end_if %>

