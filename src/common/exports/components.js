import { Tooltip } from '@/common/components/tooltip'
import { ColorPicker } from '@/common/components/colorpicker'
import { GradientPreview, GradientGenerator, GradientLibrary } from '@/common/components/gradient'
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
import IconPackGrid from '@/common/components/IconPackGrid.vue'
import { Injection } from '@/common/components/injections'
import forms from '@/common/components/forms'
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
