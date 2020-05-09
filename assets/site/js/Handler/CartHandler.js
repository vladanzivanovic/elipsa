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
                let total = parseInt(this.pageMapper.total.text());
                window.DISCOUNT = response.discount;
                const totalWithDiscount = total - (total * (window.DISCOUNT/100));

                this.pageMapper.promoCouponPrice.text(response.discount);
                this.pageMapper.promoCouponPrice.closest('.promo-coupon-holder').removeClass('hide');

                const totalWithShipping = totalWithDiscount >= FREE_SHIPPING ?
                    totalWithDiscount :
                    totalWithDiscount + SHIPPING;

                this.pageMapper.shippingPrice.text(totalWithDiscount >= FREE_SHIPPING ? 0 : SHIPPING);
                this.pageMapper.totalShipping.text(Math.round(totalWithShipping));

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

            productTotalPrice.text(quantity * parseInt(elm.innerText));

            total += parseInt(productTotalPrice.text());
        });

        this.pageMapper.total.text(total);

        if (window.DISCOUNT > 0) {
            totalWithDiscount = total - (total * (window.DISCOUNT/100));
            this.pageMapper.shippingPrice.text(totalWithDiscount >= FREE_SHIPPING ? 0 : SHIPPING);
            this.pageMapper.totalShipping.text(totalWithDiscount >= FREE_SHIPPING ? Math.round(totalWithDiscount) : Math.round(totalWithDiscount + SHIPPING));

            return true;
        }

        this.pageMapper.totalShipping.text(total >= FREE_SHIPPING ? total : Math.round(total + SHIPPING));
    }
}

export default CartHandler;