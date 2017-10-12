jQuery(document).ready(function($){
	$('.open-widget').click(function(){
		if($(".x-termcollapse").hasClass('in')){
			$('.x-termcollapse').removeClass('in');
		}
		else{
			$('.x-termcollapse').addClass('in');
			$('.x-termcollapse').css('height', 'auto');
		}
	});
});
