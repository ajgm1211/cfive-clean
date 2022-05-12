import Vue from 'vue'
import Vuex from 'vuex'
import auth from './modules/auth'

Vue.use(Vuex)

// export default new Vuex.Store({
const store = new Vuex.Store({
  state: {
    drawer: true,
    loading: false
  },
  modules: {
    auth
  }
})

Vue.prototype.$store = store

export default store