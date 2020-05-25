import Vuex from 'vuex'
import Vue from 'vue'

// Load modules
import options from './modules/options'
import googleFonts from './modules/googleFonts'
import errors from './modules/errors'
import systemInfo from './modules/systemInfo'
import users from './modules/users'
import templates from './modules/templates'
import dataSets from './modules/dataSets'
import general from './modules/general'

Vue.use(Vuex)

const debug = process.env.NODE_ENV !== 'production'

export default new Vuex.Store({
	modules: {
		options,
		googleFonts,
		errors,
		systemInfo,
		users,
		templates,
		dataSets,
		general
	},
	strict: debug
})
