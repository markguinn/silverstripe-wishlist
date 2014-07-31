<div id="WishListItems">
	<% with $CurrentList %>
		<% if $Items.Count %>
			<table id="InformationTable" class="editable infotable wishListTable">
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
									<img class="hide-when-loading" src="shop/images/remove.gif" alt="x"/>
									$Top.WishListAjaxIndicator
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
</div>