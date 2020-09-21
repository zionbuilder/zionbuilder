import Vuex from 'vuex'
import Vue from '@zb/vue'

// Load modules
import main from './modules/main'
import devices from './modules/devices'
import panels from './modules/panels'
import history from './modules/history'
import pageContent from './modules/pageContent'
import dataSets from './modules/dataSets'
import interactions from './modules/interactions'
import classes from './modules/classes'
import errors from './modules/errors'
import templates from './modules/templates'
import Library from './modules/library'

// Load modules from admin
import options from '@/admin/admin-page/store/modules/options'
Vue.use(Vuex)

const debug = process.env.NODE_ENV !== 'production'

export default new Vuex.Store({
	modules: {
		main,
		devices,
		panels,
		history,
		pageContent,
		dataSets,
		interactions,
		options,
		classes,
		errors,
		templates,
		Library
	},
	strict: false
})
