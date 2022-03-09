import state from './state'
import * as mutations from './mutations'
import * as actions from './actions'
import * as actionsFailed from './actionsFailed'

export default {
    namespaced: true,
    mutations,
    actions,
    actionsFailed,
    state
}