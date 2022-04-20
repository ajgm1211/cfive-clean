export function SET_USER(state, user) {
  state.user = user
  state.isLogged = true
  state.error = false
  state.errorMessage = ''
}

export function SET_TOKEN(state, token) {
  state.token = token
}

export function SET_COMPANY_USER(state, companyUser){
  state.companyUser = companyUser
}