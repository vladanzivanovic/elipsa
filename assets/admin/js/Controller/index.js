import DashboardController from './DashboardController';
import CoreController from "../../../js/CoreController";
import ColorsController from "./ColorsController";
import ColorEditController from "./ColorEditController";
import TagController from "./TagController";
import TagEditController from "./TagEditController";
import CategoriesController from "./CategoriesController";
import CategoryEditController from "./CategoryEditController";
import ProductEditController from "./ProductEditController";
import SizesController from "./SizesController";
import SizeEditController from "./SizeEditController";
import SliderEditController from "./SliderEditController";
import SliderController from "./SliderController";
import HomeBannersController from "./HomeBannersController";
import BannerEditController from "./BannerEditController";
import SettingsPageController from "./SettingsPageController";
import CouponsController from "./CouponsController";
import CouponsEditController from "./CouponsEditController";
import LocationsController from "./LocationsController";
import LocationEditController from "./LocationEditController";
import BlogController from "./BlogController";
import BlogEditController from "./BlogEditController";
import LoyaltyController from "./LoyaltyController";
import BannersController from "./BannersController";
import LoginPageController from "./LoginPageController";
import UsersController from "./UsersController";
import UserEditController from "./UserEditController";
import DescriptionController from "./DescriptionController";
import DescriptionEditController from "./DescriptionEditController";
import CollaboratorsController from "./CollaboratorsController";
import CareerController from "./CareerController";
import SliderTextController from "./SliderTextController";
import SliderTextEditController from "./SliderTextEditController";
import OrdersController from "./OrdersController";
import JobsController from "./JobsController";
import JobEditController from "./JobEditController";
import OrderSinglePageController from "./OrderSinglePageController";
import CatalogController from "./CatalogController";
import CatalogEditController from "./CatalogEditController";
import OfficeContactEditController from "./OfficeContactEditController";
import OfficeContactsController from "./OfficeContactsController";

let routes = [
    {
        name: 'admin.dashboard',
        controller: DashboardController,
    },
    {
        name: 'admin.colors',
        controller: ColorsController,
    },
    {
        name: 'admin.add_color_page',
        controller: ColorEditController,
    },
    {
        name: 'admin.edit_color_page',
        controller: ColorEditController,
    },
    {
        name: 'admin.product_tags',
        controller: TagController,
    },
    {
        name: 'admin.add_product_tag_page',
        controller: TagEditController,
    },
    {
        name: 'admin.edit_product_tag_page',
        controller: TagEditController,
    },
    {
        name: 'admin.categories',
        controller: CategoriesController,
    },
    {
        name: 'admin.add_category_page',
        controller: CategoryEditController,
    },
    {
        name: 'admin.edit_category_page',
        controller: CategoryEditController,
    },
    {
        name: 'admin.add_product_page',
        controller: ProductEditController,
    },
    {
        name: 'admin.edit_product_page',
        controller: ProductEditController,
    },
    {
        name: 'admin.sizes',
        controller: SizesController,
    },
    {
        name: 'admin.add_size_page',
        controller: SizeEditController,
    },
    {
        name: 'admin.edit_size_page',
        controller: SizeEditController,
    },
    {
        name: 'admin.add_slider_page',
        controller: SliderEditController,
    },
    {
        name: 'admin.sliders',
        controller: SliderController,
    },
    {
        name: 'admin.edit_slider_page',
        controller: SliderEditController,
    },
    {
        name: 'admin.home_banners',
        controller: HomeBannersController,
    },
    {
        name: 'admin.add_home_banner_page',
        controller: BannerEditController,
    },
    {
        name: 'admin.edit_home_banner_page',
        controller: BannerEditController,
    },
    {
        name: 'admin.settings_page',
        controller: SettingsPageController,
    },
    {
        name: 'admin.promotion_coupons',
        controller: CouponsController,
    },
    {
        name: 'admin.promotion_products',
        controller: CouponsController,
    },
    {
        name: 'admin.add_promotion_coupon_page',
        controller: CouponsEditController,
    },
    {
        name: 'admin.edit_promotion_coupon_page',
        controller: CouponsEditController,
    },
    {
        name: 'admin.add_promotion_product_page',
        controller: CouponsEditController,
    },
    {
        name: 'admin.edit_promotion_product_page',
        controller: CouponsEditController,
    },
    {
        name: 'admin.locations',
        controller: LocationsController,
    },
    {
        name: 'admin.add_location_page',
        controller: LocationEditController,
    },
    {
        name: 'admin.edit_location_page',
        controller: LocationEditController,
    },
    {
        name: 'admin.blog_tags',
        controller: TagController,
    },
    {
        name: 'admin.add_blog_tag_page',
        controller: TagEditController,
    },
    {
        name: 'admin.edit_blog_tag_page',
        controller: TagEditController,
    },
    {
        name: 'admin.add_blog_page',
        controller: BlogEditController,
    },
    {
        name: 'admin.blog',
        controller: BlogController,
    },
    {
        name: 'admin.edit_blog_page',
        controller: BlogEditController,
    },
    {
        name: 'admin.loyalty',
        controller: LoyaltyController,
    },
    {
        name: 'admin.banners',
        controller: BannersController,
    },
    {
        name: 'admin.add_banner_page',
        controller: BannerEditController,
    },
    {
        name: 'admin.edit_banner_page',
        controller: BannerEditController,
    },
    {
        name: 'admin.catalog_page',
        controller: CatalogController,
    },
    {
        name: 'admin.add_catalog_page',
        controller: CatalogEditController,
    },
    {
        name: 'admin.edit_catalog_page',
        controller: CatalogEditController,
    },
    {
        name: 'admin.login',
        controller: LoginPageController,
    },
    {
        name: 'admin.users',
        controller: UsersController,
    },
    {
        name: 'admin.add_user_page',
        controller: UserEditController,
    },
    {
        name: 'admin.edit_user_page',
        controller: UserEditController,
    },
    {
        name: 'admin.descriptions',
        controller: DescriptionController,
    },
    {
        name: 'admin.add_description_page',
        controller: DescriptionEditController,
    },
    {
        name: 'admin.edit_description_page',
        controller: DescriptionEditController,
    },
    {
        name: 'admin.collaborators',
        controller: CollaboratorsController,
    },
    {
        name: 'admin.career',
        controller: CareerController,
    },
    {
        name: 'admin.slider_text',
        controller: SliderTextController,
    },
    {
        name: 'admin.add_slider_text_page',
        controller: SliderTextEditController,
    },
    {
        name: 'admin.edit_slider_text_page',
        controller: SliderTextEditController,
    },
    {
        name: 'admin.orders',
        controller: OrdersController,
    },
    {
        name: 'admin.jobs',
        controller: JobsController,
    },
    {
        name: 'admin.add_job',
        controller: JobEditController,
    },
    {
        name: 'admin.edit_job',
        controller: JobEditController,
    },
    {
        name: 'admin.view_single_order',
        controller: OrderSinglePageController,
    },
    {
        name: 'admin.office_contacts',
        controller: OfficeContactsController,
    },
    {
        name: 'admin.add_office_contact_page',
        controller: OfficeContactEditController,
    },
    {
        name: 'admin.edit_office_contact_page',
        controller: OfficeContactEditController,
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
