import { createStore } from 'vuex'

// Load modules
import templates from './modules/templates'
import general from './modules/general'
const debug = process.env.NODE_ENV !== 'production'

export const store = createStore({
	modules: {
		templates,
		general
	},
	strict: debug
})
