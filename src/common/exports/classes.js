import EventBus from '@/editor/EventBus'
import { InjectionComponentsManager } from '@/common/components/injections'
import { addFilter, applyFilters } from '@/utils/filters'
import OptionsManager from '@/editor/manager/options'
import ServerRequest from '../serverRequest'

window.zb.injections = new InjectionComponentsManager()

// Event bus
window.zb.on = EventBus.addEventListener.bind(EventBus)
window.zb.off = EventBus.removeEventListener.bind(EventBus)
window.zb.trigger = EventBus.dispatchEvent.bind(EventBus)

// Filters
window.zb.addFilter = addFilter
window.zb.applyFilters = applyFilters

window.zb.options = OptionsManager

window.zb.ajax = new ServerRequest()

// Export as module
export default window.zb.injections
