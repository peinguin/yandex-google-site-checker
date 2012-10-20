function post(url,curr,domains){
	$.post(url,{ domains: domains[curr] },function(data){

		var data = eval('(' + data + ')');

		for(domain in data){
			$('#responce').html($('#responce').html()+
				'<p>'
					+domain
					+'|'
					+data[domain]['тиц']
					+'|'
					+data[domain]['yandex_pages_indexed']
					+'|'
					+data[domain]['google_page_rank']
					+'|'
					+data[domain]['google_pages_indexed']
				+'</p>');
		}

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