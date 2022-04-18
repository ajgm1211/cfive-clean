export default {
    addToWhiteLabel(companies){
        return api.call('post', `/api/companies/toWhiteLabel`, {companies});
    }
}