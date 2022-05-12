export function SET_USER(state, user) {
  state.user = user
  state.isLogged = true
  state.error = false
  state.errorMessage = ''
}

export function SET_TOKEN(state, token) {
  state.token = token
}

export function logout(state) {
  state.user = null
  state.isLogged = false
  state.token = null
}