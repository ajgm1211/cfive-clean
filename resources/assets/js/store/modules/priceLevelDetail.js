import axios from "axios";
import router from "../../main";

const state = {
  currentPriceLevel: "",
  priceLevelRates: "",
  paginateRates: "",
  priceLevelData: "",
};

const actions = {
  getPriceLevelDetail({ commit }, { id }) {
    axios.get(`/api/pricelevels/retrieve/${id}`).then((response) => {
      commit("SET_CURRENT_PRICE_LEVEL", response.data);
    });
  },

  listPriceLevelRates({ commit }, { id, page }) {
    axios.get(`api/pricelevels/details/${id}/list?page=${page}`).then((response) => {
      commit("SET_PRICE_LEVEL_RATES", response.data.data);
      commit("SET_PAGINATE_RATES", response.data);
    });
  },

  getPriceLevelData({ commit }) {
    axios.get(`api/pricelevels/data`).then((response) => {
      commit("SET_PRICE_LEVEL_DATA", response.data.data);
    });
  },

  createRate({ dispatch }, { id, body, page, currentId }) {
    axios.post(`api/pricelevels/details/${id}/store`, body).then((response) => {
      dispatch("listPriceLevelRates", {id: currentId, page:page});
    });
  },

  duplicateRate({ dispatch }, { id, page, currentId }) {
    axios.post(`/api/pricelevels/details/${id}/duplicate`).then((response) => {
      dispatch("listPriceLevelRates", {id: currentId, page:page});
    });
  },

  deleteRate({ dispatch }, { id, page, currentId }) {
    axios.put(`/api/pricelevels/details/${id}/destroy`).then((response) => {
      dispatch("listPriceLevelRates", {id: currentId, page:page});
    });
  },

  deleteMultiple({ dispatch }, { body, id, page }) {
    axios.put(`/api/pricelevels/details/destroyAll`, body).then((response) => {
      dispatch("listPriceLevelRates", {id: id, page:page});
    });
  },
};

const mutations = {
  SET_CURRENT_PRICE_LEVEL(state, value) {
    state.currentPriceLevel = value;
  },

  SET_PRICE_LEVEL_RATES(state, value) {
    state.priceLevelRates = value;
  },

  SET_PRICE_LEVEL_DATA(state, value) {
    state.priceLevelData = value;
  },

  SET_PAGINATE_RATES(state, value) {
    state.paginateRates = value;
  },
};

const getters = {
  GET_CURRENT_PRICE_LEVEL() {
    return state.currentPriceLevel;
  },

  GET_PRICE_LEVEL_RATES() {
    return state.priceLevelRates;
  },

  GET_PRICE_LEVEL_DATA() {
    return state.priceLevelData;
  },

  GET_PAGINATE_RATES() {
    return state.paginateRates;
  },
};

export default {
  state,
  mutations,
  actions,
  getters,
};
