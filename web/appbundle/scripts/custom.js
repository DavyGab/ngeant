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

$('.img-nounours').click(function() {
    if($(this).attr('id') == 'img-radio1'){
        $('#radio1').click();
    } else if($(this).attr('id') == 'img-radio2'){
        $('#radio2').click();
    }
})

$('input[type=radio]').change(function() {
    if($(this).val() == "1"){
        $('#radio1lab > i').addClass('fa-check-square-o');
        $('#radio1lab > i').removeClass('fa-square-o');
        $('#radio2lab > i').addClass('fa-square-o');
        $('#radio2lab > i').removeClass('fa-check-square-o');

        $('#img-radio2').removeClass('img-choice');
        $('#img-radio1').addClass('img-choice');
    }
    else if($(this).val() == "2"){
        $('#radio1lab > i').addClass('fa-square-o');
        $('#radio1lab > i').removeClass('fa-check-square-o');
        $('#radio2lab > i').addClass('fa-check-square-o');
        $('#radio2lab > i').removeClass('fa-square-o');

        $('#img-radio1').removeClass('img-choice');
        $('#img-radio2').addClass('img-choice');
    }
});