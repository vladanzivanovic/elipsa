class HomePageController {
    constructor() {
        $('#top_carousel').owlCarousel(this.owlOptions());
        $('#bottom_carousel').owlCarousel(this.owlOptions());
    }

    owlOptions() {
       return {
            loop: true,
            margin: 0,
            responsiveClass: true,
            navigation: true,
            navText: ["<i class='fa fa-long-arrow-left'></i>", "<i class='fa fa-long-arrow-right'></i>"],
            nav: false,
            items: 1,
            smartSpeed: 2000,
            dots: false,
            autoplay: true,
            autoplayTimeout: 4000,
            center: false,
            responsive: {
                0: {
                    items: 2,
                    center: true,
                },
                480: {
                    items: 2,
                    center: true,
                },
                760: {
                    items: 3
                }
            }
        };
    }
}

export default HomePageController;