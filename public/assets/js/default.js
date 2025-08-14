function myclose(){
	/*window.open('','_self').close();*/
	if (/Chrome/i.test(navigator.userAgent)) {
		window.close();
	} else {
		window.open('about:blank', '_self').close()
	}; 
}
function form_submit(fid){
	$(document).find('input:button').attr('disabled', 'disabled');
	fuel_set_csrf_token(fid);
	fid.submit();
}
function page_move(target){
	$(document).find('input:button').attr('disabled', 'disabled');
	window.open(target, '_self');
}
