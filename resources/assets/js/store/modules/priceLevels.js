import axios from "axios";
import router from "../../main";


const state = {
  priceLevels: "",
};

const actions = {
  getPriceLevels({ commit }) {
    axios.get("/api/pricelevels/list/").then((response) => {
      commit("SET_PRICE_LEVELS", response.data.data);
    });
  },
  
  createPriceLevel({ commit }, { body }) {
    axios.post(`/api/pricelevels/store`, body).then((response) => {
      router.push({
        name: "price-rates",
        params: { id: response.data.data.id },
      });
    });
  },

  updatePriceLevel({ commit }, { id, body }) {
    axios.post(`api/pricelevels/${id}/update`, body).then((response) => {
      console.log(response)
    });
  },

  duplicatePriceLevel({ dispatch }, { id }) {
    axios.post(`/api/pricelevels/${id}/duplicate`).then((response) => {
      dispatch("getPriceLevels");
      alert('duplicated')
    });
  },

  deletePriceLevel({ dispatch }, { id }) {
    axios.put(`/api/pricelevels/${id}/delete`).then((response) => {
      dispatch("getPriceLevels");
    });
  },

  deleteSelectedPriceLevel({ dispatch }, { body }) {
    axios.put(`/api/pricelevels/deleteAll`, body).then((response) => {
      dispatch("getPriceLevels");
    });
  },
};

const mutations = {
  SET_PRICE_LEVELS(state, value) {
    state.priceLevels = value;
  },
};

const getters = {
  GET_PRICE_LEVELS() {
    return state.priceLevels;
  },
};

export default {
  state,
  mutations,
  actions,
  getters,
};
