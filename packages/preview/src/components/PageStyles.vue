
<script>
import ElementStyles from './ElementStyles.vue'
import Options from '../Options'
import { getStyles } from '@zb/utils'
import { h } from 'vue'

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
	render (context) {
		const returnVnodes = []

		const createVnode = function (styles) {
			return h(ElementStyles, {
				props: {
					styles
				}
			})
		}

		// PageSettings
		const pageSettingsOptionsInstance = new Options(context.pageSettingsSchema, context.pageSettingsModel)
		const { customCSS: pageSettingsCustomCSS } = pageSettingsOptionsInstance.parseData()

		returnVnodes.push(createVnode(pageSettingsCustomCSS))

		// Custom css classes
		if (typeof context.cssClasses === 'object' && context.cssClasses !== null) {
			Object.keys(context.cssClasses).forEach(cssClassId => {
				const styleData = context.cssClasses[cssClassId]
				const customCSS = getStyles(`.zb .${styleData.id}`, styleData.style)

				returnVnodes.push(createVnode(customCSS))
			})
		}

		return returnVnodes
	}
}
</script>
