import * as utils from '@/utils'
import getDefaultGradientConfig from '@/common/models/defaultGradient'
import EventBus from '@/editor/EventBus'

window.zb = window.zb || {}

// Event bus
window.zb.on = EventBus.addEventListener.bind(EventBus)
window.zb.off = EventBus.removeEventListener.bind(EventBus)
window.zb.trigger = EventBus.dispatchEvent.bind(EventBus)

window.zb.utils = {
	...utils,
	getDefaultGradientConfig
}

// Export as module
export default window.zb.utils
