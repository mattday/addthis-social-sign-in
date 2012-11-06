(function($){
$(document).ready(function() {
	
	var swap_addthis_ins = function (attr_id){
		
		var ins_id = attr_id.split("-")[0]+"-instructions";
		if ($('#'+ins_id).css('display') == "none")
			$('#'+ins_id).show('slow');
		else
			$('#'+ins_id).hide('slow');	
		
	}
	
	$('[id$="-swap-ins"]').click(function(){ swap_addthis_ins($(this).attr('id')); });	
});
})(jQuery);

