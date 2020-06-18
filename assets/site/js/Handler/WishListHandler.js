import NotificationService from "../../../js/NotificationService";
import loader from "../Dom/LoaderDom";

class WishListHandler {
    constructor() {
        this.notification = NotificationService();
    }

    toggle(elm) {
        if(!window.USER) {
            this.notification.show('error', Translator.trans(`login_required`, null, 'messages', LOCALE), true);

            return;
        }
        const productId = $(elm).data('product-id');
        const page = $(elm).data('page');
        let urlRoute = Routing.generate('site_api.toggle_wish', {id: window.USER.id});
        let type = 'POST';
        const data = [{name: 'product_id', value: productId}];

        loader.show();

        $.ajax({
            type,
            url: urlRoute,
            data,
            dataType: 'json',
            success: (response) => {
                const action = response.is_added ? 'add' : 'remove';

                this.notification.show('success', Translator.trans(`wish_list.success.${action}.message`, null, 'messages', LOCALE), true);

                if(page === 'shop') {
                    if (response.is_added) {
                        $(elm).find('.fa-heart-o').addClass('fa-heart');
                        $(elm).find('.fa-heart-o').removeClass('fa-heart-o');
                    } else {
                        $(elm).find('.fa-heart').addClass('fa-heart-o');
                        $(elm).find('.fa-heart').removeClass('fa-heart');
                    }
                }

                if (page === 'account') {
                    $(`.table-row-${productId}`).remove();
                }

                loader.hide();
            },
            error: (error) => {
                loader.hide();
            }
        })
    }
}

export default WishListHandler;