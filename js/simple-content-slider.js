(function($) { // Use an IIFE (Immediately Invoked Function Expression) to safely use jQuery's $

    $(document).ready(function() {
        // Find all slider instances on the page. Each wrapper has a unique ID.
        $('.scs-slider-wrapper').each(function() {
            var $sliderWrapper = $(this);
            var $sliderContainer = $sliderWrapper.find('.scs-slider-container'); // The element Slick is applied to
            var $slides = $sliderContainer.find('.scs-slides'); // The UL containing the slides

            // Check if there are slides within this specific container
            if ($slides.length > 0 && $slides.children('li').length > 0) {
                // Get autoplaySpeed from data attribute (set by PHP)
                var autoplaySpeed = parseInt($sliderWrapper.data('autoplay-speed'), 10) || 4000;

                // Initialize Slick Carousel on the .scs-slides element
                $slides.slick({
                    // Basic Slick Carousel options
                    dots: true,             // Show navigation dots
                    arrows: true,           // Show previous/next arrows
                    infinite: true,         // Loop the slides infinitely
                    speed: 500,             // Speed of the slide transition in ms
                    slidesToShow: 1,        // Number of slides to show at a time
                    slidesToScroll: 1,      // Number of slides to scroll at a time
                    autoplay: true,         // Enable automatic playback
                    autoplaySpeed: autoplaySpeed, // Time between automatic slides in ms (default 4 seconds)
                    pauseOnHover: true,     // Pause autoplay on hover
                    adaptiveHeight: false,   // Adjust slider height to the current slide

                    // Responsive settings (optional, but recommended)
                    // This allows you to adjust the slider's behavior on different screen sizes
                    responsive: [
                        {
                            breakpoint: 768, // For screens smaller than 768px
                            settings: {
                                slidesToShow: 1,
                                slidesToScroll: 1,
                                arrows: false, // Hide arrows on smaller screens; dots are often sufficient
                                dots: true
                            }
                        },
                        {
                            breakpoint: 480, // For screens smaller than 480px
                            settings: {
                                slidesToShow: 1,
                                slidesToScroll: 1,
                                arrows: false,
                                dots: true
                            }
                        }
                        // You can add more breakpoints if needed
                    ]
                });

                // --- Handle moving the slider if data-move-target-selector is set ---
                var moveTargetSelector = $sliderWrapper.data('move-target-selector');
                if (moveTargetSelector && moveTargetSelector.length > 0) {
                    var $targetElement = $(moveTargetSelector).first(); // Get the first matching target

                    if ($targetElement.length > 0) {
                        // Move the entire wrapper into the target element
                        $targetElement.append($sliderWrapper);
                        // Make the wrapper visible now that it's moved (it was hidden via inline style)
                        $sliderWrapper.css({ 'display': 'block', 'visibility': 'visible' });

                        // Slick might need to be re-initialized or 'setPosition' called after being moved
                        // if it was initialized while hidden or in a different DOM position.
                        // For complex moves, a 'reInit' or 'setPosition' might be safer.
                        // $slides.slick('setPosition'); // Recalculate positions
                        // Or, if dimensions change significantly, you might need to unslick and re-slick.
                    } else {
                        // Target element not found, make sure the slider is visible in its original place
                        // if it wasn't already (though it should be if no move target)
                        $sliderWrapper.css({ 'display': 'block', 'visibility': 'visible' });
                        // console.warn('Simple Content Slider: Move target selector "' + moveTargetSelector + '" not found.');
                    }
                }
                // --- End of move logic ---

            } // end if $slides.length > 0
        }); // end .each()
    }); // end document.ready

})(jQuery);
