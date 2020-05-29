import AppHelperService from "../../../js/Helper/AppHelperService";
import blogPageDom from "../Dom/BlogPageDom";

class BlogPageRouting {
    constructor() {
        this.dom = blogPageDom;

        this.url = AppHelperService.generateLocalizedUrl('site.blog_list_page');
        this.apiUrl = AppHelperService.generateLocalizedUrl('site_api.blog_list_page');

        if (IS_FIRST_PAGE) {
            this.url += '/1';
            this.apiUrl += '/1';
        }

    }

    generateUrl(tag){
        tag = '/' + tag;

        if (tag === '/all') {
            tag = '';
        }

        window.history.pushState({ path: this.url + tag }, '', this.url + tag);

        return this.apiUrl + tag;
    }
}

export default BlogPageRouting;