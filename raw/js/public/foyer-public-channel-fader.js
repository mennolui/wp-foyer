var foyer_fader_slideshows;
var foyer_foyer_fader_intervalObject;

function foyer_fader_setup_slideshows() {

	foyer_fader_slideshows.each(function() {
		jQuery(this).children().first().addClass('active');
	});

	foyer_fader_slideshows
		.bind('next',function() {
			var next = jQuery(this).children('.active').next();
			if (!next.length) {
				next = jQuery(this).children().first();
			}
			jQuery(this).children('.active').removeClass('active');
			next.addClass('active');
			fader.trigger('start');
		})
		.bind('prev',function() {
			var prev = jQuery(this).children('.active').prev();
			if (!prev.length) {
				prev = jQuery(this).children().last();
			}
			jQuery(this).children('.active').removeClass('active');
			prev.addClass('active');
			fader.trigger('start');
		})
		.bind('goto',function(e, element) {
		})
	;

	var fader = jQuery('body');
	fader
		.bind('start',function() {
			fader.trigger('stop');

			foyer_fader_intervalObject = window.setInterval(function() {
					foyer_fader_slideshows.trigger('next');
				}, 6000);
		})
		.bind('stop',function() {
			window.clearInterval(foyer_fader_intervalObject);
		})
	;


	fader.trigger('start');

	fader.find('.fader-prevnext.prev').click(function() {
		foyer_fader_slideshows.trigger('prev');
		return false;
	});
	fader.find('.fader-prevnext.next').click(function() {
		foyer_fader_slideshows.trigger('next');
		return false;
	});

}
