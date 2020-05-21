
<script>
import { mapGetters } from 'vuex'
import ElementStyles from './ElementStyles.vue'
import Options from '@/common/Options'
import { getStyles } from '@/utils'

export default {
	functional: true,
	name: 'PageStyles',
	props: {
		cssClasses: {
			type: [Object, Array]
		},
		pageSettingsModel: {
			type: Object
		},
		pageSettingsSchema: {
			type: Object
		}
	},
	render (h, context) {
		const returnVnodes = []

		const createVnode = function (styles) {
			return h(ElementStyles, {
				props: {
					styles
				}
			})
		}

		// PageSettings
		const pageSettingsOptionsInstance = new Options(context.props.pageSettingsSchema, context.props.pageSettingsModel)
		const { customCSS: pageSettingsCustomCSS } = pageSettingsOptionsInstance.parseData()

		returnVnodes.push(createVnode(pageSettingsCustomCSS))

		// Custom css classes
		if (typeof context.props.cssClasses === 'object' && context.props.cssClasses !== null) {
			Object.keys(context.props.cssClasses).forEach(cssClassId => {
				const styleData = context.props.cssClasses[cssClassId]
				const customCSS = getStyles(`.zb .${styleData.id}`, styleData.style)

				returnVnodes.push(createVnode(customCSS))
			})
		}

		return returnVnodes
	}
}
</script>
