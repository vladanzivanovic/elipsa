
class ShopFilterMapper {
    constructor() {
        if (!ShopFilterMapper.instance) {
            this.collections = 'a[data-search-name="collections"]';
            this.seasons = 'a[data-search-name="seasons"]';
            this.attributes = 'a[data-search-name="attributes"]';
            this.colors = 'a[data-search-name="colors"]';
            this.sizes = 'a[data-search-name="sizes"]';
            this.filterCounter = '.filter-counter';
            this.priceRange = '#shop-slider-range';
            this.priceInput = '#filter-price';
            this.searchCriteria = '.search-criteria';
            this.categories = '.category-btn';

            ShopFilterMapper.instance = this;
        }

        return ShopFilterMapper.instance;
    }
}

const shopFilterMapper = new ShopFilterMapper();

Object.freeze(shopFilterMapper);

export default shopFilterMapper;
