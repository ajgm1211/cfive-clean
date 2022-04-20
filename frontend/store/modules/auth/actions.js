export function setToken({ commit }, token) {
    commit('SET_TOKEN', token)
}

export function setUser({ commit }, user) {
    commit('SET_USER', user)
}

export function setCompanyUser({ commit }, user) {
    commit('SET_COMPANY_USER', user)
}