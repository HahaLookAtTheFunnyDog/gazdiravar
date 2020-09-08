(function ($) {

    window.addEventListener('resize', function () {
        var videoElement = document.querySelector('video#wp-custom-header-video') || document.querySelector('iframe#wp-custom-header-video');
        if (videoElement) {
            videoSize(videoElement);
        }
    });

    function videoSize(videoElement, animate) {
        var $videoElement = jQuery(videoElement);
        var $header = jQuery(".header-homepage.cp-video-bg");
        var videoWidth = $header.width(),
            videoHeight = $header.height();

            videoWidth = Math.max(videoWidth,videoHeight);            

        if (videoWidth < videoHeight * 16 / 9) {
            videoWidth = 16 / 9 * videoHeight;
        } else {
            videoHeight = videoWidth * 9 / 16;
        }

        var marginLeft = -0.5 * (videoWidth - $header.width());

        $videoElement.css({
            width: videoWidth,
            height: videoHeight,
            "opacity": 1,
            "left": marginLeft
        });

        if (animate === false) {
            return;
        }


    }

    jQuery(function () {
        var videoElement = document.querySelector('video#wp-custom-header-video') || document.querySelector('iframe#wp-custom-header-video');
        if (videoElement) {
            videoSize(videoElement, false);
        }
    });

    __cpVideoElementFirstPlayed = false;

    document.addEventListener('wp-custom-header-video-loaded', function () {
        var videoElement = document.querySelector('video#wp-custom-header-video');

        if (videoElement) {
            videoSize(videoElement);
            return;
        }

        document.querySelector('#wp-custom-header').addEventListener('play', function () {
            var iframeVideo = document.querySelector('iframe#wp-custom-header-video');
            var videoElement = document.querySelector('video#wp-custom-header-video') || iframeVideo;
          
            if (videoElement && !__cpVideoElementFirstPlayed) {
                __cpVideoElementFirstPlayed = true;
                videoSize(videoElement);
            }
          
        });

    });
})(jQuery);