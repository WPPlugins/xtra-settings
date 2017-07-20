(function($) {

	$(document).on( 'click', '.nav-tab-wrapper a', function() {
		var tact = $(this).attr('activate');
		if (tact) {
			$('.xtra_tabbes').hide();
			$('.xtra_tabbes.xtra_tabbeID_'+tact).show();
			if (tact=='*') $('.xtra_tabbes').show();
			$('.xtra_opt_submit').show();
			if (tact.length>1) $('.xtra_opt_submit').hide();
			$('.nav-tab').removeClass('nav-tab-active');
			$('.nav-tab[activate="'+tact+'"]').addClass('nav-tab-active');
			$('.nav-tab[activate="'+tact+'"]').blur();
			$('#xtra_submit_last_seltab').val(tact);
			return false;
		}
		return true;
	})

})( jQuery );

(function ($) {
  $(function () {
    $('.xtra-color-picker').wpColorPicker();
  });
}(jQuery));