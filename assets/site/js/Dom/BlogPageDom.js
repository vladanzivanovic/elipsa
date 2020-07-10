import ShopPageMapper from "../Mapper/ShopPageMapper";
import AppHelperService from "../../../js/Helper/AppHelperService";

class BlogPageDom {
    constructor() {
        if (!BlogPageDom.instance) {
            this.mapper = new ShopPageMapper();

            BlogPageDom.instance = this;
        }

        return BlogPageDom.instance;
    }

    generateBlog(data) {
        let html = '';

        for(let i in data.blog_list) {
            let blog = data.blog_list[i];

            html += `<div class="single-blog">
                        <div class="blog-img">
                            <a href="${AppHelperService.generateLocalizedUrl('site.blog_detailed_page', {slug: blog.alias})}"><img src="${ blog.image_link_list }" alt="${ blog.title }"></a>
                            <div class="blog-img-content">
                                <span class="bc-date">${ blog.day }</span>
                                <span class="bc-month">${ blog.month }</span>
                            </div>
                        </div>
                        <div class="blog-content">
                            <a class="blog-title" href="${AppHelperService.generateLocalizedUrl('site.blog_detailed_page', {slug: blog.alias})}">${ blog.title }</a>
                            <p class="blog-text">${ blog.shortDescription }</p>
                            <a class="blog-read-more letter-capitalize" href="${AppHelperService.generateLocalizedUrl('site.blog_detailed_page', {slug: blog.alias})}">${Translator.trans('read_more', null, 'messages', LOCALE)}</a>
                        </div>
                    </div>`;
        }

        return html;
    };

    listData(data) {
        let html = '';

        for(let i in data) {
            html += `<li><a href="#">${data[i]}</a></li>`;
        }

        return html;
    }
}

const blogPageDom = new BlogPageDom();
Object.freeze(blogPageDom);

export default blogPageDom;