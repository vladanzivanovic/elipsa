class UserService {
    isUserExistsByEmail(email) {
        let deferred = $.Deferred();

        $.ajax({
            type: 'GET',
            url: Routing.generate('site_api.user_exists', {email}),
            dataType: 'json',
            success: response => {
                deferred.resolve();
            },
            error: error => {
                deferred.reject(error);
            }
        })

        return deferred.promise();
    }
}

export default UserService;