import myAccountPageMapper from "../Mapper/MyAccountPageMapper";
import MyAccountService from "../Service/MyAccountService";
import UserHandler from "../Handler/UserHandler";

class MyAccountPageController {
    constructor() {
        this.mapper = myAccountPageMapper;
        this.service = new MyAccountService();
        this.handler = new UserHandler();

        this.#showTab();

        this.registerEvents();
    }

    registerEvents() {
        $(this.mapper.personalBtn).on('click touchend', e => {
            this.handler.doUpdate(this.mapper.personalForm);
        });

        /**
         * In order to remove hash tag from url on tab click
         * we are tracking click on each tab
         */
        $('.my-account-welcome a').on('click', e => {
            location.hash = '';
            history.pushState('', document.title, `${location.pathname}`);
        })

        $(window).on('hashchange', e => {
           this.#showTab();
            $('#scrollUp').click();
        });
        // $(this.mapper.orderTab).on('show.bs.tab', e => {
        //     this.service.getList();
        // })
    }

    #showTab() {
        const hash = location.hash;

        if (!hash) {
            return;
        }

        $(`.my-account-welcome a[href="${hash}"]`).tab('show');
    }
}

export default MyAccountPageController
