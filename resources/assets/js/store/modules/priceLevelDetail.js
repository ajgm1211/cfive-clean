import axios from "axios";

const state = {
  currentPriceLevel: "",
  priceLevelRates: "",
};

const actions = {
  getPriceLevelDetail({ commit }, { id }) {
    axios.get(`/api/pricelevels/get/${id}`).then((response) => {
      commit("SET_CURRENT_PRICE_LEVEL", response.data);
      
    });
  },

  listPriceLevelRates({ commit }, {id}) {
    axios.get(`api/pricelevels/details/${id}/list`).then((response) => {
        console.log('response', response.data.data)
      commit("SET_PRICE_LEVEL_RATES", response.data.data);
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
};

const getters = {
  GET_CURRENT_PRICE_LEVEL() {
    return state.currentPriceLevel;
  },

  GET_PRICE_LEVEL_RATES() {
    return state.priceLevelRates;
  },
};

export default {
  state,
  mutations,
  actions,
  getters,
};
