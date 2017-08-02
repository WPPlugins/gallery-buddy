jQuery.noConflict();
jQuery(document).ready(function($){
	
	var galleryBuddy = {
		
		init: function (config) {
			
			this.container = config.container;
			this.slide = config.slide;
			this.btn_prev = config.btn_prev;
			this.btn_next = config.btn_next;
			
			$( galleryBuddy.slide ).hide();
			
			$( galleryBuddy.container ).each( function() {
				
				var current = 1,
					container = $(this),
					max = container.find( galleryBuddy.slide ).size(),
					slides = container.find(  galleryBuddy.slide );
					btn_prev = container.find( galleryBuddy.btn_prev ).show(),
					btn_next = container.find( galleryBuddy.btn_next ).show();
				
				slides.first().show();
				
				container.find( galleryBuddy.btn_next ).click( function () {
					slides.hide();
					current++;
					if(current > max) current = 1;
					slides.eq( current - 1 ).show();
				});
				
				container.find( galleryBuddy.btn_prev ).click( function () {
					slides.hide();
					current--;
					if(current === 0) current = max;
					slides.eq( current - 1 ).show();
				});
				
			});
			
		}
	};
	
	galleryBuddy.init({
		container: '.gallery-buddy',
		slide: '.gallery-buddy-slide',
		btn_prev: '.gallery-buddy-prev',
		btn_next: '.gallery-buddy-next'
	});
	
});