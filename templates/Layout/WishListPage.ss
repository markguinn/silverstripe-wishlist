<% with $CurrentList %>
	<h1 id="WishListTitle">
		$Title
		<a href="javascript:;" id="EditWishList">Rename</a>
	</h1>
	$Top.WishListForm

	$Content

	<% if $Items.Count %>
		<table id="InformationTable" class="editable infotable wishListTable">
			<thead>
				<tr>
					<th scope="col" class="left"><% _t("Order.PRODUCT","Product") %></th>
					<th scope="col" class="center"><% _t("Order.PRICE","Price") %></th>
					<th scope="col" class="right"></th>
				</tr>
			</thead>
			<tbody>
				<% loop $Items %>
					<tr id="$AJAXDefinitions.TableID" class="orderItemHolder">
						<% with $Buyable %>
							<td class="product title">
<%--
								<% if Image %>
									<a href="$Link" title="<% sprintf(_t("READMORE","Click here to read more on &quot;%s&quot;"),$Title) %>">
										<img src="$Image.Thumbnail.URL" alt="<% sprintf(_t("IMAGE","%s image"),$Title) %>" />
									</a>
								<% else %>
									<a href="$Link" title="<% sprintf(_t("READMORE"),$Title) %>" class="noimage">no image</a>
								<% end_if %>
								<% include ProductGroupItemImageThumb %>
--%>
								<% if $Image %>
									<div class="image">
										<a href="$Link" title="<% sprintf(_t("READMORE","View &quot;%s&quot;"),$Title) %>">
											<img src="<% with $Image.setWidth(45) %>$Me.AbsoluteURL<% end_with %>" alt="$Title"/>
										</a>
									</div>
								<% end_if %>
								<div class="itemTitleAndSubTitle">
									<% if $Link %>
										<a id="$AJAXDefinitions.TableTitleID" href="$Link" title="<%t Order.READMORE 'Click here to read more on {name}' name=$Title %>">$Title</a>
									<% else %>
										<span id="$AJAXDefinitions.TableTitleID">$Title</span>
									<% end_if %>
								</div>
							</td>
						<td class="center unitprice">$UnitPriceAsMoney.Nice</td>
						<% end_with %>
						<td class="right remove">
							<% if $Top.IsShopModule %>
								<a class="ajaxQuantityLink" href="$Buyable.WishListRemoveLink" title="<% _t("WishList.REMOVELINK","Remove from Wish List") %>">
									<img src="shop/images/remove.gif" alt="x"/>
								</a>
							<% else %>
								<strong>
									<a class="ajaxQuantityLink ajaxRemoveFromCart" href="$Buyable.WishListRemoveLink" title="<% _t("WishList.REMOVELINK","Remove from Wish List") %>">
										<img src="ecommerce/images/remove.gif" alt="x"/>
									</a>
								</strong>
							<% end_if %>
						</td>
					</tr>
				<% end_loop %>
			</tbody>
		</table>
	<% else %>
		<p class="noItems">This list contains no items.</p>
	<% end_if %>
<% end_with %>

<ul class="wishListActions">
	<% if $CurrentList.Items.Count %>
		<li class="wishListRemoveAll left">
			<a class="btn action" href="$RemoveAllLink">
				<span><% _t("WishList.REMOVEALL","Remove All Items") %></span>
			</a>
		</li>
	<% end_if %>
	<% if $CurrentList.Title != 'Wish List' %>
		<li class="deleteWishList left">
			<a class="btn action" href="$DeleteListLink">
				<span><% _t("WishList.DELETELIST","Delete List") %></span>
			</a>
		</li>
	<% end_if %>

	<li class="createWishList right">
		<a class="btn action" href="$CreateListLink">
			<span><% _t("WishList.CREATENEWLIST","Create New List") %></span>
		</a>
	</li>
</ul>

<% if $AllLists.Count > 1 %>
	<h3>Your Other Wish Lists</h3>
	<ul class="wishLists">
		<% loop $AllLists %>
			<% if $ID != $Top.CurrentList.ID %>
				<li><a href="$SetCurrentLink">$Title</a> ($BuyableCount)</li>
			<% end_if %>
		<% end_loop %>
	</ul>
<% end_if %>

