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
        },
        getRequestStatus(id) {
            return api.call('get', `/api/v2/contracts/${id}/request/status`, {});
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
        massiveChangeHarborOrig(data, route) {
            let contract_id = route.params.id;

            return api.call('post', `/api/v2/contracts/${contract_id}/ocean_freight/massiveHarborChange`, data);
        },
        massiveChangeHarborDest(data, route) {
            let contract_id = route.params.id;

            return api.call('post', `/api/v2/contracts/${contract_id}/ocean_freight/massiveHarborChangeDest`, data);
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

    inlandLocation: {
        list(params, callback, route) {

            let inland_id = route.params.id;

            api.call('get', `/api/v2/inland/${inland_id}/location`, { params })
                .then(response => {
                    callback(null, response.data);
                }).catch(error => {
                    callback(error, error.response.data);
                });
        },
        create(data, route) {
            let inland_id = route.params.id;
            return api.call('post', `/api/v2/inland/${inland_id}/location/store`, data);
        },
        update(id, data, route) {
            let inland_id = route.params.id;
            return api.call('post', `/api/v2/inland/${inland_id}/location/${id}/update`, data);
        },
        delete(id) {
            return api.call('delete', `/api/v2/inland/location/${id}/destroy/`, {});
        },
        duplicate(id, data) {
            return api.call('post', `/api/v2/inland/location/${id}/duplicate`, data);
        },
        deleteAll(ids) {
            return api.call('post', `/api/v2/inland/location/destroyAll`, { ids: ids });
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
    sale_terms: {
        list(params, callback, route) {

            api.call('get', `/api/v2/sale_terms`, { params })
                .then(response => {
                    callback(null, response.data);
                }).catch(error => {
                    callback(error, error.response.data);
                });
        },
        create(data, route) {
            return api.call('post', `/api/v2/sale_terms/store`, data);
        },
        update(id, data, route) {
            return api.call('post', `/api/v2/sale_terms/${id}/update`, data);
        },
        duplicate(id, data) {
            return api.call('post', `/api/v2/sale_terms/${id}/duplicate`, data);
        },
        delete(id) {
            return api.call('delete', `/api/v2/sale_terms/${id}/destroy`, {});
        },
        retrieve(id) {
            return api.call('get', `/api/v2/sale_terms/${id}`, {});
        },
        deleteAll(ids) {
            return api.call('post', `/api/v2/sale_terms/destroyAll`, { ids: ids });
        },
    },
    sale_charges: {
        list(params, callback, route) {

            let saleterm_id = route.params.id;

            api.call('get', `/api/v2/sale_terms/${saleterm_id}/charge`, { params })
                .then(response => {
                    callback(null, response.data);
                }).catch(error => {
                    callback(error, error.response.data);
                });
        },
        create(data, route) {
            let saleterm_id = route.params.id;
            return api.call('post', `/api/v2/sale_terms/${saleterm_id}/charge/store`, data);
        },
        update(id, data, route) {
            return api.call('post', `/api/v2/sale_terms/charge/${id}/update`, data);
        },
        delete(id) {
            return api.call('delete', `/api/v2/sale_terms/charge/${id}/destroy`, {});
        },
        duplicate(id, data) {
            return api.call('post', `/api/v2/sale_terms/charge/${id}/duplicate`, data);
        },
        deleteAll(ids) {
            return api.call('post', `/api/v2/sale_terms/charge/destroyAll`, { ids: ids });
        }
    },
    sale_codes: {
        list(params, callback, route) {

            api.call('get', `/api/v2/sale_codes`, { params })
                .then(response => {
                    callback(null, response.data);
                }).catch(error => {
                    callback(error, error.response.data);
                });
        },
        create(data, route) {
            return api.call('post', `/api/v2/sale_codes/store`, data);
        },
        update(id, data, route) {
            return api.call('post', `/api/v2/sale_codes/${id}/update`, data);
        },
        duplicate(id, data) {
            return api.call('post', `/api/v2/sale_codes/${id}/duplicate`, data);
        },
        delete(id) {
            return api.call('delete', `/api/v2/sale_codes/${id}/destroy`, {});
        },
        retrieve(id) {
            return api.call('get', `/api/v2/sale_codes/${id}`, {});
        },
    },
    quotes: {
        filterOptions() {
            return api.call('get', `/api/quote/filters`);
        },
        list(params, callback, route, page, q) {
            api.call('get', '/api/quote/list', {params, page, q})
            .then(response => {
                callback(null, response.data);
            }).catch(error => {
                callback(error, error.response.data);
            });
        },
        create(data, route) {
            return api.call('post', `/api/quote/store`, data);
        },
        update(id, data) {
            return api.call('post', `/api/quote/${id}/update`, data);
        },
        updateSearch(id, data) {
            return api.call('post', `/api/quote/${id}/updateSearch`, data);
        },
        retrieve(id) {
            return api.call('get', `/api/quotes/${id}`, {});
        },
        duplicate(id, data) {
            return api.call('post', `/api/quotes/${id}/duplicate`, data);
        },
        specialduplicate(data) {
            return api.call('post', `/api/quotes/specialduplicate`, data);
        },
        delete(id) {
            return api.call('delete', `/api/quote/${id}/destroy`, {});
        },
        deleteAll(ids) {
            return api.call('post', `/api/quotes/destroyAll`, { ids: ids });
        },
        setCostSheet(autorate_id, route) {
            let quote_id = route.params.id;
            return api.call('get', `/api/quote/${quote_id}/setCostSheet/${autorate_id}`, {});
        }
    },
    automaticrates: {
        list(params, callback, route) {

            let quote_id = route.params.id;

            api.call('get', `/api/quotes/${quote_id}/automatic_rate`, { params })
                .then(response => {
                    callback(null, response.data);
                }).catch(error => {
                    callback(error, error.response.data);
                });
        },
        create(data, route) {
            let quote_id = route.params.id;
            return api.call('post', `/api/quotes/${quote_id}/automatic_rate/store`, data);
        },
        update(id, data, route) {
            let quote_id = route.params.id;
            return api.call('post', `/api/quotes/${quote_id}/automatic_rate/${id}/update`, data)
        },
        updateTotals(autorate_id, data, route) {
            let quote_id = route.params.id;
            return api.call('post', `/api/quotes/${quote_id}/automatic_rate/${autorate_id}/totals/update`, data)
        },
        retrieve(id, route) {
            let quote_id = route.params.id;
            return api.call('get', `/api/quotes/${quote_id}/automatic_rate/${id}`, {})
        },
        retrieveTotals(id, route) {
            let quote_id = route.params.id;
            return api.call('get', `/api/quotes/${quote_id}/automatic_rate/totals/${id}`, {})
        },
        delete(id) {
            return api.call('delete', `/api/quotes/automatic_rate/${id}/destroy`, {});
        },
    },
    charges: {
        list(id, params, callback, route) {

            let quote_id = route.params.id;

            api.call('get', `/api/quotes/${quote_id}/automatic_rate/${id}/charges`, { params })
                .then(response => {
                    callback(null, response.data);
                }).catch(error => {
                    callback(error, error.response.data);
                });
        },
        create(autorate_id, data, route) {
            let quote_id = route.params.id;
            return api.call('post', `/api/quotes/${quote_id}/automatic_rate/${autorate_id}/store`, data);
        },
        retrieve(autorate_id) {
            return api.call('get', `/api/quotes/ocean_freight/${autorate_id}/charge`, {})
        },
        update(charge_id, data, route) {
            return api.call('post', `/api/quotes/ocean_freight/charge/${charge_id}/update`, data)
        },
        delete(id) {
            return api.call('delete', `/api/quotes/ocean_freight/charge/${id}/destroy/`, {});
        },
        deleteAll(ids) {
            return api.call('post', `/api/quotes/automatic_rate/charges/destroyAll`, { ids: ids });
        },
        updateStatusSelect(charge_id,data){
            return api.call('post', `/api/quotes/updateStatus/charge/${charge_id}`,data)
        }
    },
    chargeslcl: {
        list(id, params, callback, route) {

            let quote_id = route.params.id;

            api.call('get', `/api/quotes/${quote_id}/automatic_rate/${id}/chargeslcl`, { params })
                .then(response => {
                    callback(null, response.data);
                }).catch(error => {
                    callback(error, error.response.data);
                });
        },
        create(autorate_id, data, route) {
            let quote_id = route.params.id;
            return api.call('post', `/api/quotes/${quote_id}/automatic_rate/${autorate_id}/storelcl`, data);
        },
        retrieve(autorate_id) {
            return api.call('get', `/api/quotes/ocean_freight/${autorate_id}/chargelcl`, {})
        },
        update(charge_id, data, route) {
            return api.call('post', `/api/quotes/ocean_freight/chargelcl/${charge_id}/update`, data)
        },
        delete(id) {
            return api.call('delete', `/api/quotes/ocean_freight/chargelcl/${id}/destroy/`, {});
        },
        deleteAll(ids) {
            return api.call('post', `/api/quotes/automatic_rate/chargeslcl/destroyAll`, { ids: ids });
        }
    },
    automaticinlands: {
        list(combo, params, callback, route) {

            let quote_id = route.params.id;

            api.call('get', `/api/quotes/${quote_id}/port/${combo}/automatic_inlands`, { params })
                .then(response => {
                    callback(null, response.data);
                }).catch(error => {
                    callback(error, error.response.data);
                });
        },
        create(port_id, data, route) {
            let quote_id = route.params.id;
            return api.call('post', `/api/quotes/${quote_id}/port/${port_id}/automatic_inlands/store`, data);
        },
        update(autoinland_id, data, route) {
            let quote_id = route.params.id;
            return api.call('post', `/api/quotes/${quote_id}/automatic_inland/${autoinland_id}/update`, data)
        },
        updateTotals(combo, data, route) {
            let quote_id = route.params.id;
            return api.call('post', `/api/quotes/${quote_id}/automatic_inland/totals/${combo}/update`, data)
        },
        retrieveTotals(combo, route) {
            let quote_id = route.params.id;
            return api.call('get', `/api/quotes/${quote_id}/automatic_inland/totals/${combo}`, {})
        },
        retrieveAddresses(port_id, route) {
            let quote_id = route.params.id;
            return api.call('get', `/api/quotes/${quote_id}/automatic_inland/addresses/${port_id}`, {})
        },
        delete(id) {
            return api.call('delete', `/api/quotes/automatic_inland/${id}/destroy/`, {});
        },
        deleteAll(ids) {
            return api.call('post', `/api/quotes/automatic_inland/destroyAll`, { ids: ids });
        },
        search(port_id, data, route) {
            let quote_id = route.params.id;
            return api.call('post', `/api/quotes/${quote_id}/port/${port_id}/automatic_inlands/search`, data);
        },
        harbors(route) {
            let quote_id = route.params.id;
            return api.call('post', `/api/quotes/${quote_id}/automatic_inlands/harbors`, {});
        },
        deleteFull(combo, route) {
            let quote_id = route.params.id;
            return api.call('post', `/api/quotes/${quote_id}/automatic_inland/${combo}/delete_full`, {})
        },
        updatePdfOptions(port_id, data, route) {
            let quote_id = route.params.id;
            return api.call('post', `/api/quotes/${quote_id}/automatic_inland/${port_id}/update_pdf_options`, data)
        },
        getHarborAddresses(port_id) {
            return api.call('get', `/api/quotes/automatic_inlands/${port_id}/get_harbor_address`, {});
        },
    },
    automaticinlandslcl: {
        list(combo, params, callback, route) {

            let quote_id = route.params.id;

            api.call('get', `/api/quotes/${quote_id}/port/${combo}/automatic_inlands_lcl`, { params })
                .then(response => {
                    callback(null, response.data);
                }).catch(error => {
                    callback(error, error.response.data);
                });
        },
        create(port_id, data, route) {
            let quote_id = route.params.id;
            return api.call('post', `/api/quotes/${quote_id}/port/${port_id}/automatic_inlands_lcl/store`, data);
        },
        update(autoinland_id, data, route) {
            let quote_id = route.params.id;
            return api.call('post', `/api/quotes/${quote_id}/automatic_inland_lcl/${autoinland_id}/update`, data)
        },
        updateTotals(combo, data, route) {
            let quote_id = route.params.id;
            return api.call('post', `/api/quotes/${quote_id}/automatic_inland_lcl/totals/${combo}/update`, data)
        },
        retrieveTotals(combo, route) {
            let quote_id = route.params.id;
            return api.call('get', `/api/quotes/${quote_id}/automatic_inland_lcl/totals/${combo}`, {})
        },
        retrieveAddresses(port_id, route) {
            let quote_id = route.params.id;
            return api.call('get', `/api/quotes/${quote_id}/automatic_inland_lcl/addresses/${port_id}`, {})
        },
        delete(id) {
            return api.call('delete', `/api/quotes/automatic_inland_lcl/${id}/destroy/`, {});
        },
        deleteAll(ids) {
            return api.call('post', `/api/quotes/automatic_inland_lcl/destroyAll`, { ids: ids });
        },
        search(port_id, data, route) {
            let quote_id = route.params.id;
            return api.call('post', `/api/quotes/${quote_id}/port/${port_id}/automatic_inlands_lcl/search`, data);
        },
        harbors(route) {
            let quote_id = route.params.id;
            return api.call('post', `/api/quotes/${quote_id}/automatic_inlands_lcl/harbors`, {});
        },
        deleteFull(combo, route) {
            let quote_id = route.params.id;
            return api.call('post', `/api/quotes/${quote_id}/automatic_inland_lcl/${combo}/delete_full`, {})
        },
        updatePdfOptions(port_id, data, route) {
            let quote_id = route.params.id;
            return api.call('post', `/api/quotes/${quote_id}/automatic_inland_lcl/${port_id}/update_pdf_options`, data)
        },
        getHarborAddresses(port_id) {
            return api.call('get', `/api/quotes/automatic_inlands/${port_id}/get_harbor_address`, {});
        },
    },
    localcharges: {
        create(data) {
            return api.call('post', `/api/quote/localcharge/store`, data);
        },
        createCharge(data) {
            return api.call('post', `/api/quote/charge/store`, data);
        },
        remarks(quote_id) {
            return api.call('get', `/api/quote/localcharge/remarks/${quote_id}`, {});
        },
        carriers(quote) {
            return api.call('get', `/api/quote/localcharge/carriers/${quote}`, {});
        },
        update(id, data, index, type) {
            return api.call('post', `/api/quote/localcharge/updates/${id}`, { data: data, index: index, type: type });
        },
        updateRemarks(data, quote_id) {
            return api.call('post', `/api/quote/localcharge/updates/${quote_id}/remarks`, { data: data });
        },
        delete(id, type) {
            return api.call('post', `/api/quote/localcharge/delete/${id}`, { type: type });
        },
        retrieve(data) {
            return api.call('get', '/api/quote/localcharge/saleterm', { data });
        },
        localcharges(params) {
            return api.call('get', '/api/quote/localcharge', { params })
        },
        total(params) {
            return api.call('get', '/api/quote/localcharge/total', { params })
        },
        storedCharges(params) {
            return api.call('get', '/api/quote/get/localcharge', { params })
        },
        charges(params) {
            return api.call('post', '/api/quote/localcharge/store/salecharge', { params })
        },
        saleterms(params) {
            return api.call('get', '/api/quote/localcharge/saleterm', { params })
        },
        harbors(id) {
            return api.call('get', `/api/quote/local/data/${id}`, {})
        },
        deleteAll(ids) {
            return api.call('post', `/api/quote/localcharge/destroyAll`, { ids: ids });
        },
    },
    localchargeslcl: {
        create(data) {
            return api.call('post', `/api/quote/localcharge/lcl/store`, data);
        },
        createCharge(data) {
            return api.call('post', `/api/quote/charge/lcl/store`, data);
        },
        localcharges(params) {
            return api.call('get', '/api/quote/localcharge/lcl', { params })
        },
        delete(id, type) {
            return api.call('post', `/api/quote/localcharge/lcl/delete/${id}`, { type: type });
        },
        total(params) {
            return api.call('get', '/api/quote/localcharge/lcl/total', { params })
        },
        deleteAll(ids) {
            return api.call('post', `/api/quote/localcharge/lcl/destroyAll`, { ids: ids });
        },
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
        update(id, data, route) {
            return api.call('post', `/api/v2/providers/${id}/update`, data);
        },
        retrieve(id) {
            return api.call('get', `/api/v2/providers/${id}`, {});
        },
        duplicate(id, data) {
            return api.call('post', `/api/v2/providers/${id}/duplicate`, data);
        },
        delete(id) {
            return api.call('delete', `/api/v2/providers/${id}/destroy`, {});
        },
        deleteAll(ids) {
            return api.call('post', `/api/v2/providers/destroyAll`, { ids: ids });
        },
    },
    excel: {
        create(data, route) {
            return api.call('post', `/contracts/export`, data);
        }
    },
    search: {
        list(data) {
            return api.call('get', `/api/search/list`, data);
        },
        process(id) {
            return api.call('post', `/api/search/process`, id);
        },
        create(data) {
            return api.call('post', `/api/search/store`, data);
        },
        retrieve(id) {
            return api.call('get', `/api/search/${id}`, {});
        },
        createContract(data) {
            return api.call('post', `/api/search/storeContract`, data);
        },
        downloadContract(data) {
            return api.call('post', `/api/search/downloadContract`, data);
        },
    },
    searchlcl: {
        list(data) {
            return api.call('get', `/api/search_lcl/list`, data);
        },
        process(id) {
            return api.call('post', `/api/search_lcl/process`, id);
        },
        create(data) {
            return api.call('post', `/api/search_lcl/store`, data);
        },
        downloadContract(data) {
            return api.call('post', `/api/search_lcl/downloadContract`, data);
        },
    },
    contracts_lcl: {
        list(params, callback, route) {

            api.call('get', '/api/v2/contractslcl', { params })
                .then(response => {
                    callback(null, response.data);
                }).catch(error => {
                    callback(error, error.response.data);
                });
        },
        create(data, route) {
            return api.call('post', `/api/v2/contractslcl/store`, data);
        },
        update(id, data, route) {
            return api.call('post', `/api/v2/contractslcl/${id}/update`, data);
        },
        retrieve(id) {
            return api.call('get', `/api/v2/contractslcl/${id}`, {});
        },
        duplicate(id, data) {
            return api.call('post', `/api/v2/contractslcl/${id}/duplicate`, data);
        },
        delete(id) {
            return api.call('delete', `/api/v2/contractslcl/${id}/destroy`, {});
        },
        deleteAll(ids) {
            return api.call('post', `/api/v2/contractslcl/destroyAll`, { ids: ids });
        },
        getfiles(id) {
            return api.call('get', `/api/v2/contractslcl/${id}/files`, {});
        },
        removefile(id, data) {
            return api.call('post', `/api/v2/contractslcl/${id}/removefile`, data);
        },
        getRequestStatus(id) {
            return api.call('get', `/api/v2/contractslcl/${id}/request/status`, {});
        }
    },
    oceanfreightslcl: {
        list(params, callback, route) {

            let contract_id = route.params.id;

            api.call('get', `/api/v2/contractslcl/${contract_id}/ocean_freight`, { params })
                .then(response => {
                    callback(null, response.data);
                }).catch(error => {
                    callback(error, error.response.data);
                });
        },
        create(data, route) {
            let contract_id = route.params.id;
            return api.call('post', `/api/v2/contractslcl/${contract_id}/ocean_freight/store`, data);
        },
        update(id, data, route) {
            let contract_id = route.params.id;
            return api.call('post', `/api/v2/contractslcl/${contract_id}/ocean_freight/${id}/update`, data);
        },
        delete(id) {
            return api.call('delete', `/api/v2/contractslcl/ocean_freight/${id}/destroy/`, {});
        },
        duplicate(id, data) {
            return api.call('post', `/api/v2/contractslcl/ocean_freight/${id}/duplicate`, data);
        },
        deleteAll(ids) {
            return api.call('post', `/api/v2/contractslcl/ocean_freight/destroyAll`, { ids: ids });
        },
        massiveChange(data, route) {
            let contract_id = route.params.id;
            return api.call('post', `/api/v2/contractslcl/${contract_id}/ocean_freight/massiveContainerChange`, data);
        },
        massiveChangeHarborOrig(data, route) {
            let contract_id = route.params.id;

            return api.call('post', `/api/v2/contractslcl/${contract_id}/ocean_freight/massiveHarborChange`, data);
        },
        massiveChangeHarborDest(data, route) {
            let contract_id = route.params.id;

            return api.call('post', `/api/v2/contractslcl/${contract_id}/ocean_freight/massiveHarborChangeDest`, data);
        },
    },
    surchargeslcl: {
        list(params, callback, route) {

            let contract_id = route.params.id;

            api.call('get', `/api/v2/contractslcl/${contract_id}/localcharges`, { params })
                .then(response => {
                    callback(null, response.data);
                }).catch(error => {
                    callback(error, error.response.data);
                });
        },
        create(data, route) {
            let contract_id = route.params.id;

            return api.call('post', `/api/v2/contractslcl/${contract_id}/localcharge/store`, data);
        },
        update(id, data, route) {
            let contract_id = route.params.id;
            return api.call('post', `/api/v2/contractslcl/${contract_id}/localcharge/${id}/update`, data);
        },
        delete(id) {
            return api.call('delete', `/api/v2/contractslcl/localcharge/${id}/destroy`, {});
        },
        duplicate(id, data) {
            return api.call('post', `/api/v2/contractslcl/localcharge/${id}/duplicate`, data);
        },
        deleteAll(ids) {
            return api.call('post', `/api/v2/contractslcl/localcharge/destroyAll`, { ids: ids });
        }
    },
    remarks_lcl: {
        create(data, route) {
            let contract_id = route.params.id;
            return api.call('post', `/api/v2/contractslcl/${contract_id}/remarks`, data);
        },
    },
};