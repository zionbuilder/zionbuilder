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

// Plugins and directives
import { clickOutside } from '@/common/plugins/clickOutside'
import L10N from '@/common/plugins/l10n'

// Utilities
import { InjectionComponentsManager, Injection } from '@/common/components/injections'

window.zb = window.zb || {}

/**
 * Components
 */
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

/**
 * Utils
 */
window.zb.utils = {
	getDefaultGradientConfig
}

/**
 * Directives
 */
window.zb.directives = {
	clickOutside
}

/**
 * Vue plugins
 */
window.zb.plugins = {
	Forms,
	L10N
}

window.zb.l10n = new L10N()

/**
 * Injections utility
 */
window.zb.injections = new InjectionComponentsManager()
