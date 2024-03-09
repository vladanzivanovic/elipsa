import bellNotificationHandler from "../Handler/HeaderNotifications/BellNotificationHandler";

class HeaderEvents {
    #bellNotificationHandler;
    constructor() {
        this.#bellNotificationHandler = bellNotificationHandler;

        this.#registerEvents();
    }

    #registerEvents() {
        $(document).ready(async () => {
            await this.#bellNotificationHandler.notifiy();
        })

        setInterval(async () => {
            await this.#bellNotificationHandler.notifiy();
        }, 30000);
    }
}
export default HeaderEvents;
