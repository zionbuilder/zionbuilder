import { Tooltip } from '@/common/components/tooltip'
import forms, { Forms } from '@/common/components/forms'
import { GradientPreview, GradientGenerator, GradientLibrary, getDefaultGradientConfig } from '@/common/components/gradient'
import ActionsOverlay from '@/common/components/forms/elements/actions-overlay/ActionsOverlay'
import Accordion from '@/common/components/accordion/Accordion.vue'
import HorizontalAccordion from '@/common/components/horizontal-accordion/HorizontalAccordion.vue'
import Notice from '@/common/components/Notice'
import Tab from '@/common/components/tabs/tab.vue'
import Tabs from '@/common/components/tabs/tabs.vue'
import BaseIcon from '@/common/components/BaseIcon.vue'
import BaseButton from '@/common/components/BaseButton.vue'
import { Modal, ModalConfirm } from '@/common/components/modal'
import Loader from '@/common/components/Loader'
import { ColorPicker } from '@/common/components/colorpicker'
import IconPackGrid from '@/common/components/IconPackGrid.vue'

// Directives
import { clickOutside } from '@/common/plugins/clickOutside'

// Utilities
import { InjectionComponentsManager, Injection } from '@/common/components/injections'

window.zb = window.zb || {}

window.zb.components = {
	// General
	Loader,
	Modal,
	ModalConfirm,
	BaseIcon,
	BaseButton,
	Tooltip,
	Accordion,
	Notice,
	Tabs,
	Tab,
	ColorPicker,
	Injection,
	// Specific
	ActionsOverlay,
	GradientPreview,
	GradientGenerator,
	GradientLibrary,
	HorizontalAccordion,
	IconPackGrid,
	// Forms
	forms
}

window.zb.utils = {
	getDefaultGradientConfig
}

window.zb.directives = {
	clickOutside
}

window.zb.plugins = {
	Forms
}

window.zb.injections = new InjectionComponentsManager()
