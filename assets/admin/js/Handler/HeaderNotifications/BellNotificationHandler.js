import bellNotificationApiHandler from "./BellNotificationApiHandler";
import bellNotificationDom from "../../Dom/BellNotificationDom";

class BellNotificationHandler {
    #apiHandler;
    #bellDom;

    constructor(props)
    {
        if(!BellNotificationHandler.instance) {
            this.#apiHandler = bellNotificationApiHandler;
            this.#bellDom = bellNotificationDom;

            BellNotificationHandler.instance = this;
        }

        return BellNotificationHandler.instance;
    }

    async notifiy()
    {
        const notificationData = await this.#apiHandler.getNotifications();

        this.#bellDom.generate(notificationData);
    }
}

const bellNotificationHandler = new BellNotificationHandler();

Object.freeze(bellNotificationHandler);

export default bellNotificationHandler;
