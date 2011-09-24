function metaColumnEdit(caller, data) {
	var e = jQuery(caller), meta_value = e.html();
	if(!e.children().length) {
		e.html('<input type="text" value="'+meta_value+'" class="new-meta-column" />');
		e.append('<a href="#" class="save button">'+ajaxMetaL10n.ok+'</a> <a href="#" class="cancel button">'+ajaxMetaL10n.cancel+'</a>');
		e.children('.save').click(function() {
			data.action = 'update_meta_column';
			data.meta_value = e.children('input').val();
			jQuery.post(ajaxurl, data, function(feedback){
				if(feedback == 1) // Updated successfully, single key/value pair
					e.html(e.children('input').val());
				else if(feedback == 0 || feedback == -1) // Failed to update
					e.html(meta_value);
				else { // Updated successfully, multiple values for key (new hash)
					e.html(e.children('input').val());
					e.removeAttr('onclick');
					e.click(function() {
						data.prev_value_hash = feedback;
						metaColumnEdit(caller, data);
					});
				}
			});
			return false;
		});
		e.children('.cancel').click(function() {
			e.html(meta_value);
			return false;
		});
	}
}