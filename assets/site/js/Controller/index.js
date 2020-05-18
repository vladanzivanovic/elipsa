import CoreController from "../CoreController";
import ShopPageController from "./ShopPageController";
import ProductPageController from "./ProductPageController";
import CheckoutPageController from "./CheckoutPageController";
import CartPageController from "./CartPageController";
import LocationPageController from "./LocationPageController";

let routes = [
    {
        name: 'site.shop_page',
        controller: ShopPageController,
    },
    {
        name: 'site.product_page',
        controller: ProductPageController,
    },
    {
        name: 'site.cart_page',
        controller: CartPageController,
    },
    {
        name: 'site.checkout_page',
        controller: CheckoutPageController,
    },
    {
        name: 'site.location_page',
        controller: LocationPageController,
    },
];

$(document).ready(() => {
    let route = matchRoute();

    let core = new CoreController();

    core.baseCore.showFlashMsg();
    core.siteMobileMenu();

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