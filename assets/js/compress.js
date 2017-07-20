var xtra_stopper = 0;
var xtra_timestart = new Date().getTime();
var xtra_timeend = 0;
var xtra_totaltime = 0;

function xtra_do_images(dox)
{
	xtra_timestart = new Date().getTime();
	xtra_stopper = 0;
	
	var images = [];
	jQuery('.xtra_image_cb:checked').each(function(i) {
       images.push(this.value);
    });

	var target = jQuery('#xtra_ajax_resultsDiv'); 
	target.html('');
	target.show();
	var stpbut = jQuery('#xtra_ajax_stopButton'); 
	stpbut.show();
	stpbut.val('Stop '+dox);
	
	var buttonsDiv = jQuery('#xtra_ajax_buttonsDiv'); 
	buttonsDiv.hide();
	
	var xMethod = jQuery('#xtra_ajax_compM1').is(":checked") ? 1 : 0;
	xMethod = jQuery('#xtra_ajax_compM2').is(":checked") ? 2 : xMethod;
	
	var sure = jQuery('#xtra_ajax_sure').is(":checked") ? 1 : 0;
	var suretxt = '';
	if (!sure) suretxt = '<strong>SIMULATION MODE</strong> - check <strong>"I am sure"</strong> to enable REAL MODE.';
	
	target.append('<div><h3>'+dox+' Started...</h3>'+suretxt+'</div><hr>');
	xtra_do_next(dox,images,0,xMethod,1);
}

function xtra_do_next(dox,images,next_index,xMethod,fromi)
{
	if (!images.length) return xtra_do_complete(dox,2);
	if (xtra_stopper) return xtra_do_complete(dox,1);
	if (next_index >= images.length) return xtra_do_complete(dox);
	
	var cgif = jQuery('#xtra_ajax_convGIF').is(":checked") ? 1 : 0;
	var cbmp = jQuery('#xtra_ajax_convBMP').is(":checked") ? 1 : 0;
	var cpng = jQuery('#xtra_ajax_convPNG').is(":checked") ? 1 : 0;
	var sure = jQuery('#xtra_ajax_sure').is(":checked") ? 1 : 0;
	var compQ = jQuery('#xtra_ajax_compPRC').value; 
	var maxW = jQuery('#xtra_ajax_maxW').value; 
	var maxH = jQuery('#xtra_ajax_maxH').value; 
	if (xMethod==1) {
		maxW = 0;
		maxH = 0;
	}

	var acti = '';
	if (dox=='Compress') acti = 'xtra_compress_image';
	else if (dox=='Delete Backup') acti = 'xtra_delete_backup';
	else acti = 'xtra_restore_image';
	
	jQuery.post(
		ajaxurl,{
			_wpnonce: xtra_vars._wpnonce, 
			action: acti, 
			id: images[next_index], 
			ids: images,
			fromi: fromi,
			sure: sure, 
			compQ: compQ, 
			maxW: maxW,
			maxH: maxH,
			xMethod: xMethod, 
			cgif: cgif, 
			cbmp: cbmp, 
			cpng: cpng
		},
		function(response) 
		{
			var result;
			var target = jQuery('#xtra_ajax_resultsDiv'); 
			target.show();
			var stpbut = jQuery('#xtra_ajax_stopButton'); 
			stpbut.show();
			try {
				result = JSON.parse(response);
				if ( result['message'].indexOf('Bulk') > -1 )
					target.append('<div class="indented">' + fromi+'-'+result['fromi'] + ' / ' + images.length + ' &gt;&gt; ' + result['message'] +'</div>');
				else
					target.append('<div class="indented">' + (next_index+1) + '/' + images.length + ' &gt;&gt; ' + result['message'] +'</div>');
			}
			catch(e) {
				target.append('<div>' + xtra_vars.invalid_response + '</div>');
				if (console) {
					console.warn(images[next_index] + ': '+ e.message);
					console.warn('Invalid JSON Response: ' + response);
				}
		    }
			target.animate({scrollTop: 999999});
			if ( result['message'].indexOf('Bulk') > -1 && result['fromi'] >= images.length )
				return xtra_do_complete(dox);
			else
				xtra_do_next(dox,images,next_index+1,xMethod,result['fromi']+1);
		}
	);
}

function xtra_do_stop()
{
	xtra_stopper = 1;
}

function xtra_do_complete(dox,param)
{
	var stpbut = jQuery('#xtra_ajax_stopButton'); 
	stpbut.hide();
	
	var buttonsDiv = jQuery('#xtra_ajax_buttonsDiv'); 
	buttonsDiv.show();
	
	var target = jQuery('#xtra_ajax_resultsDiv'); 
	var target2 = jQuery('#xtra_refresh_images2'); 
	xtra_timeend = new Date().getTime();
	xtra_totaltime = ' in ' + Math.round( ( xtra_timeend - xtra_timestart ) / 1000 ) + ' sec.';
	
	var msg = dox+' Complete'+xtra_totaltime;
	var premsg = '';
	var refrbutt = '<input type="submit" id="xtra_refresh_images2" name="xtra_refresh_images" value="Refresh Image List" class="button button-primary bold" />';
	if (param == 1) msg = dox+' Stopped'+xtra_totaltime;
	if (param == 2) premsg = 'NOTHING was selected.';
		
	target.append(premsg+'<hr><div><strong>'+msg+'</strong></div><br/>'+refrbutt);
	target2.show();
	target.animate({scrollTop: 999999});
}
