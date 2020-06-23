import { Checkbox, CheckboxGroup, CheckboxSwitch } from './elements/checkbox'
import { InputSelect } from './elements/select'
import { ColorPicker } from './elements/colorpicker'
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
	CustomSelector
]

export const Forms = {
	install (Vue, opts = {}) {
		components.forEach(component => {
			Vue.component(component.name, component)
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
	InputDate
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
	CustomSelector
}
