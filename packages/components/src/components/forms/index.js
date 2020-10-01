// Utils
import { createOptionsInstance } from './options'

// General components
import { InputCheckboxSwitch, InputCheckboxGroup, InputCheckbox } from '../InputCheckbox'
import { InputSelect } from '../InputSelect'
import { InputColorPicker, Color } from '../InputColorPicker'
import { InputNumber, InputNumberUnit } from '../InputNumber'
import { InputRange, InputRangeDynamic } from '../InputRange'
import { InputMedia } from '../InputMedia'
import { InputSwitch } from '../InputSwitch'

// Inputs
import { InputBackgroundImage } from '../InputBackgroundImage'
import { InputBackgroundVideo } from '../InputBackgroundVideo'
import { InputImage } from '../InputImage'
import { InputEditor } from '../InputEditor'
import { InputCode } from '../InputCode'
import { RadioImage } from '../RadioImage'
import { InputDatePicker } from '../InputDatePicker'
import { InputShapeDividers, ShapeDividerComponent, SvgMask } from '../InputShapeDividers'

// Layout
import { InputWrapper } from './elements/inputWrapper'
import InputWrapper2 from './elements/InputWrapper.vue'
import OptionsForm from './elements/OptionsForm.vue'
import { InputLabel } from '../InputLabel'
import { InputRadioGroup, InputRadio, InputRadioIcon } from '../InputRadio'

import { InputTextAlign } from '../InputTextAlign'
import { InputTextTransform } from '../InputTextTransform'
import { InputTextShadow } from '../InputTextShadow'
import { InputBorderControl, InputBorderTabs, InputBorderRadius, InputBorderRadiusTabs } from '../borders'
import { BaseInput } from '../BaseInput'
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
	InputEditor,
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
	InputShapeDividers,
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
	InputEditor,
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
	InputShapeDividers,
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
	InputEditor,
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
	InputShapeDividers,
	ShapeDividerComponent,
	SvgMask,
	InputWrapper2,
	OptionsForm,
	createOptionsInstance
}
