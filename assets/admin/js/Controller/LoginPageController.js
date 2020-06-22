import loginPageMapper from "../Mapper/LoginPageMapper";
import LoginHandler from "../Handler/LoginHandler";

class LoginPageController {
    constructor() {
        this.mapper = loginPageMapper;
        this.handler = new LoginHandler();

        this.registerEvents();
    }

    registerEvents()
    {
        $(this.mapper.submitBtn).on('click touchend', (e) => {
            e.preventDefault();
            e.stopPropagation();

            this.handler.doLogin();
        });
    }
}

export default LoginPageController;