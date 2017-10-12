jQuery(document).ready(function($){
	$('.open-widget').click(function(){
		if($(".fieldset-wrapper.collapse").hasClass('in')){
			$('.fieldset-wrapper.collapse').removeClass('in');
		}
		else{
			$('.fieldset-wrapper.collapse').addClass('in');
			$('.fieldset-wrapper.collapse').css('height', 'auto');
		}
	});
});
