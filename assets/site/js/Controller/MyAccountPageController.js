import myAccountPageMapper from "../Mapper/MyAccountPageMapper";
import MyAccountService from "../Service/MyAccountService";
import UserHandler from "../Handler/UserHandler";

class MyAccountPageController {
    constructor() {
        this.mapper = myAccountPageMapper;
        this.service = new MyAccountService();
        this.handler = new UserHandler();

        this.registerEvents();
    }

    registerEvents() {
        $(this.mapper.personalBtn).on('click touchend', e => {
            this.handler.doUpdate(this.mapper.personalForm);
        });
        // $(this.mapper.orderTab).on('show.bs.tab', e => {
        //     this.service.getList();
        // })
    }
}

export default MyAccountPageController