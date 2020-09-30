import BaseButton from './components/BaseButton/BaseButton.vue'
import Loader from './components/Loader.vue'
import Label from './components/Label/Label.vue'
import Tab from './components/tabs/tab.vue'
import Tabs from './components/tabs/tabs.vue'
import Accordion from './components/accordion/Accordion.vue'
import { Modal, ModalConfirm } from './components/modal'
import Notice from './components/Notice.vue'
import UpgradeToPro from './components/UpgradeToPro/UpgradeToPro.vue'
import { Tooltip } from '@zionbuilder/tooltip'
import { ColorPicker } from './components/colorpicker'
import { GradientPreview, GradientGenerator, GradientLibrary } from './components/gradient'
import ActionsOverlay from './components/forms/elements/actions-overlay/ActionsOverlay.vue'
import HorizontalAccordion from './components/horizontal-accordion/HorizontalAccordion.vue'
import BaseIcon from './components/BaseIcon.vue'
import IconPackGrid from './components/IconPackGrid.vue'
import { Injection } from './components/injections'
import { Sortable } from '@zionbuilder/sortable'
import { ChangesBullet } from './components/ChangesBullet'
import { createOptionsInstance } from './components/forms'
export * as forms from './components/forms'
export * as utils from './utils/'

export {
	BaseButton,
	UpgradeToPro,
	Label,

	// General
	Modal,
	ModalConfirm,
	BaseIcon,
	Tooltip,
	Loader,
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
	Sortable,
	ChangesBullet
}

export const options = createOptionsInstance()