import CoreController from "../../../js/CoreController";
import ShopPageController from "./ShopPageController";

let routes = [
    {
        name: 'site.shop_page',
        controller: ShopPageController,
    },
];

$(document).ready(() => {
    let route = matchRoute();

    let core = new CoreController();

    core.showFlashMsg();
    if (route) {
        new route.controller();
    }
});

let matchRoute = () => {
    for(let i in routes) {
        let route = routes[i];

        if (route.name === ROUTE_NAME) {
            return route;
        }
    }
};