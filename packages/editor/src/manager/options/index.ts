// Utils
import { applyFilters } from '@zb/utils'
import AccordionMenu from './AccordionMenu'
import PseudoGroup from './PseudoGroup'
import Background from './Background'
import BackgroundColor from './BackgroundColor'
import BackgroundGradient from './BackgroundGradient'
import Typography from './Typography'
import Group from './Group'
import PanelAccordion from './PanelAccordion'
import ResponsiveGroup from './ResponsiveGroup'
import Link from './Link'
import ColumnSize from './ColumnSize'
import IconLibrary from './IconLibrary'
import WPWidget from './WPWidget'
import Repeater from './Repeater'
import TabGroup from './TabGroup'
import ElementStyles from './ElementStyles'
import Gallery from './Gallery'
import HTMLComponent from './HTML'
import UpgradeToPro from './UpgradeToPro'
// Custom options just for buidler
import GlobalClasses from './GlobalCssClasses'
import ChildAdder from './ChildAdder'
import Dimensions from './Dimensions'

// Forms component
import {
	BaseInput,
	InputDate,
	ShapeDividers,
	ShapeDividerComponent,
	ColorPicker,
	CustomSelector,
	InputSelect,
	RadioImage,
	InputRange,
	InputRangeDynamic,
	Editor,
	InputMedia,
	InputImage,
	InputTextShadow,
	CustomCode,
	InputNumber,
	InputNumberUnit,
	Checkbox,
	CheckboxGroup,
	CheckboxSwitch,
	InputTextAlign,
	InputTextTransform,
	InputBorderTabs,
	InputBackgroundVideo
} from '@zb/components/forms'


export default () => {
	const options = {}
	const activeResponsiveOptions = null

	const setActiveResponsiveOptions = (optionInstance) => {
		activeResponsiveOptions = optionInstance
	}

	const getActiveResponsiveOptions = () => {
		return activeResponsiveOptions
	}

	const removeActiveResponsiveOptions = () => {
		activeResponsiveOptions = null
	}

	/**
	 * Register default dynamic options
	 *
	 * Will register all our custom form inputs created
	 * for Zion Builder
	 */
	const registerDefaultOptions = () => {
		// Register global options
		registerOption({
			id: 'text',
			component: BaseInput,
			dynamic: {
				type: 'TEXT'
			}
		})

		registerOption({
			id: 'textarea',
			component: BaseInput,
			dynamic: {
				type: 'TEXT'
			}
		})

		registerOption({
			id: 'select',
			component: InputSelect
		})

		registerOption({
			id: 'slider',
			component: InputRange
		})

		registerOption({
			id: 'dynamic_slider',
			component: InputRangeDynamic
		})

		registerOption({
			id: 'editor',
			component: Editor,
			dynamic: {
				type: 'TEXT'
			}
		})

		registerOption({
			id: 'media',
			component: InputMedia
		})

		registerOption({
			id: 'image',
			component: InputImage
		})

		registerOption({
			id: 'number',
			component: InputNumber
		})

		registerOption({
			id: 'number_unit',
			component: InputNumberUnit
		})

		registerOption({
			id: 'code',
			component: CustomCode
		})

		registerOption({
			id: 'custom_selector',
			component: CustomSelector
		})

		registerOption({
			id: 'radio_image',
			component: RadioImage
		})

		registerOption({
			id: 'colorpicker',
			component: ColorPicker,
			dynamic: {
				type: 'TYPE_HIDDEN',
				custom_dynamic: true
			}
		})

		registerOption({
			id: 'checkbox',
			component: Checkbox
		})

		registerOption({
			id: 'checkbox_group',
			component: CheckboxGroup
		})
		registerOption({
			id: 'checkbox_switch',
			component: CheckboxSwitch
		})

		registerOption({
			id: 'column_size',
			component: ColumnSize
		})

		registerOption({
			id: 'text_align',
			component: InputTextAlign
		})

		registerOption({
			id: 'text_transform',
			component: InputTextTransform
		})

		registerOption({
			id: 'borders',
			component: InputBorderTabs
		})

		registerOption({
			id: 'shadow',
			component: InputTextShadow
		})

		registerOption({
			id: 'video',
			component: InputBackgroundVideo
		})

		registerOption({
			id: 'icon_library',
			component: IconLibrary
		})

		registerOption({
			id: 'date_input',
			component: InputDate
		})

		registerOption({
			id: 'shape_dividers',
			component: ShapeDividers
		})
		registerOption({
			id: 'shape_component',
			component: ShapeDividerComponent
		})

		// register custom options
		registerOption(AccordionMenu)
		registerOption(IconLibrary)
		registerOption(PseudoGroup)
		registerOption(Background)
		registerOption(BackgroundColor)
		registerOption(BackgroundGradient)
		registerOption(Typography)
		registerOption(Group)
		registerOption(PanelAccordion)
		registerOption(ResponsiveGroup)
		registerOption(Link)
		registerOption(ColumnSize)
		registerOption(WPWidget)
		registerOption(Repeater)
		registerOption(TabGroup)
		registerOption(ElementStyles)
		registerOption(Gallery)
		registerOption(HTMLComponent)
		registerOption(UpgradeToPro)
		registerOption(Dimensions)

		// options just for builder
		registerOption(GlobalClasses)
		registerOption(ChildAdder)
	}

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
			return null
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

	// Init
	registerDefaultOptions()

	return {
		registerOption,
		getOptionComponent,
		getOption,
		removeActiveResponsiveOptions,
		getActiveResponsiveOptions,
		setActiveResponsiveOptions
	}
}