import L10n from './L10n'
import routes from './router'
import store from './store'
import { addFilter, applyFilters } from '@/utils/filters'
import { InjectionComponentsManager } from '@/common/components/injections'
import { Tooltip } from '@/common/components/tooltip'
import { BaseInput } from '@/common/components/forms/elements/input'

// Utils
import * as utils from '@/utils'

// Components
import IconPackGrid from '@/common/components/IconPackGrid.vue'
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

	// Injections Manager
	Injections: new InjectionComponentsManager(),

	// Filters
	addFilter,
	applyFilters
}

window.ZionBuilderApi = ZionBuilderApi
export default ZionBuilderApi
