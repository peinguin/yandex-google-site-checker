function post(url,curr,domains){
	$.post(url,{ domains: domains[curr] },function(data){

		$('#responce').html($('#responce').html()+data);

		if(++curr<domains.length)
			post(url,curr,domains);
	});
}

$(document).ready(function(){
	$('form').submit(function(){
		post($(this).attr('action'),0,$(this).children('textarea').val().split(/[\n\s\t,;]+/));
		return false;
	});
});