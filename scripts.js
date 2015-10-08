jQuery(document).ready(function($)
{	
	$(document).on('click', '.woc_btn_woocommerce_close', function()
		{
			var woc_off_message 	= $(this).attr('woc_off_message');
			alert(woc_off_message);
		}
	)
});