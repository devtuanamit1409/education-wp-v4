(function ($) {
    $.fn.lp_course_countdown = function () {
        var countdowns = this;

        for (var i = 0; i < countdowns.length; i++) {
			var _countdown = $(countdowns[i]);
			var speed = _countdown.attr('data-speed');
			var remain = parseInt(_countdown.attr('data-timestamp-remain'))*1000;
			var gmt = _countdown.attr( 'data-gmt' );
			var time = _countdown.attr('data-time');
			var showtext = _countdown.attr('data-showtext');
			var current_time = new Date();
			var expiryTime = current_time.getTime()+remain;
			var expiryDate = new Date(expiryTime);
			var gmt = -expiryDate.getTimezoneOffset() / 60;
			var options = {
				expiryDate : expiryDate,
				speed : speed ? speed : 500,
				gmt : parseFloat(gmt),
				showText : (showtext=='yes') ? 1 : 0,
                localization: {
                    days: lp_coming_soon_translation.days,
                    hours: lp_coming_soon_translation.hours,
                    minutes: lp_coming_soon_translation.minutes,
                    seconds: lp_coming_soon_translation.seconds
                }
            };
            _countdown.mbComingsoon(options);
        }
    };

    $(document).ready(function () {
        $('.learnpress-course-coming-soon').lp_course_countdown();
        if (typeof tinymce !== 'undefined') {
            setTimeout(function () {
                var $editor_cm = tinymce.get('_lp_coming_soon_msg');
                if ($editor_cm) {
                    $editor_cm.on('Change KeyUp', function (e, b) {
                        $('#' + this.settings.id).val(this.getContent())
                    });
                }
            }, 1000);
        }
    });

})(jQuery);