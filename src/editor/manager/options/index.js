// Utils
import { applyFilters } from '@/utils/filters'
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
} from '@/common/components/forms'

class OptionsManager {
	constructor () {
		this.options = {}

		// Register default dynamic options
		this.registerDefaultOptions()

		this.activeResponsiveOptions = null
	}

	setActiveResponsiveOptions (optionInstance) {
		this.activeResponsiveOptions = optionInstance
	}

	getActiveResponsiveOptions () {
		return this.activeResponsiveOptions
	}

	removeActiveResponsiveOptions () {
		this.activeResponsiveOptions = null
	}

	/**
	 * Register default dynamic options
	 *
	 * Will register all our custom form inputs created
	 * for Zion Builder
	 */
	registerDefaultOptions () {
		// Register global options
		this.registerOption({
			id: 'text',
			component: BaseInput,
			dynamic: {
				type: 'TEXT'
			}
		})

		this.registerOption({
			id: 'textarea',
			component: BaseInput,
			dynamic: {
				type: 'TEXT'
			}
		})

		this.registerOption({
			id: 'select',
			component: InputSelect
		})

		this.registerOption({
			id: 'slider',
			component: InputRange
		})

		this.registerOption({
			id: 'dynamic_slider',
			component: InputRangeDynamic
		})

		this.registerOption({
			id: 'editor',
			component: Editor,
			dynamic: {
				type: 'TEXT'
			}
		})

		this.registerOption({
			id: 'media',
			component: InputMedia
		})

		this.registerOption({
			id: 'image',
			component: InputImage
		})

		this.registerOption({
			id: 'number',
			component: InputNumber
		})

		this.registerOption({
			id: 'number_unit',
			component: InputNumberUnit
		})

		this.registerOption({
			id: 'code',
			component: CustomCode
		})

		this.registerOption({
			id: 'custom_selector',
			component: CustomSelector
		})

		this.registerOption({
			id: 'radio_image',
			component: RadioImage
		})

		this.registerOption({
			id: 'colorpicker',
			component: ColorPicker,
			dynamic: {
				type: 'TYPE_HIDDEN',
				custom_dynamic: true
			}
		})

		this.registerOption({
			id: 'checkbox',
			component: Checkbox
		})

		this.registerOption({
			id: 'checkbox_group',
			component: CheckboxGroup
		})
		this.registerOption({
			id: 'checkbox_switch',
			component: CheckboxSwitch
		})

		this.registerOption({
			id: 'column_size',
			component: ColumnSize
		})

		this.registerOption({
			id: 'text_align',
			component: InputTextAlign
		})

		this.registerOption({
			id: 'text_transform',
			component: InputTextTransform
		})

		this.registerOption({
			id: 'borders',
			component: InputBorderTabs
		})

		this.registerOption({
			id: 'shadow',
			component: InputTextShadow
		})

		this.registerOption({
			id: 'video',
			component: InputBackgroundVideo
		})

		this.registerOption({
			id: 'icon_library',
			component: IconLibrary
		})

		this.registerOption({
			id: 'date_input',
			component: InputDate
		})

		this.registerOption({
			id: 'shape_dividers',
			component: ShapeDividers
		})
		this.registerOption({
			id: 'shape_component',
			component: ShapeDividerComponent
		})

		// register custom options
		this.registerOption(AccordionMenu)
		this.registerOption(IconLibrary)
		this.registerOption(PseudoGroup)
		this.registerOption(Background)
		this.registerOption(BackgroundColor)
		this.registerOption(BackgroundGradient)
		this.registerOption(Typography)
		this.registerOption(Group)
		this.registerOption(PanelAccordion)
		this.registerOption(ResponsiveGroup)
		this.registerOption(Link)
		this.registerOption(ColumnSize)
		this.registerOption(WPWidget)
		this.registerOption(Repeater)
		this.registerOption(TabGroup)
		this.registerOption(ElementStyles)
		this.registerOption(Gallery)
		this.registerOption(HTMLComponent)
		this.registerOption(UpgradeToPro)
		this.registerOption(Dimensions)

		// options just for builder
		this.registerOption(GlobalClasses)
		this.registerOption(ChildAdder)
	}

	/**
	 * Get Option Component
	 *
	 * Will return the requested option component tag
	 *
	 * @param {Object} schema The option schema
	 */
	getOption (schema, model = null, formModel = {}) {
		let optionConfig = this.options[schema.type]
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
	getOptionComponent (schema, model = null, formModel = {}) {
		const optionConfig = this.getOption(schema.type)
		return applyFilters('zionbuilder/getOption', optionConfig.component, schema, model, formModel)
	}

	/**
	 * Register Option
	 *
	 * Will register a new dynamic option
	 *
	 * @param {Object} optionConfig Object config
	 */
	registerOption (optionConfig) {
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

		this.options[optionConfig.id] = optionConfig
	}
}

export default new OptionsManager()
