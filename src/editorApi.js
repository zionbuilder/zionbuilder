import * as utils from '@/utils'

import { addFilter, applyFilters } from './utils/filters'

import ElementFocusMarshall from '@/editor/utils/ElementFocusMarshall'
import ElementsManager from '@/editor/manager/elements'
import EventBus from '@/editor/EventBus'
import { InjectionComponentsManager } from '@/common/components/injections'
import L10n from './editor/L10n'
import OptionsManager from '@/editor/manager/options'
import { Tooltip } from './common/components/tooltip'
import { BaseInput } from './common/components/forms/elements/input'
import Vue from 'vue'

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

	l10n: L10n,

	// Injections Manager
	Injections: new InjectionComponentsManager(),

	components: {
		Tooltip,
		BaseInput
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
