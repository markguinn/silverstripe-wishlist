(function ($, window, document, undefined) {
	'use strict';

	$(function(){
		$(document)
			.on('click', '#EditWishList', function(){
				// Rename link displays the edit form
				$(document.body).toggleClass('editingWishList');
				return false;
			})
			.on('click', '#Form_WishListForm_action_cancelEdit', function(e) {
				// Cancel button hides the edit form
				$(document.body).removeClass('editingWishList');
				$(this).removeClass('ajax-loading');
				e.preventDefault();
				return false;
			})
			.on('wishlistchanged', function(){
				// hide the edit form when the ajax is done
				$(document.body).removeClass('editingWishList');
			})
		;
	});
}(jQuery, this, this.document));