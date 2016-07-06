$(document).ready(function(){
	
//------------------------------------------------------------------------
//						ANCHOR SMOOTHSCROLL SETTINGS
//------------------------------------------------------------------------
	$('a.goto, .navbar .nav a').smoothScroll({speed: 1200});

//------------------------------------------------------------------------
//						FULL HEIGHT SECTION SCRIPT
//------------------------------------------------------------------------
	$(".screen-height").css("min-height",$( window ).height());
	$( window ).resize(function() {
		$(".screen-height").css("min-height",$( window ).height());
	});

});


//------------------------------------------------------------------------
//						BTN AFFICHAGE FORM
//------------------------------------------------------------------------

$('.download-btn').click(function(){
    $('#element-rent-form').show();
});

$('#btn-1').click(function(){
    if (!$(this).hasClass('download-btn-selected')) {
        $(this).addClass('download-btn-selected');
    }
    $('#btn-2').removeClass('download-btn-selected');
});

$('#btn-2').click(function(){
    if (!$(this).hasClass('download-btn-selected')) {
        $(this).addClass('download-btn-selected');
    }
    $('#btn-1').removeClass('download-btn-selected');
});