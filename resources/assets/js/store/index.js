import Vue from "vue";
import Vuex from "vuex";
import priceLevels from "../store/modules/priceLevels";
import priceLevelDetail from "../store/modules/priceLevelDetail";
import apiCredentials from "../store/modules/apiCredentials";
import SegmentConfiguration from "../store/modules/SegmentConfiguration";

Vue.use(Vuex);

export default new Vuex.Store({
  modules: {
    priceLevels,
    priceLevelDetail,
    apiCredentials,
    SegmentConfiguration
  },
});
