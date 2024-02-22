class BellNotificationApiHandler {
    constructor() {
        if (!BellNotificationApiHandler.instance) {
            BellNotificationApiHandler.instance = this;
        }

        return BellNotificationApiHandler.instance;
    }

    async getNotifications()
    {
        let result;
        let route = Routing.generate('admin.get_bell_notifications');
        let type = 'GET';

        try {
            result = await $.ajax({
                type,
                url: route,
                data: null,
            })
        }catch (error) {
            result = error;
        }

        return result;
    }
}

const bellNotificationApiHandler = new BellNotificationApiHandler();

Object.freeze(bellNotificationApiHandler);

export default bellNotificationApiHandler;
