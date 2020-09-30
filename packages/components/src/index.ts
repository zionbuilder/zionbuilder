import Button from './components/Button/Button.vue'
import Loader from './components/Loader.vue'
import Label from './components/Label/Label.vue'
import Tab from './components/Tabs/Tab.vue'
import Tabs from './components/Tabs/Tabs.vue'
import Accordion from './components/Accordion/Accordion.vue'
import { Notice } from './components/Notice'
import UpgradeToPro from './components/UpgradeToPro/UpgradeToPro.vue'
import { ColorPicker } from './components/colorpicker'
import { GradientPreview, GradientGenerator, GradientLibrary } from './components/gradient'
import ActionsOverlay from './components/forms/elements/actions-overlay/ActionsOverlay.vue'
import HorizontalAccordion from './components/HorizontalAccordion/HorizontalAccordion.vue'
import { Icon } from './components/Icon'
import IconPackGrid from './components/IconPackGrid.vue'
import { Injection } from './components/injections'
import { Sortable } from '@zionbuilder/sortable'
import { ChangesBullet } from './components/ChangesBullet'
import { createOptionsInstance } from './components/forms'
export * as forms from './components/forms'
export * as utils from './utils/'
import { Tooltip } from '@zionbuilder/tooltip'
import { Modal, ModalConfirm } from '@zionbuilder/modal'

export {
	Button,
	UpgradeToPro,
	Label,

	// General
	Modal,
	ModalConfirm,
	Icon,
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