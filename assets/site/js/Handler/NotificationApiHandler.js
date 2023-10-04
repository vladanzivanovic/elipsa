import AppHelperService from "../../../js/Helper/AppHelperService";

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
            url: AppHelperService.generateLocalizedUrl('site_api.set_notification'),
            data: JSON.stringify(data),
            dataType: 'json',
            contentType: 'application/json',
            headers: {
                'Content-Language': LOCALE,
            }
        })
    }
}

const notificationApiHandler = new NotificationApiHandler();

Object.freeze(notificationApiHandler);

export default notificationApiHandler;
