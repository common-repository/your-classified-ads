jQuery(document).ready(function($){

	
	//icons messages
	$(".help .info.hide").hide();
	
	$(".help img").click(function(){
		var text = $(this).next('.info');

		text.toggle();

		return false;
	});

});



