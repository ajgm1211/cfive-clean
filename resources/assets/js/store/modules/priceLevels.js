import axios from "axios";
import router from "../../main";


const state = {
  priceLevels: "",
  paginatePriceLevels: "",
};

const actions = {
  getPriceLevels({ commit }, {page}) {
    return axios.get(`/api/pricelevels/list?page=${page}`).then((response) => {
      commit("SET_PRICE_LEVELS", response.data.data);
      commit("SET_PAGINATE_PRICE_LEVELS", response.data.meta);
    });
  },
  
  createPriceLevel({ commit }, { body }) {
    return axios.post(`/api/pricelevels/store`, body).then((response) => {
      router.push({
        name: "price-rates",
        params: { id: response.data.data.id },
      });
    });
  },

  updatePriceLevel({ commit }, { id, body }) {
    return axios.post(`api/pricelevels/${id}/update`, body).then((response) => {
    });
  },

  duplicatePriceLevel({ dispatch }, { id, page }) {
    return axios.post(`/api/pricelevels/${id}/duplicate`).then((response) => {
      dispatch("getPriceLevels", {page: page});
    });
  },

  deletePriceLevel({ dispatch }, { id, page }) {
    return axios.put(`/api/pricelevels/${id}/delete`).then((response) => {
      dispatch("getPriceLevels", {page: page});
    });
  },

  deleteSelectedPriceLevel({ dispatch }, { body, page }) {
    return axios.put(`/api/pricelevels/deleteAll`, body).then((response) => {
      dispatch("getPriceLevels", {page: page});
    });
  },
};

const mutations = {
  SET_PRICE_LEVELS(state, value) {
    state.priceLevels = value;
  },
  SET_PAGINATE_PRICE_LEVELS(state, value) {
    state.paginatePriceLevels = value;
  },
};

const getters = {
  GET_PRICE_LEVELS() {
    return state.priceLevels;
  },
  GET_PAGINATE_PRICE_LEVELS() {
    return state.paginatePriceLevels;
  },
};

export default {
  state,
  mutations,
  actions,
  getters,
};
