var wpurp = wpurp || {};

wpurp.timer_seconds = 0;
wpurp.timer_seconds_remaining = 0;
wpurp.timer_alarm_sound = 'data:audio/wav;base64,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qJrx4KOQjahZxWbcZgztj2c49nKmkId44S71j0c8eV9yDK6uPRzx5X18eDvjvQ6yKo9ZSS6l//8elePK/Lf//IInrOF/FvDoADYAGBMGb7FtErm5MXMlmPAJQVgWta7Zx2go+8xJ0UiCb8LHHdftWyLJE0QIAIsI+UbXu67dZMjmgDGCGl1H+vpF4NSDckSIkk7Vd+sxEhBQMRU8j/12UIRhzSaUdQ+rQU5kGeFxm+hb1oh6pWWmv3uvmReDl0UnvtapVaIzo1jZbf/pD6ElLqSX+rUmOQNpJFa/r+sa4e/pBlAABoAAAAA3CUgShLdGIxsY7AUABPRrgCABdDuQ5GC7DqPQCgbbJUAoRSUj+NIEig0YfyWUho1VBBBA//uQZB4ABZx5zfMakeAAAAmwAAAAF5F3P0w9GtAAACfAAAAAwLhMDmAYWMgVEG1U0FIGCBgXBXAtfMH10000EEEEEECUBYln03TTTdNBDZopopYvrTTdNa325mImNg3TTPV9q3pmY0xoO6bv3r00y+IDGid/9aaaZTGMuj9mpu9Mpio1dXrr5HERTZSmqU36A3CumzN/9Robv/Xx4v9ijkSRSNLQhAWumap82WRSBUqXStV/YcS+XVLnSS+WLDroqArFkMEsAS+eWmrUzrO0oEmE40RlMZ5+ODIkAyKAGUwZ3mVKmcamcJnMW26MRPgUw6j+LkhyHGVGYjSUUKNpuJUQoOIAyDvEyG8S5yfK6dhZc0Tx1KI/gviKL6qvvFs1+bWtaz58uUNnryq6kt5RzOCkPWlVqVX2a/EEBUdU1KrXLf40GoiiFXK///qpoiDXrOgqDR38JB0bw7SoL+ZB9o1RCkQjQ2CBYZKd/+VJxZRRZlqSkKiws0WFxUyCwsKiMy7hUVFhIaCrNQsKkTIsLivwKKigsj8XYlwt/WKi2N4d//uQRCSAAjURNIHpMZBGYiaQPSYyAAABLAAAAAAAACWAAAAApUF/Mg+0aohSIRobBAsMlO//Kk4soosy1JSFRYWaLC4qZBYWFRGZdwqKiwkNBVmoWFSJkWFxX4FFRQWR+LsS4W/rFRb/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////VEFHAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAU291bmRib3kuZGUAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAMjAwNGh0dHA6Ly93d3cuc291bmRib3kuZGUAAAAAAAAAACU=';
wpurp.timer = undefined;
wpurp.alarm_timer = undefined;

wpurp.timer_play = function() {
    jQuery('#wpurp-timer-play').hide();
    jQuery('#wpurp-timer-pause').show();

    clearInterval(wpurp.timer);
    wpurp.timer = setInterval(wpurp.update_timer, 1000);
};

wpurp.timer_pause = function() {
    jQuery('#wpurp-timer-pause').hide();
    jQuery('#wpurp-timer-play').show();
    
    clearInterval(wpurp.timer);
};

wpurp.update_timer = function() {
    wpurp.timer_seconds_remaining--;
    if(wpurp.timer_seconds_remaining <= 0) {
        wpurp.timer_seconds_remaining = 0;
        wpurp.timer_finished();
    }

    jQuery('#wpurp-timer-remaining').text(wpurp.timer_seconds_to_hms(wpurp.timer_seconds_remaining));

    var percentage_elapsed = 100 * (wpurp.timer_seconds - wpurp.timer_seconds_remaining) / wpurp.timer_seconds;
    jQuery('#wpurp-timer-bar-elapsed').css('width', percentage_elapsed + '%');
};

wpurp.timer_finished = function() {
    // Clear existing timers.
    wpurp.timer_pause();
    clearInterval(wpurp.alarm_timer);

    // Keep sounding alarm and pulsate background until closed.
    wpurp.timer_finished_sequence();
    wpurp.timer = setInterval(wpurp.timer_finished_sequence, 2000);
};

wpurp.timer_finished_sequence = function() {
    wpurp.timer_play_alarm();

    jQuery('#wpurp-timer-container')
        .animate({ opacity: 0.5 }, 500 )
        .animate({ opacity: 1 }, 500 )
        .animate({ opacity: 0.5 }, 500 )
        .animate({ opacity: 1 }, 500 );
};

wpurp.timer_play_alarm = function() {
    var alarm = new Audio(wpurp.timer_alarm_sound);
    wpurp.alarm_timer = setInterval(function() { alarm.play() }, 250);
    setTimeout(function() { clearInterval(wpurp.alarm_timer); }, 1250);
};

wpurp.open_timer = function(seconds) {
    wpurp.remove_timer(function() {
        if(seconds > 0) {
            wpurp.timer_seconds = seconds;
            wpurp.timer_seconds_remaining = seconds;

            var timer = jQuery('<div id="wpurp-timer-container"></div>').hide(),
                play = jQuery('<span id="wpurp-timer-play" class="wpurp-timer-icon">' + wpurp_timer.icons.play + '</span>'),
                pause = jQuery('<span id="wpurp-timer-pause" class="wpurp-timer-icon">' + wpurp_timer.icons.pause + '</span>'),
                time_remaining = jQuery('<span id="wpurp-timer-remaining"></span>'),
                bar = jQuery('<span id="wpurp-timer-bar-container"><span id="wpurp-timer-bar"><span id="wpurp-timer-bar-elapsed"></span></span></span>'),
                close = jQuery('<span id="wpurp-timer-close" class="wpurp-timer-icon">' + wpurp_timer.icons.close + '</span>');

            time_remaining.text(wpurp.timer_seconds_to_hms(seconds));

            timer
                .append(play)
                .append(pause)
                .append(time_remaining)
                .append(bar)
                .append(close);

            jQuery('body').append(timer);
            wpurp.timer_play();
            timer.fadeIn();
        }
    });
};

wpurp.remove_timer = function(callback) {
    clearInterval(wpurp.timer);
    clearInterval(wpurp.alarm_timer);
    var timer = jQuery('#wpurp-timer-container');

    if(timer.length > 0) {
        timer.clearQueue();
        timer.fadeOut(400, function() {
            timer.remove();
            if(callback !== undefined) {
                callback();
            }
        });
    } else {
        if(callback !== undefined) {
            callback();
        }
    }
}

wpurp.timer_seconds_to_hms = function(s) {
    var h = Math.floor(s/3600);
    s -= h*3600;
    var m = Math.floor(s/60);
    s -= m*60;
    return (h < 10 ? '0'+h : h)+":"+(m < 10 ? '0'+m : m)+":"+(s < 10 ? '0'+s : s);
}


jQuery(document).ready(function($) {
	jQuery(document).on('click', '.wpurp-timer-link', function(e) {
		e.preventDefault();
		e.stopPropagation();

        var seconds = parseInt(jQuery(this).find('.wpurp-timer').data('seconds'));
        wpurp.open_timer(seconds);
	});

	jQuery(document).on('click', '#wpurp-timer-play', function(e) {
		wpurp.timer_play();
	});
    jQuery(document).on('click', '#wpurp-timer-pause', function(e) {
		wpurp.timer_pause();
	});
    jQuery(document).on('click', '#wpurp-timer-close', function(e) {
		wpurp.remove_timer();
	});
});
