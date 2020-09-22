import AppHelperService from "../../../js/Helper/AppHelperService";
import CartDom from "../Dom/CartDropDownDom";
import cartPageMapper from "../Mapper/CartPageMapper";
import NotificationService from "../../../js/NotificationService";
import loader from "../Dom/LoaderDom";

class CartHandler {
    constructor() {
        this.cartDom = CartDom;
        this.pageMapper = cartPageMapper;
        this.notification = NotificationService();
    }

    update() {
        const data = [];
        $.each($(this.pageMapper.quatityInput), (index, elm) => {
            data.push({
                name: $(elm).attr('name'),
                value: $(elm).val(),
            })
        });

        $('#page-loader').fadeOut('slow', function() { $(this).removeClass('hide'); });

        $.ajax({
            type: 'PUT',
            url: AppHelperService.generateLocalizedUrl('site_api.update_order_products'),
            data,
            dataType: 'json',
            success: response => {
                this.updateProductsPrices();
                $('#page-loader').addClass('hide');
            },
            error: error => {
                $('#page-loader').addClass('hide');
            }
        })
    }

    remove(id, elm) {
        let urlRoute = AppHelperService.generateLocalizedUrl('site_api.remove_order_product', {id});
        let type = 'DELETE';

        $('#page-loader').fadeOut('slow', function() { $(this).removeClass('hide'); });

        $.ajax({
            type,
            url: urlRoute,
            dataType: 'json',
            success: (response) => {
                this.cartDom.removeProduct(id);

                if (elm) {
                    elm.remove();
                    this.updateProductsPrices();
                }
                $('#page-loader').addClass('hide');
            },
            error: (error) => {
                $('#page-loader').addClass('hide');
            }
        })
    }

    setCoupon() {
        loader.show();
        this.pageMapper.promoCouponErrorText.empty();

        $.ajax({
            type: 'PATCH',
            url: AppHelperService.generateLocalizedUrl('site_api.set_order_coupon', {code: this.pageMapper.promoCouponInput.val()}),
            dataType: 'json',
            success: response => {
                let total = parseInt(this.pageMapper.total.data('totalPrice'));
                let shippingPrice = 0;
                let totalWithShipping = Math.round(total);

                window.DISCOUNT = response.discount;
                const totalWithDiscount = total - (total * (window.DISCOUNT/100));

                this.pageMapper.promoCouponPrice.text(response.discount);
                this.pageMapper.promoCouponPrice.closest('.promo-coupon-holder').removeClass('hide');

                if (totalWithDiscount < FREE_SHIPPING) {
                    shippingPrice = SHIPPING;
                    totalWithShipping = totalWithDiscount + SHIPPING;
                }

                this.pageMapper.shippingPrice.text(AppHelperService.formatPrice(shippingPrice));

                this.pageMapper.totalShipping.data('totalWithShipping', totalWithShipping);
                this.pageMapper.totalShipping.text(AppHelperService.formatPrice(Math.round(totalWithShipping)));

                loader.hide();
            },
            error: error => {
                if (error.responseJSON.message) {
                    this.pageMapper.promoCouponErrorText.text(Translator.trans(error.responseJSON.message, null, 'validators', LOCALE));
                } else {
                    this.pageMapper.promoCouponErrorText.text(Translator.trans('promo_coupon.not_found', null, 'validators', LOCALE));
                }

                loader.hide();
            }
        })
    }

    updateProductsPrices() {
        let total = 0;
        let totalWithDiscount = null;

        $.each($('.product_price_value'), (index, elm) => {
            const tr = $(elm).closest('tr');
            const quantity = tr.find('input').val();
            const productTotalPrice = tr.find('.product_price_total');
            let productTotal = quantity * parseInt($(elm).data('price'));

            productTotalPrice.text(AppHelperService.formatPrice(productTotal));

            total += parseInt(productTotal);
        });

        this.pageMapper.total.text(AppHelperService.formatPrice(total));

        let shippingPrice = 0;
        let totalWithShipping = Math.round(total);

        if (window.DISCOUNT > 0) {
            totalWithDiscount = total - (total * (window.DISCOUNT/100));
            totalWithShipping = Math.round(totalWithDiscount);

            if (totalWithDiscount < FREE_SHIPPING) {
                shippingPrice = SHIPPING;
                totalWithShipping = Math.round(totalWithDiscount + SHIPPING);
            }

            this.pageMapper.shippingPrice.text(AppHelperService.formatPrice(shippingPrice));

            this.pageMapper.totalShipping.data('totalWithShipping', totalWithShipping);
            this.pageMapper.totalShipping.text(AppHelperService.formatPrice(totalWithShipping));

            return true;
        }

        if (total < FREE_SHIPPING) {
            shippingPrice = SHIPPING;
            totalWithShipping = Math.round(total + SHIPPING);
        }

        this.pageMapper.shippingPrice.text(AppHelperService.formatPrice(shippingPrice));

        this.pageMapper.totalShipping.data('totalWithShipping', totalWithShipping);
        this.pageMapper.totalShipping.text(AppHelperService.formatPrice(totalWithShipping));
    }
}

export default CartHandler;