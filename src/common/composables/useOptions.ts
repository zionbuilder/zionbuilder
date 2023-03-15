// Utils
import { type Component } from 'vue';

import { BaseInput } from '../components/BaseInput';
import { InputDatePicker } from '../components/InputDatePicker';
import { InputShapeDividers } from '../components/InputShapeDividers';
import { ShapeDividerComponent } from '../components/InputShapeDividers';
import { InputColorPicker } from '../components/InputColorPicker';
import { InputCustomSelector } from '../components/InputCustomSelector';
import { InputSelect } from '../components/InputSelect';
import { InputRadioImage } from '../components/InputRadioImage';
import { InputRange } from '../components/InputRange';
import { InputIcon } from '../components/InputIcon';
import { InputRangeDynamic } from '../components/InputRange';
import { InputEditor } from '../components/InputEditor';
import { InputMedia } from '../components/InputMedia';
import { InputFile } from '../components/InputFile';
import { InputImage } from '../components/InputImage';
import { InputTextShadow } from '../components/InputTextShadow';
import { InputCode } from '../components/InputCode';
import { InputNumber } from '../components/InputNumber';
import { InputNumberUnit } from '../components/InputNumber';
import { InputCheckbox } from '../components/InputCheckbox';
import { InputCheckboxGroup } from '../components/InputCheckbox';
import { InputCheckboxSwitch } from '../components/InputCheckbox';
import { InputTextAlign } from '../components/InputTextAlign';
import { InputBorderTabs } from '../components/InputBorders';
import { InputBackgroundVideo } from '../components/InputBackgroundVideo';
import { InputSpacing } from '../components/InputSpacing';
import { InputRepeater } from '../components/InputRepeater';
import { UpgradeToPro } from '../components/UpgradeToPro';
import { InputDimensions } from '../components/InputDimensions';
import { InputHTML } from '../components/InputHTML';
import { InputLink } from '../components/InputLink';

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
		id: 'editor',
		component: InputEditor,
		dynamic: {
			type: 'TEXT',
		},
	},

	{
		id: 'number',
		component: InputNumber,
		dynamic: {
			type: 'TEXT',
		},
	},

	{
		id: 'colorpicker',
		component: InputColorPicker,
		dynamic: {
			type: 'TYPE_HIDDEN',
		},
	},

	{
		id: 'link',
		component: InputLink,
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
