class BlogEditService {
    sendSummernoteFile(el, file) {
        let data = new FormData();
        data.set('tmp_image', file);

        return $.ajax({
            type: 'POST',
            url: Routing.generate('admin.blog_image_resize'),
            data: data,
            contentType: false,
            processData: false,
        });
    }

    removeSummernoteImage(filename) {
        return $.ajax({
            type: 'DELETE',
            url: Routing.generate('remove_blog_image', {filename}),
            dataType: 'json'
        })
    }
}

export default BlogEditService;