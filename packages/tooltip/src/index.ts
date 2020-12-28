import Tooltip from './components/Tooltip.vue'
import { setDefaults, TooltipOptions } from './options'
import { App } from 'vue'

const VueTooltip = {
	install(app: App, options: TooltipOptions) {
		app.component('Tooltip', Tooltip)
		setDefaults(options)
	}
}

export default VueTooltip

export {
	Tooltip
}
