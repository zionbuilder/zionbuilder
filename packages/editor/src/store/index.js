import { createStore } from 'vuex'

// Load modules
import main from './modules/main'
import pageContent from './modules/pageContent'
import interactions from './modules/interactions'

// Load modules from admin
// TODO: use data
// import options from '../../../admin/src/store/modules/options'

const debug = process.env.NODE_ENV !== 'production'

export const store = createStore({
	modules: {
		main,
		pageContent,
		interactions
	},
	strict: false
})
