export default {
    list(params, callback) {

        api.call('get', `/api/contacts/list`, { params })
            .then(response => {
                callback(null, response.data);
            }).catch(error => {
                callback(error, error.data);
            });
    },
    retrieve(id) {
        return api.call('get', `/api/contacts/retrieve/${id}`);
    },
    update(id, contact) {
        return api.call('post', `/api/contacts/${id}/update`, {contact});
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
    create(contact, toWhiteLabel){
        return api.call('post', `/api/contacts/store`, {contact, toWhiteLabel});
    },
    createMassive(contacts) {
        return api.call('post', `/api/contacts/create-massive`, contacts);
    },
    transfer(contacts){
        return api.call('post', `/api/contacts/toWhiteLabel`, {contacts});
    },
    getCompanies(){
        return api.call('get', `/api/contacts/getCompanies`);
    }
}