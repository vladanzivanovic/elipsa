class ProductPageMapper {
    constructor() {
        if (!ProductPageMapper.instance) {
            this.color          = $('.color-btn');
            this.colorActive    = $('.color-btn.active');
            this.size           = $('.size-btn');
            this.sizeActive     = $('.size-btn.active');
            this.largeImage     = $('.large-image');
            this.thumbImage     = $('.thumb-image');
            this.quantity       = $('#product-quantity');
            this.addBtn         = $('.spd-add-to');
            this.quantityBtn    = $('.qtybutton', $(document));
            this.youtubeModal = '#youtubeModal';
            this.youtubeCarousel = '.youtube-carousel';
            this.youtubeArea = '.youtube-area'

            ProductPageMapper.instance = this;
        }

        return ProductPageMapper.instance;
    }
}

const instance = new ProductPageMapper();

Object.freeze(instance);

export default instance;
