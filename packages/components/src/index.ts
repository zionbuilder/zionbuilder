import { App } from 'vue'

// General components
import { Accordion } from './components/Accordion'
import { ActionsOverlay } from './components/ActionsOverlay'
import { Button } from './components/Button'
import { ChangesBullet } from './components/ChangesBullet'
import { ColorPicker } from './components/Colorpicker'
import { EmptyList } from './components/EmptyList'
import { GradientPreview, GradientGenerator, GradientLibrary } from './components/Gradient'
import { HorizontalAccordion } from './components/HorizontalAccordion'
import { Icon } from './components/Icon'
import { Injection } from './components/Injection'
import { InlineEdit } from './components/InlineEdit'

// Inputs
import { BaseInput } from './components/BaseInput'
import { InputBackgroundImage } from './components/InputBackgroundImage'
import { InputBackgroundVideo } from './components/InputBackgroundVideo'
import { InputBorderControl, InputBorderTabs, InputBorderRadius, InputBorderRadiusTabs } from './components/InputBorders'
import { InputCheckbox, InputCheckboxGroup, InputCheckboxSwitch } from './components/InputCheckbox'
import { InputCode } from "./components/InputCode"
import { InputColorPicker, Color } from './components/InputColorPicker'
import { InputCustomSelector } from './components/InputCustomSelector'
import { InputDatePicker } from './components/InputDatePicker'
import { InputEditor } from './components/InputEditor'
import { InputImage } from './components/InputImage'
import { InputLabel } from './components/InputLabel'
import { InputMedia } from './components/InputMedia'
import { InputSelect } from './components/InputSelect'
import { InputRadio, InputRadioGroup, InputRadioIcon } from './components/InputRadio'
import { InputShapeDividers, ShapeDividerComponent, SvgMask } from './components/InputShapeDividers'
import { InputWrapper } from './components/InputWrapper'
import { RadioImage } from './components/RadioImage'
import { InputRange, InputRangeDynamic } from './components/InputRange'
import { InputTextShadow } from './components/InputTextShadow'
import { InputNumber, InputNumberUnit } from './components/InputNumber'
import { InputTextAlign } from './components/InputTextAlign'
import { InputTextTransform } from './components/InputTextTransform'
import { OptionsForm, OptionWrapper } from './components/OptionsForm'

import { Loader } from './components/Loader'
import { Label } from './components/Label'
import { Tab, Tabs } from './components/Tabs'
import { Notice } from './components/Notice'
import { UpgradeToPro } from './components/UpgradeToPro'
import IconPackGrid from './components/IconPackGrid.vue'
import { Sortable } from '@zionbuilder/sortable'
export * as utils from '@utils'
import { Tooltip } from '@zionbuilder/tooltip'
import { Modal, ModalConfirm, ModalTemplateSaveButton } from '@zionbuilder/modal'
import { getDefaultGradient } from './utils'
export * from '@data'


const components = [
	Button,
	UpgradeToPro,
	Label,
	EmptyList,

	// General
	Modal,
	ModalConfirm,
	ModalTemplateSaveButton,
	Icon,
	Tooltip,
	Loader,
	Accordion,
	Notice,
	Tabs,
	Tab,
	ColorPicker,
	Color,
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
	InputWrapper,
	InputSelect,
	InputRange,
	InputRangeDynamic,
	InputTextShadow,
	InputNumber,
	InputNumberUnit,
	InputTextAlign,
	InputTextTransform,
	OptionsForm,
	OptionWrapper,
	getDefaultGradient
]

const install = (app: App) => {
	components.forEach(component => {
		app.component(component.name, component);
	});
}

export {
	install,
	Button,
	UpgradeToPro,
	Label,
	EmptyList,

	// General
	Modal,
	ModalConfirm,
	ModalTemplateSaveButton,
	Icon,
	Tooltip,
	Loader,
	Accordion,
	Notice,
	Tabs,
	Tab,
	ColorPicker,
	Color,
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
	InputWrapper,
	InputSelect,
	InputRange,
	InputRangeDynamic,
	InputTextShadow,
	InputNumber,
	InputNumberUnit,
	InputTextAlign,
	InputTextTransform,
	OptionsForm,
	OptionWrapper,
	getDefaultGradient
}