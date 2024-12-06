jQuery(document).ready(function($) {
    if ($('#ftpopupex').length === 0) {
        return; 
    }
	var dataTime = $('#popup-timer').data('time');
	var waitTime = dataTime && !isNaN(parseInt(dataTime)) ? parseInt(dataTime) : 0;
	var closeTime = localStorage.getItem('ftpopup');
	var currentTime = new Date().getTime();
	if (waitTime === 0 || !closeTime || (currentTime - closeTime) > waitTime * 60 * 60 * 1000) {
		$('#ftpopupex').modal({
			fadeDuration: 250,
			fadeDelay: 0.50
		});
		$('.jquery-modal.blocker').addClass('fox-popupex');
	}
	$('.close-modal, .blocker').on('click', function() {
		var closeTime = new Date().getTime();
		localStorage.setItem('ftpopup', closeTime);
	});
});