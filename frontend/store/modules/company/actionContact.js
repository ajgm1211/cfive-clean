export default {
    list(params, callback) {
        api.call('get', `contacts`, { params })
            .then(response => {
                callback(null, response.data);
            }).catch(error => {
                callback(error, error.response.data);
            });
    },
    duplicate(id, data) {
        return api.call('post', `/api/contacts/${id}/duplicate`, data);
    },
    delete(id) {
        return api.call('put', `/api/contacts/${id}/delete`, {});
    },
    deleteAll(ids) {
        return api.call('put', `/api/contacts/deleteAll`, { ids: ids });
    }
}