export default {
    list(params, callback) {

        api.call('get', `/api/contacts/failed/list`, { params })
            .then(response => {
                callback(null, response.data);
            }).catch(error => {
                callback(error, error.response.data);
            });
    }
}