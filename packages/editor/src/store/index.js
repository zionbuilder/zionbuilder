import { createStore } from 'vuex'

// Load modules
import main from './modules/main'
import history from './modules/history'
import pageContent from './modules/pageContent'
import interactions from './modules/interactions'
import Library from './modules/library'

// Load modules from admin
// TODO: use data
// import options from '../../../admin/src/store/modules/options'

const debug = process.env.NODE_ENV !== 'production'

export const store = createStore({
	modules: {
		main,
		history,
		pageContent,
		interactions,
		Library
	},
	strict: false
})
