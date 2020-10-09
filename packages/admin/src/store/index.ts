import { createStore } from 'vuex'

// Load modules
import options from './modules/options'
import googleFonts from './modules/googleFonts'
import users from './modules/users'
import templates from './modules/templates'
import dataSets from './modules/dataSets'
import general from './modules/general'

const debug = process.env.NODE_ENV !== 'production'

export const store = createStore({
	modules: {
		options,
		googleFonts,
		users,
		templates,
		dataSets,
		general
	},
	strict: debug
})
