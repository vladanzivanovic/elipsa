class FlexSliderService {
    static setProductSlider(thumbImageWrapper, fullImageWrapper) {

        if (!IS_MOBILE) {
            thumbImageWrapper.flexslider({
                direction    : 'vertical',
                animation    : "slide",
                controlNav   : false,
                directionNav : false,
                animationLoop: false,
                slideshow    : false,
                asNavFor     : '#sliders',
                after        : function (slider) {

                    const prevItemIndex = slider.currentItem - 1;
                    const nextItemIndex = slider.currentItem + 1;
                    const parent = slider[0];

                    if (prevItemIndex < 0 ||
                        nextItemIndex >= slider.slides.length ||
                        ($(slider.slides[prevItemIndex]).offset().top > 0 &&
                            $(slider.slides[nextItemIndex]).offset().top <= $(parent).height())
                    ) {
                        return;
                    }

                    slider.flexAnimate(slider.currentItem - 1, slider.vars.pauseOnAction);
                    $(slider.slides[slider.currentItem]).addClass('flex-active-slide');
                    $(slider.slides[slider.currentItem - 1]).removeClass('flex-active-slide');
                }
            })
        }

        const fullImageOptions = {
            animation: "slide",
            direction: 'horizontal',
            prevText: '',
            nextText: '',
        };

        if (!IS_MOBILE) {
            fullImageOptions.sync = '#carousel';
            fullImageOptions.direction = 'vertical';
            fullImageOptions.controlNav = false;
            fullImageOptions.animationLoop = false;
            fullImageOptions.slideshow = false;

        }

        fullImageWrapper.flexslider(fullImageOptions);
    }
}

export default FlexSliderService;