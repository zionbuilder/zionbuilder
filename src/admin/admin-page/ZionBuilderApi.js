import L10n from './L10n'
import routes from './router'
import store from './store'

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
		IconPackGrid
	},
	utils
}

window.ZionBuilderApi = ZionBuilderApi
export default ZionBuilderApi
