import axios from 'axios';
export default {
    list(params, callback) {

        api.call('get', `/api/companies/list`, { params })
            .then(response => {
                callback(null, response.data);
            }).catch(error => {
                callback(error, error.response.data);
            });
    },
    retrieve(id) {
        return api.call('get', `/api/companies/retrieve/${id}`);
    },
    duplicate(id, data) {
        return api.call('post', `/api/companies/${id}/duplicate`, data);
    },
    delete(id) {
        return api.call('put', `/api/companies/${id}/delete`, {});
    },
    deleteAll(ids) {
        return api.call('put', `/api/companies/deleteAll`, { ids: ids });
    },
    update(id, company) {
        return api.call('post', `/api/companies/${id}/update`, {company});
    },
    create(company){
        return api.call('post', `/api/companies/store`, {company});
    },
    downloadTemplate(){
        axios({
            url: '/api/companies/template', // File URL Goes Here
            method: 'GET',
            responseType: 'blob',
        });
    }
    
}