export default {
    list(params, callback) {

        api.call('get', `/api/contacts/failed/list`, { params })
            .then(response => {
                callback(null, response.data);
            }).catch(error => {
                callback(error, error.response.data);
            });
    },
    update(id, contact) {
        return api.call('post', `/api/contacts/failed/${id}/update`, {contact, id});
    },
}