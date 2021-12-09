// Utils
import { applyFilters } from '@zb/hooks'

// Forms component
import {
	BaseInput,
	InputDatePicker,
	InputShapeDividers,
	ShapeDividerComponent,
	InputColorPicker,
	InputCustomSelector,
	InputSelect,
	InputRadioImage,
	InputRange,
	InputIcon,
	InputRangeDynamic,
	InputEditor,
	InputMedia,
	InputFile,
	InputImage,
	InputTextShadow,
	InputCode,
	InputNumber,
	InputNumberUnit,
	InputCheckbox,
	InputCheckboxGroup,
	InputCheckboxSwitch,
	InputTextAlign,
	InputBorderTabs,
	InputBackgroundVideo,
	InputMarginPadding
} from '../components'

const options = {
	text: {
		id: '',
		component: BaseInput,
		dynamic: {
			type: 'TEXT'
		}
	},
	icon_library: {
		id: 'icon_library',
		component: InputIcon,
		config: {
			// Can be one of the following
			barebone: true
		}
	},
	textarea: {
		id: 'textarea',
		component: BaseInput,
		dynamic: {
			type: 'TEXT'
		}
	},
	select: {
		id: 'select',
		component: InputSelect
	},
	slider: {
		id: 'slider',
		component: InputRange
	},
	dynamic_slider: {
		id: 'dynamic_slider',
		component: InputRangeDynamic
	},
	editor: {
		id: 'editor',
		component: InputEditor,
		dynamic: {
			type: 'TEXT'
		}
	},
	media: {
		id: 'media',
		component: InputMedia
	},
	file: {
		id: 'file',
		component: InputFile
	},
	image: {
		id: 'image',
		component: InputImage
	},
	number: {
		id: 'number',
		component: InputNumber
	},
	number_unit: {
		id: 'number_unit',
		component: InputNumberUnit
	},
	code: {
		id: 'code',
		component: InputCode
	},
	custom_selector: {
		id: 'custom_selector',
		component: InputCustomSelector
	},
	colorpicker: {
		id: 'colorpicker',
		component: InputColorPicker,
		dynamic: {
			type: 'TYPE_HIDDEN',
			custom_dynamic: true
		}
	},
	checkbox: {
		id: 'checkbox',
		component: InputCheckbox
	},
	radio_image: {
		id: 'radio_image',
		component: InputRadioImage
	},
	checkbox_group: {
		id: 'checkbox_group',
		component: InputCheckboxGroup
	},
	checkbox_switch: {
		id: 'checkbox_switch',
		component: InputCheckboxSwitch
	},
	text_align: {
		id: 'text_align',
		component: InputTextAlign
	},
	borders: {
		id: 'borders',
		component: InputBorderTabs
	},
	shadow: {
		id: 'shadow',
		component: InputTextShadow
	},
	video: {
		id: 'video',
		component: InputBackgroundVideo
	},
	date_input: {
		id: 'date_input',
		component: InputDatePicker
	},
	shape_dividers: {
		id: 'shape_dividers',
		component: InputShapeDividers
	},
	shape_component: {
		id: 'shape_component',
		component: ShapeDividerComponent
	},
	margin_padding: {
		id: 'margin_padding',
		component: InputMarginPadding
	}

}

export const useOptions = () => {

	/**
	 * Get Option Component
	 *
	 * Will return the requested option component tag
	 *
	 * @param {Object} schema The option schema
	 */
	const getOption = (schema, model = null, formModel = {}) => {
		let optionConfig = options[schema.type]
		optionConfig = applyFilters('zionbuilder/getOptionConfig', optionConfig, schema, model, formModel)

		if (!optionConfig) {
			// eslint-disable-next-line
			console.warn(`Option type ${schema.type} not found. Please register the option type using ZionBuilderApi.options.registerOption!`)
			return {}
		}

		return optionConfig
	}

	/**
	 * Get Option Component
	 *
	 * Will return the requested option component tag
	 *
	 * @param {String} optionId The option type id for which we need to return the component
	 */
	const getOptionComponent = (schema, model = null, formModel = {}) => {
		const optionConfig = getOption(schema.type)
		return applyFilters('zionbuilder/getOption', optionConfig.component, schema, model, formModel)
	}

	/**
	 * Register Option
	 *
	 * Will register a new dynamic option
	 *
	 * @param {Object} optionConfig Object config
	 */
	const registerOption = (optionConfig) => {
		// Check if the config contains the option type id
		if (!optionConfig.hasOwnProperty('id')) {
			// eslint-disable-next-line
			console.warn('You need to specify the option type id.', optionConfig)
		}

		// Check if the option contains the component needed to render the option type
		if (!optionConfig.hasOwnProperty('component')) {
			// eslint-disable-next-line
			console.warn('You need to specify the option type id.', optionConfig)
		}

		options[optionConfig.id] = optionConfig
	}

	return {
		registerOption,
		getOptionComponent,
		getOption
	}
}