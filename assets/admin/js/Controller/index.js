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
        name: 'admin.tags',
        controller: TagController,
    },
    {
        name: 'admin.add_tag_page',
        controller: TagEditController,
    },
    {
        name: 'admin.edit_tag_page',
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