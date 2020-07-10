import myAccountPageMapper from "../Mapper/MyAccountPageMapper";
import orderTabDom from "../Dom/OrderTabDom";

class MyAccountService {
    constructor() {
        this.mapper = myAccountPageMapper;
        this.orderDom = orderTabDom;
    }

    getList() {
        $(`${this.mapper.orderTable} tbody`).empty();

        $.ajax({
            type: "POST",
            url: Routing.generate(`site_api.my_account.${LOCALE}`),
            dataType: 'json',
            success: response => {
                const ordersHtml = this.orderDom.generateOrderList(response.orders);

                $(`${this.mapper.orderTable} tbody`).append(ordersHtml);
            }
        })
    }
}

export default MyAccountService;