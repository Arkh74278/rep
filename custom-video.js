
document.addEventListener('DOMContentLoaded', function() {
    var videoThumbnail = document.querySelector('.video-thumbnail');
    var videoPlayer = document.querySelector('.video-js');

    if (videoThumbnail && videoPlayer) {
        videoThumbnail.addEventListener('click', function(event) {
            event.preventDefault();

            // check if the libraries have already been loaded
            if (!window.LIBRARIES_LOADED) {
                // load the video.js library
                let link1 = document.createElement('link');
                link1.href = 'https://vjs.zencdn.net/8.3.0/video-js.css';
                link1.type = 'text/css';
                link1.rel = 'stylesheet';
                document.head.appendChild(link1);

                let script1 = document.createElement('script');
                script1.src = 'https://vjs.zencdn.net/8.3.0/video.min.js';
                document.body.appendChild(script1);

                // load the Magnific Popup library
                let link2 = document.createElement('link');
                link2.href = 'https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.min.css';
                link2.type = 'text/css';
                link2.rel = 'stylesheet';
                document.head.appendChild(link2);

                let script2 = document.createElement('script');
                script2.src = 'https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js';
                script2.onload = function() {
                    // set a flag so we know the libraries have been loaded
                    window.LIBRARIES_LOADED = true;

                    // initialize the video player
                    videoPlayer.classList.add('vjs-show');
                    videoPlayer.play();
                };
                document.body.appendChild(script2);
            } else {
                // initialize the video player
                videoPlayer.classList.add('vjs-show');
                videoPlayer.play();
            }
        });
    }
});
