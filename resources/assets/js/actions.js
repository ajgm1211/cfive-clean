
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
	    duplicate(id, data){
			return api.call('post', `/api/v2/contracts/${id}/duplicate`, data);
	    },
	    delete(id){
	    	return api.call('delete', `/api/v2/contracts/${id}/destroy`, {});	
	    },
	    deleteAll(ids){
	    	return api.call('post', `/api/v2/contracts/destroyAll`, { ids:ids });	
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
	   	duplicate(id, data){
			return api.call('post', `/api/v2/contracts/ocean_freight/${id}/duplicate`, data);
	    },
	    deleteAll(ids){
	    	return api.call('post', `/api/v2/contracts/ocean_freight/destroyAll`, { ids:ids });	
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
	        return api.call('delete', `/api/v2/contracts/localcharge/${id}/destroy`, {});
	    },
	   	duplicate(id, data){
			return api.call('post', `/api/v2/contracts/localcharge/${id}/duplicate`, data);
	    },
	    deleteAll(ids){
	    	return api.call('post', `/api/v2/contracts/localcharge/destroyAll`, { ids:ids });	
	    }
	},
	restrictions: {
	   	create(data, route) {
	    	let contract_id = route.params.id;
	        return api.call('post', `/api/v2/contracts/${contract_id}/restrictions`, data);
	    },
	}

};