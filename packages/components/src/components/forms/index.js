// Utils
import { createOptionsInstance } from './options'

// General components
import { InputCheckboxSwitch, InputCheckboxGroup, InputCheckbox } from '../InputCheckbox'
import { InputSelect } from './elements/select'
import { InputColorPicker, Color } from '../InputColorPicker'
import { InputNumber, InputNumberUnit } from '../InputNumber'
import { InputRange, InputRangeDynamic } from './elements/range'
import { InputMedia } from './elements/media'
import { InputSwitch } from './elements/switch'

// Inputs
import { InputBackgroundImage } from '../InputBackgroundImage'
import { InputBackgroundVideo } from '../InputBackgroundVideo'
import { InputImage } from './elements/featured-image'
import { Editor } from './elements/editor'
import { InputCode } from '../InputCode'
import { RadioImage } from './elements/radio_image'
import { InputDatePicker } from '../InputDatePicker'
import { ShapeDividers, ShapeDividerComponent, SvgMask } from './elements/shape-dividers'

// Layout
import { InputWrapper } from './elements/inputWrapper'
import InputWrapper2 from './elements/InputWrapper.vue'
import OptionsForm from './elements/OptionsForm.vue'
import { InputLabel } from '../InputLabel'
import { InputRadioGroup, InputRadio, InputRadioIcon } from '../InputRadio'

import { InputTextAlign } from '../InputTextAlign'
import { InputTextTransform } from './elements/textTransform'
import { InputTextShadow } from '../InputTextShadow'
import { InputBorderControl, InputBorderTabs, InputBorderRadius, InputBorderRadiusTabs } from './elements/borders'
import { BaseInput } from './elements/input'
import { InputCustomSelector } from '../InputCustomSelector'

// Misc
import { EmptyList } from '../EmptyList'
import { ActionsOverlay } from '../ActionsOverlay'

const components = [
	// ALL DONE
	BaseInput,
	InputWrapper,
	InputLabel,
	InputRange,
	InputRangeDynamic,
	InputMedia,
	InputSwitch,
	InputBackgroundImage,
	InputBackgroundVideo,
	EmptyList,
	ActionsOverlay,
	Editor,
	InputCode,
	RadioImage,
	InputDatePicker,
	// Needs testing
	InputSelect,
	InputCheckboxSwitch,
	InputCheckboxGroup,
	InputCheckbox,
	InputColorPicker,
	Color,
	InputNumber,
	InputNumberUnit,
	InputRadioGroup,
	InputRadio,
	InputRadioIcon,
	InputTextAlign,
	InputTextTransform,
	InputTextShadow,
	InputBorderControl,
	InputBorderTabs,
	InputBorderRadius,
	InputBorderRadiusTabs,
	InputImage,
	InputCustomSelector,
	ShapeDividers,
	ShapeDividerComponent,
	SvgMask,
	InputWrapper2,
	OptionsForm
]

const install = (app) => {
	components.forEach(component => {
		app.component(component.name, component)
	})
}

export default {
	install,
	BaseInput,
	InputWrapper,
	EmptyList,
	InputMedia,
	InputRange,
	InputRangeDynamic,
	InputBackgroundImage,
	InputBackgroundVideo,
	InputSwitch,
	Editor,
	InputCode,
	RadioImage,
	ActionsOverlay,
	InputLabel,
	InputSelect,
	InputCheckboxSwitch,
	InputCheckboxGroup,
	InputCheckbox,
	InputColorPicker,
	Color,
	InputNumber,
	InputNumberUnit,
	InputRadioGroup,
	InputRadio,
	InputRadioIcon,
	InputTextAlign,
	InputTextTransform,
	InputTextShadow,
	InputBorderControl,
	InputBorderTabs,
	InputBorderRadius,
	InputBorderRadiusTabs,
	InputImage,
	InputCustomSelector,
	InputDatePicker,
	ShapeDividers,
	ShapeDividerComponent,
	SvgMask,
	InputWrapper2,
	OptionsForm
}

export {
	install,
	// ALL DONE
	BaseInput,
	InputWrapper,
	InputLabel,
	InputRange,
	InputRangeDynamic,
	InputMedia,
	InputSwitch,
	InputBackgroundImage,
	InputBackgroundVideo,
	EmptyList,
	ActionsOverlay,
	Editor,
	InputCode,
	RadioImage,
	InputDatePicker,
	// Needs testing
	InputSelect,
	InputCheckboxSwitch,
	InputCheckboxGroup,
	InputCheckbox,
	InputColorPicker,
	Color,
	InputNumber,
	InputNumberUnit,
	InputRadioGroup,
	InputRadio,
	InputRadioIcon,
	InputTextAlign,
	InputTextTransform,
	InputTextShadow,
	InputBorderControl,
	InputBorderTabs,
	InputBorderRadius,
	InputBorderRadiusTabs,
	InputImage,
	InputCustomSelector,
	ShapeDividers,
	ShapeDividerComponent,
	SvgMask,
	InputWrapper2,
	OptionsForm,
	createOptionsInstance
}
