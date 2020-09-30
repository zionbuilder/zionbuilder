// General components
import { Button } from './components/Button'
import { Loader } from './components/Loader'
import { Label } from './components/Label'
import { Tab, Tabs } from './components/Tabs'
import { Accordion } from './components/Accordion'
import { Notice } from './components/Notice'
import { UpgradeToPro } from './components/UpgradeToPro'
import { ColorPicker } from './components/Colorpicker'
import { GradientPreview, GradientGenerator, GradientLibrary } from './components/Gradient'
import { ActionsOverlay } from './components/ActionsOverlay'
import { HorizontalAccordion } from './components/HorizontalAccordion'
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