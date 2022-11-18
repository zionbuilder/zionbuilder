// Utils
import { Component } from 'vue';
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
	InputSpacing,
	InputRepeater,
	UpgradeToPro,
	InputDimensions,
	InputHTML,
} from '../components';

interface Option {
	id: string;
	component: Component;
	dynamic?: Record<string, unknown>;
	config?: Record<string, unknown>;
	componentProps?: Record<string, unknown>;
}

const options: Option[] = [
	{
		id: 'text',
		component: BaseInput,
		dynamic: {
			type: 'TEXT',
		},
	},
	{
		id: 'icon_library',
		component: InputIcon,
		config: {
			// Can be one of the following
			barebone: true,
		},
	},
	{
		id: 'textarea',
		component: BaseInput,
		componentProps: {
			type: 'textarea',
		},
		dynamic: {
			type: 'TEXT',
		},
	},
	{
		id: 'password',
		componentProps: {
			type: 'password',
		},
		component: BaseInput,
	},
	{
		id: 'select',
		component: InputSelect,
	},
	{
		id: 'slider',
		component: InputRange,
	},
	{
		id: 'dynamic_slider',
		component: InputRangeDynamic,
	},
	{
		id: 'editor',
		component: InputEditor,
		dynamic: {
			type: 'TEXT',
		},
	},
	{
		id: 'media',
		component: InputMedia,
	},
	{
		id: 'file',
		component: InputFile,
	},
	{
		id: 'image',
		component: InputImage,
	},
	{
		id: 'number',
		component: InputNumber,
	},
	{
		id: 'number_unit',
		component: InputNumberUnit,
	},
	{
		id: 'code',
		component: InputCode,
	},
	{
		id: 'custom_selector',
		component: InputCustomSelector,
	},
	{
		id: 'colorpicker',
		component: InputColorPicker,
		dynamic: {
			type: 'TYPE_HIDDEN',
			custom_dynamic: true,
		},
	},
	{
		id: 'checkbox',
		component: InputCheckbox,
	},
	{
		id: 'radio_image',
		component: InputRadioImage,
	},
	{
		id: 'checkbox_group',
		component: InputCheckboxGroup,
	},
	{
		id: 'checkbox_switch',
		component: InputCheckboxSwitch,
	},
	{
		id: 'text_align',
		component: InputTextAlign,
	},
	{
		id: 'borders',
		component: InputBorderTabs,
	},
	{
		id: 'shadow',
		component: InputTextShadow,
	},
	{
		id: 'video',
		component: InputBackgroundVideo,
	},
	{
		id: 'date_input',
		component: InputDatePicker,
	},
	{
		id: 'shape_dividers',
		component: InputShapeDividers,
	},
	{
		id: 'shape_component',
		component: ShapeDividerComponent,
	},
	{
		id: 'spacing',
		component: InputSpacing,
	},
	{
		id: 'repeater',
		component: InputRepeater,
	},
	{
		id: 'upgrade_to_pro',
		component: UpgradeToPro,
	},
	{
		id: 'dimensions',
		component: InputDimensions,
	},
	{
		id: 'html',
		component: InputHTML,
	},
];

export const useOptions = () => {
	const { applyFilters } = window.zb.hooks;

	/**
	 * Get Option Component
	 *
	 * Will return the requested option component tag
	 *
	 * @param schema The option schema
	 */
	const getOption = (schema: Record<string, any>, model = null, formModel = {}) => {
		let optionConfig = options.find(option => option.id === schema.type);
		optionConfig = applyFilters('zionbuilder/getOptionConfig', optionConfig, schema, model, formModel);

		if (!optionConfig) {
			// eslint-disable-next-line
			console.warn(
				`Option type ${schema.type} not found. Please register the option type using ZionBuilderApi.options.registerOption!`,
			);
			return null;
		}

		return optionConfig;
	};

	/**
	 * Get Option Component
	 *
	 * Will return the requested option component tag
	 *
	 * @param  optionId The option type id for which we need to return the component
	 */
	const getOptionComponent = (schema: Record<string, any>, model = null, formModel = {}) => {
		const optionConfig = getOption(schema.type);
		return applyFilters('zionbuilder/getOption', optionConfig?.component, schema, model, formModel);
	};

	/**
	 * Register Option
	 *
	 * Will register a new dynamic option
	 *
	 * @param {Object} optionConfig Object config
	 */
	const registerOption = (optionConfig: Option) => {
		// Check if the config contains the option type id
		if (!Object.prototype.hasOwnProperty.call(optionConfig, 'id')) {
			// eslint-disable-next-line
			console.warn('You need to specify the option type id.', optionConfig);
		}

		// Check if the option contains the component needed to render the option type
		if (!Object.prototype.hasOwnProperty.call(optionConfig, 'component')) {
			// eslint-disable-next-line
			console.warn('You need to specify the option type id.', optionConfig);
		}

		options.push(optionConfig);
	};

	return {
		registerOption,
		getOptionComponent,
		getOption,
	};
};
