import Tooltip from './components/Tooltip.vue'
import { setDefaults, TooltipOptions } from './options'
import { App } from 'vue'
import { PopperDirective } from './directive'

const VueTooltip = {
	install(app: App, options: TooltipOptions) {
		app.component('Tooltip', Tooltip)
		app.directive('znpb-tooltip', PopperDirective)
		setDefaults(options)
	}
}

export default VueTooltip

export {
	Tooltip,
	PopperDirective
}
