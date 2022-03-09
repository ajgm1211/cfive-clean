import state from './state'
import * as mutations from './mutations'
import * as actions from './actions'
import * as actionsFailed from './actionsFailed'
import * as actionsContact from './actionsContact'

export default {
    namespaced: true,
    mutations,
    actions,
    actionsFailed,
    actionsContact,
    state
}