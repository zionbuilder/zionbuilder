import { App } from 'vue'

// General components
import { Accordion } from './components/Accordion'
import { ActionsOverlay } from './components/ActionsOverlay'
import { BaseInput } from './components/BaseInput'
import { Button } from './components/Button'
import { ChangesBullet } from './components/ChangesBullet'
import { ColorPicker } from './components/Colorpicker'
import { EmptyList } from './components/EmptyList'
import { GradientPreview, GradientGenerator, GradientLibrary } from './components/Gradient'
import { HorizontalAccordion } from './components/HorizontalAccordion'
import { Icon } from './components/Icon'
import { Injection } from './components/injections'
import { InlineEdit } from './components/InlineEdit'

// Inputs
import { InputBackgroundImage } from './components/InputBackgroundImage'
import { InputBackgroundVideo } from './components/InputBackgroundVideo'
import { InputBorderControl, InputBorderTabs, InputBorderRadius, InputBorderRadiusTabs } from './components/InputBorders'
import { InputCheckbox, InputCheckboxGroup, InputCheckboxSwitch } from './components/InputCheckbox'
import { InputCode } from "./components/InputCode"
import { InputColorPicker } from './components/InputColorPicker'
import { InputCustomSelector } from './components/InputCustomSelector'
import { InputDatePicker } from './components/InputDatePicker'
import { InputEditor } from './components/InputEditor'
import { InputImage } from './components/InputImage'
import { InputLabel } from './components/InputLabel'
import { InputMedia } from './components/InputMedia'
// INPUTNUMBER
import { InputRadio, InputRadioGroup, InputRadioIcon } from './components/InputRadio'

import { Loader } from './components/Loader'
import { Label } from './components/Label'
import { Tab, Tabs } from './components/Tabs'
import { Notice } from './components/Notice'
import { UpgradeToPro } from './components/UpgradeToPro'
import IconPackGrid from './components/IconPackGrid.vue'
import { Sortable } from '@zionbuilder/sortable'
import { createOptionsInstance } from './components/forms'
import { RadioImage } from './components/RadioImage'
export * as forms from './components/forms'
export * as utils from './utils/'
import { Tooltip } from '@zionbuilder/tooltip'
import { Modal, ModalConfirm } from '@zionbuilder/modal'

import { InputShapeDividers, ShapeDividerComponent, SvgMask } from './components/InputShapeDividers'
import { InputWrapper } from './components/InputWrapper'

const components = [
	Button,
	UpgradeToPro,
	Label,
	EmptyList,

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
	RadioImage,
	InlineEdit,

	// Specific
	ActionsOverlay,
	GradientPreview,
	GradientGenerator,
	GradientLibrary,
	HorizontalAccordion,
	IconPackGrid,
	// Forms
	Sortable,
	ChangesBullet,

	// Inputs
	InputBackgroundImage,
	InputBackgroundVideo,
	InputCheckboxGroup,
	InputCheckbox,
	InputCheckboxSwitch,
	InputLabel,
	InputCode,
	InputRadio,
	InputRadioGroup,
	InputRadioIcon,
	InputDatePicker,
	InputColorPicker,
	InputCustomSelector,
	InputShapeDividers,
	ShapeDividerComponent,
	SvgMask,
	InputBorderControl,
	InputBorderTabs,
	InputBorderRadius,
	InputBorderRadiusTabs,
	InputMedia,
	InputImage,
	InputEditor,
	BaseInput,
	InputWrapper
]

const install = (app: App) => {
	components.forEach(component => {
		app.component(component.name, component);
	});
}

export const options = createOptionsInstance()

export {
	install,
	Button,
	UpgradeToPro,
	Label,
	EmptyList,

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
	RadioImage,

	// Specific
	ActionsOverlay,
	GradientPreview,
	GradientGenerator,
	GradientLibrary,
	HorizontalAccordion,
	IconPackGrid,
	// Forms
	Sortable,
	ChangesBullet,

	// Inputs
	InputBackgroundImage,
	InputBackgroundVideo,
	InputCheckboxGroup,
	InputCheckbox,
	InputCheckboxSwitch,
	InputLabel,
	InputCode,
	InputRadio,
	InputRadioGroup,
	InputRadioIcon,
	InputDatePicker,
	InputColorPicker,
	InputCustomSelector,
	InputShapeDividers,
	ShapeDividerComponent,
	SvgMask,
	InputBorderControl,
	InputBorderTabs,
	InputBorderRadius,
	InputBorderRadiusTabs,
	InputMedia,
	InputImage,
	InputEditor,
	BaseInput,
	InputWrapper
}