jQuery.noConflict();
jQuery(document).ready(function ($) {
	
	var pluginGalleryBuddy = {
		
		language_key: $('html').attr('lang'),
		
		translate: function (string) {

			var dict = [];
			dict['Gallery'] = [];
			dict['Gallery']['en-US'] = 'Gallery';
			dict['Gallery']['de-DE'] = 'Galerie';
			dict['Remove'] = [];
			dict['Remove']['en-US'] = 'Remove';
			dict['Remove']['de-DE'] = 'Entfernen';
			dict['sort message'] = [];
			dict['sort message']['en-US'] = 'You can drag the images to sort them.';
			dict['sort message']['de-DE'] = 'Du kannst durch Anfassen und Ziehen die Reihenfolge der Bilder in der Galerie verändern.';

			if (dict[string][pluginGalleryBuddy.language_key]) {
				return dict[string][pluginGalleryBuddy.language_key];
			}
			else {
				return dict[string]['en-US'];
			}
		},
		
		init: function () {
			
			/* legt Sortierliste an */
		
			$('#library-form').after('<div style="height: 0;border-top: 2px dotted #ddd;"><h3 style="margin-left: 15px; margin-bottom: 0px;">' + pluginGalleryBuddy.translate('Gallery') + '</h3><p style="margin-left: 15px; margin-top: 6px;">' + pluginGalleryBuddy.translate('sort message') + '<p><div id="gallery-buddy-sorter" style="padding-bottom: 40px;"><ul></ul></div></div>');
			$('#gallery-buddy-sorter ul').sortable({
				cursor: 'move',
				axis: 'y',
				update: function(event, ui) {
				
					pluginGalleryBuddy.protector_on();
				
					$.ajax({
						type: 'POST',
						url: '../wp-content/plugins/gallery-buddy/model.php',
						data: $('#gallery-buddy-sorter ul').sortable('serialize') + '&job=sort&post_id=' + post_id,
						success: function(data) {
							pluginGalleryBuddy.protector_off();
						},
						error: function(data) {
							pluginGalleryBuddy.protector_off();
							
						}
					});
				
				}
			});
		
			/* die Zeitverzögerung ist nicht mehr notwendig, weil das Skript nach 
			der Definition der post_id geladen wird, speziell am Ende des Codes. */
		
			var images = {};
		
			//window.setTimeout(function(){
				
				if (typeof post_id != 'undefined') {
					
					$.ajax({
						type: 'POST',
						url: '../wp-content/plugins/gallery-buddy/model.php',
						data: 'job=data&post_id=' + post_id,
						success: function(data) 
						{
							var imgdata = $.parseJSON(data)
							for(i in imgdata) 
							{
								images[imgdata[i]['img_id']] = {}
								images[imgdata[i]['img_id']]['pos'] = imgdata[i]['pos'];
								images[imgdata[i]['img_id']]['title'] = imgdata[i]['title'];
								images[imgdata[i]['img_id']]['icon'] = imgdata[i]['icon'];
							}
							
							/* Media-Library: */
							
							/* Fügt die Checkboxen ein */

							$('#media-items .media-item').each(function () {
								var the_id = $(this).attr('id');
								var media_id_split = the_id.split('-');
								$(this).find('.filename:last').after('<div class="mg-form-box"><label>' + pluginGalleryBuddy.translate('Gallery') + '</label><input id="mg-add-' + media_id_split[2] + '" class="mg-add" type="checkbox" value="' + media_id_split[2] + '"/></div>');
							});
					
							/* setzt den Status der Checkboxen */
					
							$('.media-item').each(function () {
								var img_id = $(this).find('.mg-add').val();
								if(images[img_id]) $(this).find('.mg-add').attr('checked','checked');
							});
					
							/* Gallery: lege Sortierliste an */
							
							var gallery_buddy_sorter = $('#gallery-buddy-sorter ul');
							
							for(i in images) 
							{
								gallery_buddy_sorter.append('<li id="mgsid_' + i + '"><img class="" src="' + images[i]['icon'] + '" alt="" style=""><span>' + images[i]['title'] + '</span><a href="' + i + '">' + pluginGalleryBuddy.translate('Remove') + '</a></li>');
							}
					
							gallery_buddy_sorter.sortable('refresh');
					
						},
						error: function(data) {}
					});
			
				}
			
			//}, 100);
		
		
			/* Aktionen bei Klick auf die Checkboxen */
		
			$('#media-items').delegate('.mg-add', 'click', function () {
			
				var checked = $(this).attr('checked');
				var checkbox_val = $(this).val();
				var title = $(this).parent().parent().find('.filename.new').text();
				var icon = $(this).parent().parent().find('.pinkynail').attr('src');

				var job = false;
			
				if(checked == 'checked')
				{
					job = 'insert';
				}
				else
				{
					job = 'remove';
				}
			
				/* baue Sortierliste */
			
				if(job == 'remove')
				{
					for (i in images) {
					    if (i == checkbox_val) {
					        delete images[i];
							$('#mgsid_' + i).remove();
					    }
					}
				}
		
				if(job == 'insert')
				{
					images[checkbox_val] = {};
					images[checkbox_val]['pos'] = 99;
					images[checkbox_val]['title'] = title;
					images[checkbox_val]['icon'] = icon;
				
					$('#gallery-buddy-sorter ul').append('<li id="mgsid_' + checkbox_val + '"><img class="" src="' + icon + '" alt="" style=""><span>' + title + '</span><a href="' + checkbox_val + '">' + pluginGalleryBuddy.translate('Remove') + '</a></li>');
					var next_pos = 0;
					next_pos = $('#gallery-buddy-sorter ul li').size() - 1;
				}
			
				$('#gallery-buddy-sorter ul').sortable('refresh');
				
				pluginGalleryBuddy.protector_on();
				
				$.ajax({
					type: 'POST',
					url: '../wp-content/plugins/gallery-buddy/model.php',
					data: 'job=' + job + '&post_id=' + post_id + '&img_id=' + checkbox_val + '&pos=' + next_pos,
					success: function(data) 
					{
						if(job == 'remove')
						{
							$.ajax({
								type: 'POST',
								url: '../wp-content/plugins/gallery-buddy/model.php',
								data: $('#gallery-buddy-sorter ul').sortable('serialize') + '&job=sort&post_id=' + post_id,
								success: function(data) {
									pluginGalleryBuddy.protector_off();
								},
								error: function(data) {
									pluginGalleryBuddy.protector_off();
								}
							});
						}
						else {
							pluginGalleryBuddy.protector_off();
						}
					},
					error: function (data) {
						pluginGalleryBuddy.protector_off();
					}
				});
			
			});
		
		
			/* Aktion bei klick auf "Entfernen" */
		
			$('#gallery-buddy-sorter').delegate('a', 'click', function (event) {
		
				event.preventDefault();
			
				var img_id = $(this).attr('href');
			
				for (i in images) {
				    if (i == img_id) {
				        delete images[i];
						$('#mgsid_' + i).remove();
						$('#mg-add-' + i).removeAttr('checked');
				    }
				}
				
				pluginGalleryBuddy.protector_on();
				
				$.ajax({
					type: 'POST',
					url: '../wp-content/plugins/gallery-buddy/model.php',
					data: 'job=remove&post_id=' + post_id + '&img_id=' + img_id,
					success: function(data) 
					{
						$.ajax({
							type: 'POST',
							url: '../wp-content/plugins/gallery-buddy/model.php',
							data: $('#gallery-buddy-sorter ul').sortable('serialize') + '&job=sort&post_id=' + post_id,
							success: function(data) {
								pluginGalleryBuddy.protector_off();
							},
							error: function(data) {
								pluginGalleryBuddy.protector_off();
							}
						});
					},
					error: function(data) {}
				});
		
			});
		
		},
		
		protector_on: function() {
			$('#wpwrap', window.parent.document).prepend('<div id="gallery-buddy-sorter-protector" style="z-index:100000;position:absolute;top:0;right:0;bottom:0;left:0;background:rgba(0,0,0,0.5) url(../wp-content/plugins/gallery-buddy/images/loader.gif) no-repeat center center;">');
		},
		
		protector_off: function() {
			$('#wpwrap', window.parent.document).find('#gallery-buddy-sorter-protector').remove();
		}
		
	};
	
	pluginGalleryBuddy.init();
	
});