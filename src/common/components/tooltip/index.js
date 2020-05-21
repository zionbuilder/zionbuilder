import TooltipComponent from './Tooltip.vue'
import { setDefaults } from './options'

const VueTooltip = {
	install (Vue, options) {
		Vue.component('Tooltip', TooltipComponent)
		setDefaults(options)
	}
}

// Automatic installation if Vue has been added to the global scope.
if (typeof window !== 'undefined' && window.Vue) {
	window.Vue.use(VueTooltip)
}

export default VueTooltip

export const Tooltip = TooltipComponent
