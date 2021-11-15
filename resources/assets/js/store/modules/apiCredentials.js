import axios from "axios";
import router from "../../main";

const state = {
    companyUsers: [],
    apiProviders: [],
    paginateCompanyUsers: {},
    companyUser: {},
};

const actions = {
    getCompanyUsers({ commit }, {page}) {
        axios.get(`/api/apiCredentials/companyUsers?page=${page}`).then(response => {
            const json = response.data;
            commit("SET_PAGINATE_COMPANY_USERS", json);
        });
    },
    getApiProvidersByCompanyUser({ commit }, {id}) {
        axios.get(`/api/apiCredentials/companyUser/${id}`).then(response => {
            const json = response.data;
            commit("SET_COMPANY_USER", json);
        });
    },
    getAvailableApiProviders({ commit }, { body }) {
        axios.post(`/api/apiCredentials/apiProviders`, body).then(response => {
            const json = response.data;
            commit("SET_AVAILABLE_API_PROVIDERS", json);
        });
    },
    createApiCredentials({ commit }, { body }) {
        return axios.post(`/api/apiCredentials/store`, body);
    },

    updateApiCredentials({ commit }, { id, body }) {
        return axios.post(`/api/apiCredentials/update/${id}`, body);
    },
    updateApiCredentialsStatus({ commit }, { id, body }) {
        return axios.post(`/api/apiCredentials/status/${id}`, body);
    },
    deleteApiProviderOfCompanyUser({ commit }, { id, body }) {
        return axios.post(`/api/apiCredentials/companyUser/${id}/deleteApiProvider`, body);
    }    
};

const mutations = {
    SET_PAGINATE_COMPANY_USERS(state, value) {
        state.paginateCompanyUsers = value;
        state.companyUsers = value.data;
    },
    SET_AVAILABLE_API_PROVIDERS(state, value) {
        state.apiProviders = value;
    },
    SET_COMPANY_USER(state, value) {
        state.companyUser = value;
    }
};

const getters = {
    GET_COMPANY_USERS() {
        return state.companyUsers;
    },
    GET_PAGINATE_COMPANY_USERS() {
        return state.paginateCompanyUsers;
    },
    GET_AVAILABLE_API_PROVIDERS() {
        return state.apiProviders;
    },
    GET_COMPANY_USER() {
        return state.companyUser;
    },
};

export default {
    state,
    mutations,
    actions,
    getters,
};
