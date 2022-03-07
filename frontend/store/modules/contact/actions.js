export default {
    list(params, callback) {

        api.call('get', `/api/contacts/list`, { params })
            .then(response => {
                callback(null, response.data);
            }).catch(error => {
                callback(error, error.response.data);
            });
    },
    retrieve(id) {
        return api.call('get', `/api/contacts/retrieve/${id}`);
    },
    duplicate(id, data) {
        return api.call('post', `/api/contacts/${id}/duplicate`, data);
    },
    delete(id) {
        return api.call('put', `/api/contacts/${id}/delete`, {});
    },
    deleteAll(ids) {
        return api.call('put', `/api/contacts/deleteAll`, { ids: ids });
    },
    contacts(){
        return api.call('get', `/api/contacts/contacts`);
    }
}