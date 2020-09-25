import { Checkbox, CheckboxGroup, CheckboxSwitch } from './elements/checkbox'
import { InputSelect } from './elements/select'
import { ColorPicker, Color } from './elements/colorpicker'
import { InputNumber, InputNumberUnit } from './elements/InputNumber'
import { InputRange, InputRangeDynamic } from './elements/range'
import { InputMedia } from './elements/media'
import { InputSwitch } from './elements/switch'
import { InputBackgroundImage } from './elements/background-image'
import { InputBackgroundVideo } from './elements/background-video'
import { InputImage } from './elements/featured-image'
import { Editor } from './elements/editor'
import { CustomCode } from './elements/code'
import { RadioImage } from './elements/radio_image'
import { InputDate } from './elements/InputDate'
import { ShapeDividers, ShapeDividerComponent, SvgMask } from './elements/shape-dividers'

// Layout
import { InputWrapper } from './elements/inputWrapper'
import { Label as InputLabel } from './elements/label'
import { RadioGroup, RadioGroupItem, RadioIconItem } from './elements/radio'

import { InputTextAlign } from './elements/textAlign'
import { InputTextTransform } from './elements/textTransform'
import { InputTextShadow } from './elements/textShadow'
import { InputBorderControl, InputBorderTabs, InputBorderRadius, InputBorderRadiusTabs } from './elements/borders'
import { BaseInput } from './elements/input'
import { CustomSelector } from './elements/custom-selector'

// Misc
import { EmptyList } from './elements/empty-list'
import { ActionsOverlay } from './elements/actions-overlay'
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
	CustomCode,
	RadioImage,
	InputDate,
	// Needs testing
	InputSelect,
	Checkbox,
	CheckboxGroup,
	CheckboxSwitch,
	ColorPicker,
	Color,
	InputNumber,
	InputNumberUnit,
	RadioGroup,
	RadioGroupItem,
	RadioIconItem,
	InputTextAlign,
	InputTextTransform,
	InputTextShadow,
	InputBorderControl,
	InputBorderTabs,
	InputBorderRadius,
	InputBorderRadiusTabs,
	InputImage,
	CustomSelector,
	ShapeDividers,
	ShapeDividerComponent,
	SvgMask
]

export const Forms = {
	install (app) {
		components.forEach(component => {
			app.component(component.name, component)
		})
	}
}

export default {
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
	CustomCode,
	RadioImage,
	ActionsOverlay,
	InputLabel,
	InputSelect,
	Checkbox,
	CheckboxGroup,
	CheckboxSwitch,
	ColorPicker,
	Color,
	InputNumber,
	InputNumberUnit,
	RadioGroup,
	RadioGroupItem,
	RadioIconItem,
	InputTextAlign,
	InputTextTransform,
	InputTextShadow,
	InputBorderControl,
	InputBorderTabs,
	InputBorderRadius,
	InputBorderRadiusTabs,
	InputImage,
	CustomSelector,
	InputDate,
	ShapeDividers,
	ShapeDividerComponent,
	SvgMask
}

export {
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
	CustomCode,
	RadioImage,
	InputDate,
	// Needs testing
	InputSelect,
	Checkbox,
	CheckboxGroup,
	CheckboxSwitch,
	ColorPicker,
	Color,
	InputNumber,
	InputNumberUnit,
	RadioGroup,
	RadioGroupItem,
	RadioIconItem,
	InputTextAlign,
	InputTextTransform,
	InputTextShadow,
	InputBorderControl,
	InputBorderTabs,
	InputBorderRadius,
	InputBorderRadiusTabs,
	InputImage,
	CustomSelector,
	ShapeDividers,
	ShapeDividerComponent,
	SvgMask
}
