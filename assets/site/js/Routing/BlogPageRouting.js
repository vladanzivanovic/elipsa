import blogPageDom from "../Dom/BlogPageDom";

class BlogPageRouting {
    constructor() {
        this.dom = blogPageDom;

        this.url = Routing.generate(`site.blog_list_page.${LOCALE}`);
        this.apiUrl = Routing.generate(`site_api.blog_list_page.${LOCALE}`);

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
