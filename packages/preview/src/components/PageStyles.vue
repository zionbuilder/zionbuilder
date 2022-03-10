
<script>
import ElementStyles from './ElementStyles.vue'
import Options from '../Options'
import { getCssFromSelector } from '@zb/editor'
import { h } from 'vue'
import { usePseudoSelectors } from '@zb/components'

export default {
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
	setup (props) {
		return () => {
			const { activePseudoSelector } = usePseudoSelectors()
			const returnVnodes = []

			const createVnode = function (styles) {
				return h(ElementStyles, {
					styles
				})
			}

			// PageSettings
			const pageSettingsOptionsInstance = new Options(props.pageSettingsSchema, props.pageSettingsModel)
			const { customCSS: pageSettingsCustomCSS } = pageSettingsOptionsInstance.parseData()

			returnVnodes.push(createVnode(pageSettingsCustomCSS))

			// Custom css classes
			if (typeof props.cssClasses === 'object' && props.cssClasses !== null) {
				Object.keys(props.cssClasses).forEach(cssClassId => {
					const styleData = props.cssClasses[cssClassId]
					let customCSS = getCssFromSelector([`.zb .${styleData.id}`], styleData)

					// Generate the styles on hover
					if (activePseudoSelector.value && activePseudoSelector.value.id === ':hover') {
						customCSS += getCssFromSelector([`.zb .${styleData.id}`], styleData, {
							forcehoverState: true
						})
					}

					returnVnodes.push(createVnode(customCSS))
				})
			}

			return returnVnodes
		}
	}
}
</script>
