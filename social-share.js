jQuery(function($) {
	$('.tn-social-share li a').click(function(e) {
		e.preventDefault();
		openPopUp($(this).attr('href'));
	});
});

function openPopUp(url){
	var width = 600;
	var height = 400;
    var w = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
    var h = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;
    var left = ((w / 2) - (width / 2)) +  10;
    var top = ((h / 2) - (height / 2)) +  50;
    var userWindow = window.open(url, '', 'scrollbars=yes, width=' + width + ', height=' + height + ', top=' + top + ', left=' + left);
    userWindow.focus();
}