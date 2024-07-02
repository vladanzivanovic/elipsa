class NotificationApiHandler {
    constructor() {
        if(!NotificationApiHandler.instance) {
            NotificationApiHandler.instance = this;
        }

        return NotificationApiHandler.instance;
    }

    async notifyMe(type, payload, email)
    {
        const data = {type, payload, email};
        await $.ajax({
            type: 'POST',
            url: Routing.generate(`site_api.set_notification.${LOCALE}`),
            data: JSON.stringify(data),
        })
    }
}

const notificationApiHandler = new NotificationApiHandler();

Object.freeze(notificationApiHandler);

export default notificationApiHandler;
