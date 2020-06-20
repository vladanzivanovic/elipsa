import CoreController from "../CoreController";
import ShopPageController from "./ShopPageController";
import ProductPageController from "./ProductPageController";
import CheckoutPageController from "./CheckoutPageController";
import CartPageController from "./CartPageController";
import LocationPageController from "./LocationPageController";
import BlogListPageController from "./BlogListPageController";
import LoyaltyPageController from "./LoyaltyPageController";
import MyAccountPageController from "./MyAccountPageController";
import AskUsPageController from "./AskUsPageController";
import HomePageController from "./HomePageController";
import CatalogPageController from "./CatalogPageController";
import ContactPageController from "./ContactPageController";

let routes = [
    {
        name: 'site.home_page',
        controller: HomePageController,
    },
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
    {
        name: 'site.blog_list_page',
        controller: BlogListPageController,
    },
    {
        name: 'site.loyalty',
        controller: LoyaltyPageController,
    },
    {
        name: 'site.trendy_page',
        controller: ShopPageController,
    },
    {
        name: 'site.account_page',
        controller: MyAccountPageController,
    },
    {
        name: 'site.ask_us',
        controller: AskUsPageController,
    },
    {
        name: 'site.catalog_page',
        controller: CatalogPageController,
    },
    {
        name: 'site.contact_page',
        controller: ContactPageController,
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