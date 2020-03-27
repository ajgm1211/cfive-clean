import axios from 'axios';

class Api {

	constructor () {

	}

	call ( requestType, url, data = null ) {
		
		return new Promise(( resolve, reject ) => {
			axios[requestType]( url, data )
				.then( response => {
					console.log(response);
					resolve(response);
				})
				.catch( ( { response } ) => { 
					console.log(response);

					reject(response);
					if (response.status === 403) {
						//Event.$swal("Oops!!", response.data.error, "error"); 
					} else {
						//Event.$swal("Oops!!", response.data.message, "error"); 
					}

					if (response.status === 401) {
						auth.logout();
					}

				});
		});
	}

	getData(params, url, callback) {

	    this.call('get', url, { params })
	        .then(response => {
	            callback(null, response.data);
	        }).catch(error => {
	            callback(error, error.response.data);
	        });
	}
}

export default Api;