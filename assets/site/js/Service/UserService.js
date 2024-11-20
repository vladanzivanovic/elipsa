class UserService {
    constructor() {
        if (!UserService.instance) {
            UserService.instance = this;
        }

        return UserService.instance;
    }

    async isUserExistsByEmail(email)
    {
        let result;

        try {
            result = await $.ajax({
                type: 'GET',
                url: Routing.generate(`site_api.user_exists.${LOCALE}`, {email}),
                dataType: 'json',
            })
        } catch (error) {
            result = error;
        }

        return result;
    }
}

const userService = new UserService();

Object.freeze(userService);

export default userService;
