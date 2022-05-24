import axios from "axios";

const state = {
  segmentConfiguration: {},
  segmentTypes:{},
  user:null
};

const actions = {
  getSegmentConfiguration({ commit }, {page=1}) {
    return axios.get(`/api/segment-configuration/list?page=${page}`).then((response) => {
      commit("SET_SEGMENT_CONFIGURATION", response.data.data);
    });
  },
  getSegmentTypes({ commit }, {segmentConfigurationIds}) {
    return axios.post(`/api/segment-configuration/types`, segmentConfigurationIds).then((response) => {
      commit("SET_SEGMENT_TYPES", response.data.data);
    });
  },
  postSegmentConfiguration({ commit, dispatch }, {segment, ids, page}){
    return axios.post(`/api/segment-configuration/store`, segment).then((response) => {
      dispatch('getSegmentConfiguration', {page:page})
      dispatch('getSegmentTypes', {segmentConfigurationIds:ids})
    });
  },
  putSegmentConfiguration({ commit, dispatch }, {segments, ids, page}){
    return axios.post( `/api/segment-configuration/update`, {segments}).then((response) => {
      dispatch('getSegmentConfiguration', {page:page})
      dispatch('getSegmentTypes', {segmentConfigurationIds:ids})
    });
  }
};

const mutations = {
    SET_SEGMENT_CONFIGURATION(state, value){
      state.segmentConfiguration = value
    },
    SET_SEGMENT_TYPES(state, values){
      state.segmentTypes = values
    },
    SET_USER(state, values){
      state.user = values
    }
};

const getters = {
  GET_SEGMENT_CONFIGURATION() {
      return state.segmentConfiguration;
  },
  GET_SEGMENT_TYPES() {
    return state.segmentTypes;
  },
  GET_USER(){
    return state.user;
  }
};

export default {
  state,
  mutations,
  actions,
  getters,
};
