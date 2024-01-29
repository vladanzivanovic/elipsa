import myAccountPageMapper from "../Mapper/MyAccountPageMapper";
import MyAccountService from "../Service/MyAccountService";
import myAccountValidator from "../Validators/MyAccountValidator";
import myAccountHandler from "../Handler/MyAccount/MyAccountHandler";

require('inputmask/dist/jquery.inputmask.bundle');

class MyAccountPageController {
    #validator;

    constructor() {
        this.mapper = myAccountPageMapper;
        this.service = new MyAccountService();
        this.handler = myAccountHandler;
        this.#validator = myAccountValidator;

        this.#showTab();

        $(this.mapper.personalFormFields.mobilePhone).inputmask({mask: '(999) 99 999-999[9]', autoUnmask: true, clearIncomplete: true});
        $(this.mapper.personalInfoFields.mobilePhone).inputmask({mask: '(999) 99 999-999[9]', autoUnmask: true, clearIncomplete: true});
        $(this.mapper.personalFormFields.zipCode).inputmask({mask: '99999', autoUnmask: true, clearIncomplete: true});

        this.#validator.validate();

        this.registerEvents();
    }

    registerEvents() {
        $(this.mapper.personalSaveBtn).on('click', e => {
            this.handler.update();
        });

        $(this.mapper.personalChangeBtn).on('click', e => {
            this.#showPersonalForm();
        });

        $(this.mapper.personalCancelBtn).on('click', e => {
            this.#hidePersonalForm();
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

        $('a[data-toggle="tab"]').on('shown.bs.tab', e => {
            this.#hidePersonalForm();
        });
    }

    #showPersonalForm() {
        $(this.mapper.personalFormBoard).removeClass('hide');
        $(this.mapper.personalInfoBoard).addClass('hide');
    }

    #hidePersonalForm() {
        $(this.mapper.personalFormBoard).addClass('hide');
        $(this.mapper.personalInfoBoard).removeClass('hide');

        $(this.mapper.personalFormFields.firstName).val(USER.first_name);
        $(this.mapper.personalFormFields.lastName).val(USER.last_name);
        $(this.mapper.personalFormFields.email).val(USER.email);
        $(this.mapper.personalFormFields.address).val(USER.address ? USER.address.street : '');
        $(this.mapper.personalFormFields.city).val(USER.address ? USER.address.city : '');
        $(this.mapper.personalFormFields.country).val(USER.address ? USER.address.country : '');
        $(this.mapper.personalFormFields.zipCode).val(USER.address ? USER.address.post_code : '');
        $(this.mapper.personalFormFields.password).val('');
    }

    #showTab() {
        const hash = location.hash;

        this.#hidePersonalForm();

        if (!hash) {
            return;
        }
        $(`.my-account-welcome a[href="${hash}"]`).tab('show');
    }
}

export default MyAccountPageController
