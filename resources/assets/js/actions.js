/* Endpoints API */

export default {
    contracts: {
        list(params, callback, route) {

            api.call('get', '/api/v2/contracts', { params })
                .then(response => {
                    callback(null, response.data);
                }).catch(error => {
                    callback(error, error.response.data);
                });
        },
        create(data, route) {
            return api.call('post', `/api/v2/contracts/store`, data);
        },
        update(id, data, route) {
            return api.call('post', `/api/v2/contracts/${id}/update`, data);
        },
        retrieve(id) {
            return api.call('get', `/api/v2/contracts/${id}`, {});
        },
        duplicate(id, data) {
            return api.call('post', `/api/v2/contracts/${id}/duplicate`, data);
        },
        delete(id) {
            return api.call('delete', `/api/v2/contracts/${id}/destroy`, {});
        },
        deleteAll(ids) {
            return api.call('post', `/api/v2/contracts/destroyAll`, { ids: ids });
        },
        getfiles(id) {
            return api.call('get', `/api/v2/contracts/${id}/files`, {});
        },
        removefile(id, data) {
            return api.call('post', `/api/v2/contracts/${id}/removefile`, data);
        }
    },
    excel: {
       
        create(data, route) {
            return api.call('post', `/contracts/export`, data);
        }
       
    },
    oceanfreights: {
        list(params, callback, route) {

            let contract_id = route.params.id;

            api.call('get', `/api/v2/contracts/${contract_id}/ocean_freight`, { params })
                .then(response => {
                    callback(null, response.data);
                }).catch(error => {
                    callback(error, error.response.data);
                });
        },
        create(data, route) {
            let contract_id = route.params.id;
            return api.call('post', `/api/v2/contracts/${contract_id}/ocean_freight/store`, data);
        },
        update(id, data, route) {
            let contract_id = route.params.id;
            return api.call('post', `/api/v2/contracts/${contract_id}/ocean_freight/${id}/update`, data);
        },
        delete(id) {
            return api.call('delete', `/api/v2/contracts/ocean_freight/${id}/destroy/`, {});
        },
        duplicate(id, data) {
            return api.call('post', `/api/v2/contracts/ocean_freight/${id}/duplicate`, data);
        },
        deleteAll(ids) {
            return api.call('post', `/api/v2/contracts/ocean_freight/destroyAll`, { ids: ids });
        },
        massiveChange(data, route) {
            let contract_id = route.params.id;
            return api.call('post', `/api/v2/contracts/${contract_id}/ocean_freight/massiveContainerChange`, data);
        },
    },
    surcharges: {
        list(params, callback, route) {

            let contract_id = route.params.id;

            api.call('get', `/api/v2/contracts/${contract_id}/localcharges`, { params })
                .then(response => {
                    callback(null, response.data);
                }).catch(error => {
                    callback(error, error.response.data);
                });
        },
        create(data, route) {
            let contract_id = route.params.id;

            return api.call('post', `/api/v2/contracts/${contract_id}/localcharge/store`, data);
        },
        update(id, data, route) {
            let contract_id = route.params.id;
            return api.call('post', `/api/v2/contracts/${contract_id}/localcharge/${id}/update`, data);
        },
        delete(id) {
            return api.call('delete', `/api/v2/contracts/localcharge/${id}/destroy`, {});
        },
        duplicate(id, data) {
            return api.call('post', `/api/v2/contracts/localcharge/${id}/duplicate`, data);
        },
        deleteAll(ids) {
            return api.call('post', `/api/v2/contracts/localcharge/destroyAll`, { ids: ids });
        }
    },
    restrictions: {
        create(data, route) {
            let contract_id = route.params.id;
            return api.call('post', `/api/v2/contracts/${contract_id}/restrictions`, data);
        },
    },
    remarks: {
        create(data, route) {
            let contract_id = route.params.id;
            return api.call('post', `/api/v2/contracts/${contract_id}/remarks`, data);
        },
    },
    inlands: {
        list(params, callback, route) {

            api.call('get', '/api/v2/inland', { params })
                .then(response => {
                    callback(null, response.data);
                }).catch(error => {
                    callback(error, error.response.data);
                });
        },
        create(data, route) {
            return api.call('post', `/api/v2/inland/store`, data);
        },
        update(id, data, route) {
            return api.call('post', `/api/v2/inland/${id}/update`, data);
        },
        retrieve(id) {
            return api.call('get', `/api/v2/inland/${id}`, {});
        },
        duplicate(id, data) {
            return api.call('post', `/api/v2/inland/${id}/duplicate`, data);
        },
        delete(id) {
            return api.call('delete', `/api/v2/inland/${id}/destroy`, {});
        },
        deleteAll(ids) {
            return api.call('post', `/api/v2/inland/destroyAll`, { ids: ids });
        },
    },
    ranges: {
        list(params, callback, route) {

            let inland_id = route.params.id;

            api.call('get', `/api/v2/inland/${inland_id}/range`, { params })
                .then(response => {
                    callback(null, response.data);
                }).catch(error => {
                    callback(error, error.response.data);
                });
        },
        create(data, route) {
            let inland_id = route.params.id;
            return api.call('post', `/api/v2/inland/${inland_id}/range/store`, data);
        },
        update(id, data, route) {
            let inland_id = route.params.id;
            return api.call('post', `/api/v2/inland/${inland_id}/range/${id}/update`, data);
        },
        delete(id) {
            return api.call('delete', `/api/v2/inland/range/${id}/destroy/`, {});
        },
        duplicate(id, data) {
            return api.call('post', `/api/v2/inland/range/${id}/duplicate`, data);
        },
        deleteAll(ids) {
            return api.call('post', `/api/v2/inland/range/destroyAll`, { ids: ids });
        }
    },
    kms: {
        list(params, callback, route) {

            let inland_id = route.params.id;

            api.call('get', `/api/v2/inland/${inland_id}/km`, { params })
                .then(response => {
                    callback(null, response.data);
                }).catch(error => {
                    callback(error, error.response.data);
                });
        },
        update(id, data, route) {
            let inland_id = route.params.id;
            return api.call('post', `/api/v2/inland/${inland_id}/km/${id}/update`, data);
        },
        retrieve(route) {
            let inland_id = route.params.id;
            return api.call('get', `/api/v2/inland/${inland_id}/km/retrieve`, {});
        }
    },
    transit_time: {
        list(params, callback, route) {

            api.call('get', `/api/v2/transit_time`, { params })
                .then(response => {
                    callback(null, response.data);
                }).catch(error => {
                    callback(error, error.response.data);
                });
        },
        create(data, route) {
            let contract_id = route.params.id;
            return api.call('post', `/api/v2/transit_time/store`, data);
        },
        update(id, data, route) {
            let contract_id = route.params.id;
            return api.call('post', `/api/v2/transit_time/${id}/update`, data);
        },
        delete(id) {
            return api.call('delete', `/api/v2/transit_time/${id}/destroy/`, {});
        },
        deleteAll(ids) {
            return api.call('post', `/api/v2/transit_time/destroyAll`, { ids: ids });
        }
    },

	providers: {
		list(params, callback, route) {

        	api.call('get', '/api/v2/providers', { params })
        		.then(response => {
	            	callback(null, response.data);
	        	}).catch(error => {
	            	callback(error, error.response.data);
	        	});
    	},
    	create(data, route) {
	        return api.call('post', `/api/v2/providers/store`, data);
	    },
		update(id, data, route){
	    	return api.call('post', `/api/v2/providers/${id}/update`, data);
	    },
	    retrieve(id){
			return api.call('get', `/api/v2/providers/${id}`, {});
	    },
	    duplicate(id, data){
			return api.call('post', `/api/v2/providers/${id}/duplicate`, data);
	    },
	    delete(id){
	    	return api.call('delete', `/api/v2/providers/${id}/destroy`, {});	
	    },
	    deleteAll(ids){
	    	return api.call('post', `/api/v2/providers/destroyAll`, { ids:ids });	
	    },
	},

};