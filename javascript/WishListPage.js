(function ($, window, document, undefined) {
	'use strict';

	$(function(){
		// Rename link displays the edit form
		$('#EditWishList').click(function(){
			$(document.body).toggleClass('editingWishList');
			return false;
		});

		// Cancel button just hides the form
		$('#Form_WishListForm_action_cancelEdit').click(function(){
			$(document.body).removeClass('editingWishList');
			return false;
		});
	});
}(jQuery, this, this.document));