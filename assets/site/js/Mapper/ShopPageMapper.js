class ShopPageMapper {
    constructor() {
        this.category           = $('.category-btn');
        this.color              = $('.color-btn');
        this.size               = $('.size-btn');
        this.sortOption         = $('.sort-option');
        this.limit              = $('.limit-product');
        this.searchView        = $('.search-criteria');
        this.selectedCriteria  = $('.selected-filter-btn');
        this.priceRange        = $('#shop-slider-range');
        this.amountPrice       = $('#shop-amount');
    }
}

export default ShopPageMapper;