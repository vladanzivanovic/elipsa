import navBarMapper from "../Mapper/NavBarMapper";

const feather = require ('feather-icons');

class BellNotificationDom {
    #mapper;

    constructor() {
        if(!BellNotificationDom.instance) {
            this.#mapper = navBarMapper;
            BellNotificationDom.instance = this;
        }

        return BellNotificationDom.instance;
    }

    generate(data) {
        let html = '';

        $(`${this.#mapper.notification.bellBtn} ${this.#mapper.notification.indicator}`).addClass('d-none');
        $(this.#mapper.notification.bellBody).empty();

        if (data.orders > 0) {
            $(`${this.#mapper.notification.bellBtn} ${this.#mapper.notification.indicator}`).removeClass('d-none');
            html += this.#generateOrderNotification(data.orders);
        }

        $(this.#mapper.notification.bellBody).html(html);
        feather.replace();
    }

    #generateOrderNotification(noOfOrders)
    {
        const text = 1 === noOfOrders ? `1 nova porudžbina` : `${noOfOrders} novih porudžbina`;

        let html = `
            <a href="${Routing.generate('admin.orders')}" class="dropdown-item">
                <div class="icon">
                    <i data-feather="gift"></i>
                </div>
                <div class="content">
                    <p>${text}</p>
<!--                    <p class="sub-text text-muted">30 min ago</p>-->
                </div>
            </a>
        `;

        return html;
    }
}
const bellNotificationDom = new BellNotificationDom();

Object.freeze(bellNotificationDom);

export default bellNotificationDom;
