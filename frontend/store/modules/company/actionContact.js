export default {
    list(params, callback) {
        api.call('get', `contacts`, { params })
            .then(response => {
                callback(null, response.data);
            }).catch(error => {
                callback(error, error.response.data);
            });
    }
}