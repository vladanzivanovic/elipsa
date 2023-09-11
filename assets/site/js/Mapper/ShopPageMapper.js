class ShopPageMapper {
    constructor() {
        if(!ShopPageMapper.instance) {
            // this.category           = $('.category-btn');
            // this.color              = $('.color-btn');
            // this.collection = $('a[data-search-name="collection"]');
            // this.size               = $('.size-btn');
            this.sortOption = '.sort-option';
            this.limit = '.limit-product';
            this.searchView = $('.search-criteria');
            this.selectedCriteria = $('.selected-filter-btn');
            // this.priceRange        = $('#shop-slider-range');
            // this.amountPrice       = $('#shop-amount');
            this.filterBtnOpen = '#filter-btn-open';
            this.filterBtnClose = '.filter-close';
            // this.filterCounter = '.filter-counter';

            ShopPageMapper.instance = this;
        }

        return ShopPageMapper.instance;
    }
}

const shopPageMapper = new ShopPageMapper();

Object.freeze(shopPageMapper);

export default shopPageMapper;
