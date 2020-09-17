import * as utils from '@/utils'

import { addFilter, applyFilters } from './utils/filters'

import ElementFocusMarshall from '@/editor/utils/ElementFocusMarshall'
import ElementsManager from '@/editor/manager/elements'
import EventBus from '@/editor/EventBus'
import OptionsManager from '@/editor/manager/options'
import Vue from 'vue'

import Injections from '@zb/injections'
import {
	Tooltip,
	GradientPreview,
	ActionsOverlay
} from '@zb/components'
import {
	BaseInput
} from '@zb/components/forms'

const eventBusInstance = EventBus

const ZionBuilderApi = {
	// Access to Vue Instance
	Vue,

	// eventBus
	on: eventBusInstance.addEventListener.bind(eventBusInstance),
	off: eventBusInstance.removeEventListener.bind(eventBusInstance),
	trigger: eventBusInstance.dispatchEvent.bind(eventBusInstance),

	// Filters
	addFilter,
	applyFilters,

	// Options Manager
	OptionsManager,

	// Elements Manager
	ElementsManager,

	// Injections Manager
	Injections,

	components: {
		Tooltip,
		BaseInput,
		GradientPreview,
		ActionsOverlay
	},

	utils: utils,
	ajax: new utils.ServerRequest(),
	...window.ZnPbInitalData,
	editor: {
		ElementFocusMarshall: ElementFocusMarshall()
	}
}

window.ZionBuilderApi = ZionBuilderApi
export default ZionBuilderApi
