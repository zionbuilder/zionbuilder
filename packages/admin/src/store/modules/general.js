import * as types from '../mutation-types'

const state = {
	is_pro_active: window.ZnPbAdminPageData.is_pro_active
}

const getters = {
	isPro: state => state.is_pro_active
}

const actions = {

}

const mutations = {

}

export default {
	state,
	getters,
	actions,
	mutations
}
