import { createStore } from 'vuex'

// Load modules
import main from './modules/main'
import devices from './modules/devices'
import history from './modules/history'
import pageContent from './modules/pageContent'
import dataSets from './modules/dataSets'
import interactions from './modules/interactions'
import classes from './modules/classes'
import templates from './modules/templates'
import Library from './modules/library'

// Load modules from admin
// TODO: use data
// import options from '../../../admin/src/store/modules/options'

const debug = process.env.NODE_ENV !== 'production'

export const store = createStore({
	modules: {
		main,
		devices,
		history,
		pageContent,
		dataSets,
		interactions,
		classes,
		templates,
		Library
	},
	strict: false
})
