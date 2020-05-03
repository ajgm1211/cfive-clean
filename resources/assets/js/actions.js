
export default {
	oceanfreights: {
		list(contract_id, params, callback) {
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
	    delete(token) {
	        return api.call('get', `password/find/${token}`);
	    }
	}

};