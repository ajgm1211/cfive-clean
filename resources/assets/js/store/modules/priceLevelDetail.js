import axios from "axios";
import toastr from "toastr";

const state = {
  currentPriceLevel: "",
  priceLevelRates: "",
  paginateRates: "",
  priceLevelData: "",
  modalEdit: false,
  duplicated: false,
};

const actions = {
  getPriceLevelDetail({ commit }, { id }) {
    axios.get(`/api/pricelevels/retrieve/${id}`).then((response) => {
      commit("SET_CURRENT_PRICE_LEVEL", response.data.data);
    });
  },

  listPriceLevelRates({ commit }, { id, page }) {
    axios
      .get(`api/pricelevels/details/${id}/list?page=${page}`)
      .then((response) => {
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
    axios
      .post(`api/pricelevels/details/${id}/store`, body)
      .then((response) => {
        dispatch("listPriceLevelRates", { id: currentId, page: page });
      })
      .catch((error) => {
        console.log(error.response.data.message);
        if (error.response.data.message == "Price level detail is not unique") {
          toastr.error("Price level detail must be unique");
        }
      });
  },

  duplicateRate({ dispatch }, { id, page, currentId }) {
    axios.post(`/api/pricelevels/details/${id}/duplicate`).then((response) => {
      dispatch("listPriceLevelRates", { id: currentId, page: page });
    });
  },

  deleteRate({ dispatch }, { id, page, currentId }) {
    axios.put(`/api/pricelevels/details/${id}/destroy`).then((response) => {
      dispatch("listPriceLevelRates", { id: currentId, page: page });
    });
  },

  deleteMultiple({ dispatch }, { body, id, page }) {
    axios.put(`/api/pricelevels/details/destroyAll`, body).then((response) => {
      dispatch("listPriceLevelRates", { id: id, page: page });
    });
  },

  editPriceLevel({ dispatch, commit }, { body, id, currentId, page }) {
    axios
      .post(`/api/pricelevels/details/${id}/update`, body)
      .then((response) => {
        dispatch("listPriceLevelRates", { id: currentId, page: page });
        commit("SET_MODAL_EDIT", false);
      })
      .catch((error) => {
        if (error.response.data.message == "Price level detail is not unique") {
          toastr.error("Price level detail must be unique");
        }
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

  SET_MODAL_EDIT(state, value) {
    state.modalEdit = value;
  },

  SET_DUPLICATED(state, value) {
    state.duplicated = value;
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

  GET_MODAL_EDIT() {
    return state.modalEdit;
  },

  GET_DUPLICATED() {
    return state.duplicated;
  },
};

export default {
  state,
  mutations,
  actions,
  getters,
};
