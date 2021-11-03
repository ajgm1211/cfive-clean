import axios from "axios";
import router from "../../main";

const state = {
    companyUsers: [],
    paginateCompanyUsers: {
        "current_page": 1,
        "data": [],
        "first_page_url": "http://cargofive.test.com/api/apiCredentials/companyUsers?page=1",
        "from": 1,
        "last_page": 1,
        "last_page_url": "http://cargofive.test.com/api/apiCredentials/companyUsers?page=1",
        "next_page_url": "http://cargofive.test.com/api/apiCredentials/companyUsers?page=1",
        "path": "http://cargofive.test.com/api/apiCredentials/companyUsers",
        "per_page": 5,
        "prev_page_url": null,
        "to": 1,
        "total": 0
    },
    companyUser: {},
};

const actions = {
    getCompanyUsers({ commit }, {page}) {
        axios.get(`/api/apiCredentials/companyUsers?page=${page}`).then(response => {
            const json = response.data;
            commit("SET_PAGINATE_COMPANY_USERS", json);
        });
    },
    getApiProviders({ commit }, {id}) {
        axios.get(`/api/apiCredentials/${id}`).then(response => {
            const json = response.data;
            commit("SET_COMPANY_USER", json);
        });
    }
};

const mutations = {
    SET_PAGINATE_COMPANY_USERS(state, value) {
        state.paginateCompanyUsers = value;
        state.companyUsers = value.data;
    },
    SET_COMPANY_USER(state, value) {
        state.companyUser = value;
    },
};

const getters = {
    GET_COMPANY_USERS() {
        return state.companyUsers;
    },
    GET_PAGINATE_COMPANY_USERS() {
        return state.paginateCompanyUsers;
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
