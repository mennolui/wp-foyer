function update_slide_format_meta_boxes(){var e,i;e=jQuery(".postbox[id*=foyer_slide_format_]"),i=jQuery("#foyer_slide_format input[name=slide_format]:checked").val(),e.hide().filter("#foyer_slide_format_"+i).show()}!function($){"use strict";function e(){var i=$(".foyer_slides_editor"),t=$(".foyer_slides_editor_slides");$(".foyer_slides_editor_add_select").unbind("change").change(function(o){var r=$(o.currentTarget).val();if(r>0){var a={action:"foyer_slides_editor_add_slide",channel_id:i.data("channel-id"),slide_id:r,nonce:foyer_slides_editor_security.nonce};$.post(ajaxurl,a,function(i){""!=i&&(t.replaceWith(i),$(".foyer_slides_editor_add_select").val(""),e())})}return!1}),$(".foyer_slides_editor_slides_slide_remove").unbind("click").click(function(o){if(confirm(foyer_slides_editor_defaults.confirm_remove_message)){var r={action:"foyer_slides_editor_remove_slide",channel_id:i.data("channel-id"),slide_key:$(o.currentTarget).parents(".foyer_slides_editor_slides_slide").data("slide-key"),nonce:foyer_slides_editor_security.nonce};$.post(ajaxurl,r,function(i){""!=i&&(t.replaceWith(i),e())})}return!1}),t.sortable({revert:100,update:function(o,r){var a=[];$(this).children().each(function(){a[a.length]=$(this).data("slide-id")});var d={action:"foyer_slides_editor_reorder_slides",channel_id:i.data("channel-id"),slide_ids:a,nonce:foyer_slides_editor_security.nonce};$.post(ajaxurl,d,function(i){""!=i&&(t.replaceWith(i),e())})}}),t.disableSelection()}$(function(){e()})}(jQuery),jQuery(function(){update_slide_format_meta_boxes(),jQuery("#foyer_slide_format input[name=slide_format]").on("change",function(){update_slide_format_meta_boxes()})}),jQuery(function(){var e,i;wp.media&&(i=wp.media.model.settings.post.id,e=foyer_slide_format_default.photo,jQuery(".slide_image_upload_button").on("click",function(t){var o,r;return t.preventDefault(),o=jQuery(this).parent(),r?(r.uploader.uploader.param("post_id",e),void r.open()):(wp.media.model.settings.post.id=e,r=wp.media.frames.file_frame=wp.media({title:foyer_slide_format_default.text_select_photo,button:{text:foyer_slide_format_default.text_use_photo},multiple:!1}),r.on("select",function(){var e;e=r.state().get("selection").first().toJSON(),o.find(".slide_image_preview").attr("src",e.url).css("width","auto"),o.find(".slide_image_value").val(e.id),wp.media.model.settings.post.id=i,o.removeClass("empty")}),void r.open())}),jQuery(".slide_image_delete_button").on("click",function(e){var i,t;e.preventDefault(),i=jQuery(this).parent(),i.find(".slide_image_preview").attr("src",""),i.find(".slide_image_value").val(""),i.addClass("empty")}),jQuery("a.add_media").on("click",function(){wp.media.model.settings.post.id=i}))}),function($){"use strict";function e(){var e=$(".foyer-preview-actions button"),t=$(".foyer-preview");e.on("click",function(){var o=jQuery(this).attr("data-orientation");e.removeClass("active");for(var r in foyer_preview.orientations)t.removeClass("foyer-preview-"+r);t.addClass("foyer-preview-"+o),jQuery(this).addClass("active"),i(o)})}function i(e){console.log(e);var i={action:"foyer_preview_save_orientation_choice",orientation:e,object_id:foyer_preview.object_id};jQuery.post(foyer_preview.ajax_url,i)}$(function(){e()})}(jQuery);