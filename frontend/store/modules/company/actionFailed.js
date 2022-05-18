export default {
    list(params, callback) {

        api.call('get', `/api/companies/failed/list`, { params })
            .then(response => {
                callback(null, response.data);
            }).catch(error => {
                callback(error, error.response.data);
            });
    },
    update(id, company) {
        return api.call('post', `/api/companies/failed/${id}/update`, {company, id});
    },
}