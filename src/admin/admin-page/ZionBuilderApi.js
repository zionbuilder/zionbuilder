import L10n from './L10n'
import routes from './router'
import store from './store'
import { addFilter, applyFilters } from '@/utils/filters'
import Injections from '@zb/injections'

import {
	Tooltip,
	IconPackGrid
} from '@zb/components'
import {
	BaseInput
} from '@zb/components/forms'

// Utils
import * as utils from '@/utils'
import { errorInterceptor } from '@/api/ServiceInterceptor'

// Components
import ModalTemplateSaveButton from './components/ModalTemplateSaveButton'

const ZionBuilderApi = {
	L10n,
	routes,
	store,
	components: {
		ModalTemplateSaveButton,
		IconPackGrid,
		Tooltip,
		BaseInput
	},
	utils,
	interceptors: {
		errorInterceptor
	},

	// Injections Manager
	Injections,

	// Filters
	addFilter,
	applyFilters
}

window.ZionBuilderApi = ZionBuilderApi
export default ZionBuilderApi
