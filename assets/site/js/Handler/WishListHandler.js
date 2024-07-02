class WishListHandler {
    toggle(elm) {
        const productId = $(elm).data('product-id');
        const page = $(elm).data('page');

        let urlRoute = Routing.generate(`site_api.toggle_wish.${LOCALE}`, {productId: productId});
        let type = 'POST';
        let result;

        // loader.show();

        return $.ajax({
            type,
            url: urlRoute,
            data: null,
            // success: (response) => {
            //     console.log(response);
            //     const action = response.is_added ? 'add' : 'remove';
            //
            //     this.notification.show('success', Translator.trans(`wish_list.success.${action}.message`, null, 'messages', LOCALE), true);
            //
            //     if (page === 'shop') {
            //         if (response.is_added) {
            //             $(elm).find('.fa-heart-o').addClass('fa-heart');
            //             $(elm).find('.fa-heart-o').removeClass('fa-heart-o');
            //         } else {
            //             $(elm).find('.fa-heart').addClass('fa-heart-o');
            //             $(elm).find('.fa-heart').removeClass('fa-heart');
            //         }
            //     }
            //
            //     if (page === 'account') {
            //         $(`.table-row-${productId}`).remove();
            //     }
            //
            //     loader.hide();
            // },
            // error: (error) => {
            //     loader.hide();
            // }
        })

            // loader.hide();
    }
}

export default WishListHandler;
