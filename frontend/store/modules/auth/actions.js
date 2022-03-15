export function setToken({ commit }, token) {
    commit('SET_TOKEN', token)
}

export function setCurrentUser({ commit }, user) {
    commit('SET_USER', user)
  }