class NavBarMapper {
    constructor() {
        if(!NavBarMapper.instance) {
            this.notification = {
                bellBtn: '#bell-notifications-drop-down',
                indicator: '.indicator',
                bellBody: '.bell-notification-list-body',
            };

            NavBarMapper.instance = this;
        }

        return NavBarMapper.instance;
    }
}

const navBarMapper = new NavBarMapper();

Object.freeze(navBarMapper);

export default navBarMapper;
