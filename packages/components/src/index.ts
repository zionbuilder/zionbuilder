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
import { RadioImage } from './components/RadioImage'
export * as forms from './components/forms'
export * as utils from './utils/'
import { Tooltip } from '@zionbuilder/tooltip'
import { Modal, ModalConfirm } from '@zionbuilder/modal'

// Inputs
import { InputBackgroundVideo } from './components/InputBackgroundVideo'
import { InputCheckbox, InputCheckboxGroup, InputCheckboxSwitch } from './components/InputCheckbox'
import { InputLabel } from "./components/InputLabel";
import { InputCode } from "./components/InputCode";
import { InputRadio, InputRadioGroup, InputRadioIcon } from "./components/InputRadio";
import { InputDatePicker } from './components/InputDatePicker'
import { InputColorPicker } from './components/InputColorPicker'
import { InputCustomSelector } from './components/InputCustomSelector'
import { InputShapeDividers, ShapeDividerComponent, SvgMask } from './components/InputShapeDividers'
import { InputBorderControl, InputBorderTabs, InputBorderRadius, InputBorderRadiusTabs } from './components/borders'
import { InputMedia } from './components/InputMedia'
import { InputImage } from './components/InputImage'
import { InputEditor } from './components/InputEditor'
import { BaseInput } from './components/BaseInput'
import { InputWrapper } from './components/InputWrapper'
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

export const options = createOptionsInstance()