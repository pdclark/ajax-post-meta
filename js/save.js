function metaColumnEdit(a,b){var c=jQuery(a),d=c.html();if(!c.children().length){c.html('<input type="text" value="'+d+'" class="new-meta-column" />');c.append('<a href="#" class="save button">'+ajaxMetaL10n.ok+'</a> <a href="#" class="cancel button">'+ajaxMetaL10n.cancel+"</a>");c.children(".save").click(function(){b.action="update_meta_column";b.meta_value=c.children("input").val();jQuery.post(ajaxurl,b,function(e){if(e==1){c.html(c.children("input").val())}else{if(e==0||e==-1){c.html(d)}else{c.html(c.children("input").val());c.removeAttr("onclick");c.click(function(){b.prev_value_hash=e;metaColumnEdit(a,b)})}}});return false});c.children(".cancel").click(function(){c.html(d);return false})}};