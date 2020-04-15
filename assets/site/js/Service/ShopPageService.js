import AppHelperService from "../../../js/Helper/AppHelperService";
import ShopPageDom from "../Dom/ShopPageDom";
import ShopPageMapper from "../Mapper/ShopPageMapper";

class ShopPageService {
    constructor() {
        this.dom = ShopPageDom;
        this.mapper = new ShopPageMapper();

        let selectedPrices = [0,5000];

        if (SEARCH_CRITERIA.hasOwnProperty('price')) {
            selectedPrices = SEARCH_CRITERIA.price[0].split('-');
        }

        $.scrollUp.init({
            scrollTrigger: $('<a/>', {
                id: 'scroll-to-products',
                href: '#products',
                class: 'hide',
            }),
            scrollTarget: 50,
            easingType: 'linear',
            scrollSpeed: 1500,
            animation: 'fade'
        });

        this.mapper.priceRange.slider({
            range: true,
            min: 0,
            max: PRICES[1],
            values: selectedPrices,
            slide: (event, ui) => {
                this.mapper.amountPrice.val(`${ui.values[0]} RSD - ${ui.values[1]} RSD`);
            }
        });
        this.mapper.amountPrice.val(`${this.mapper.priceRange.slider("values", 0)} RSD - ${this.mapper.priceRange.slider("values", 1)} RSD`);
    }

    applyFilter(url)
    {
        $('#shop-loader').fadeOut('slow', function() { $(this).removeClass('hide'); });
        $.ajax({
            type: 'GET',
            url: url,
            dataType: 'json',
            success: response => {
                $('.grid-items > .row').empty()
                    .append(this.dom.generateProducts(response));

                $('#scroll-to-products').trigger('click');
                $('#shop-loader').addClass('hide');
            },
            error: error => {

            }
        })
    }
}

export default ShopPageService;