
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
	    update(id, data, route){
	    	return api.call('post', `/api/v2/contracts/${id}/update`, data);
	    },
	    retrieve(id){
			return api.call('get', `/api/v2/contracts/${id}`, {});
	    },
	    delete(id){
	    	return api.call('delete', `/api/v2/contracts/${id}/destroy`, {});	
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
	        return api.call('delete', `/api/v2/contracts/ocean_freight/${id}/delete/`);
	    }
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
	        return api.call('delete', `/api/v2/contracts/surcharges/${id}/delete/`);
	    }
	}

};