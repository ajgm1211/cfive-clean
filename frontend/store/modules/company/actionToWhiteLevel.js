export default {
    addToWL(companies){
        return api.call('post', `/api/companies/toWhiteLevel`, {companies});
    }
}