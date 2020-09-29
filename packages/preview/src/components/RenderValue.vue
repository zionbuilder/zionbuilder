<template>
	<InlineEditor
		v-if="renderType === 'editor'"
		v-model="optionValue"
		v-bind="$attrs"
	/>

	<span
		v-else-if="renderType === 'dynamic_html'"
		v-html="optionValue"
	></span>

	<ElementIcon
		v-else-if="renderType === 'icon'"
		:iconConfig="optionValue"
	/>

	<img
		v-else-if="renderType === 'image'"
		:src="optionValue"
	/>

	<component
		:is="htmlTag"
		v-else
	>
		{{optionValue}}
	</component>
</template>

<script>
// Utils
import { getOptionValue } from '@zb/utils'
import { mapActions } from 'vuex'

// Components
import InlineEditor from './InlineEditor'

export default {
	name: 'RenderValue',
	inheritAttrs: false,
	inject: {
		elementInfo: {
			from: 'elementInfo',
			default: () => { }
		}
	},
	props: {
		option: {
			type: String,
			required: true
		},
		htmlTag: {
			type: String,
			required: false,
			default: 'span'
		}
	},
	components: {
		InlineEditor
	},
	computed: {
		optionValue: {
			get () {
				const schema = this.getOptionSchemaFromPath
				return getOptionValue(this.elementInfo.options, this.option, schema.default)
			},
			set (newValue) {
				this.updateElementOptionValue({
					elementUid: this.elementInfo.data.uid,
					path: this.option,
					newValue
				})
			}
		},
		elementOptionsSchema () {
			return this.elementInfo.elementModel.options
		},
		optionType () {
			return this.getOptionSchemaFromPath.type
		},
		getOptionSchemaFromPath () {
			const paths = this.option.split('.')
			let currentSchema = this.elementOptionsSchema
			const pathLength = paths.length
			let returnSchema = null

			paths.forEach((path, i) => {
				if (i + 1 === pathLength) {
					returnSchema = currentSchema[path]
				} else if (currentSchema[path]) {
					currentSchema = currentSchema[path]
				} else {
					// eslint-disable-next-line
					console.error(`schema could not be found for ${this.option}`)
				}
			})

			return returnSchema
		},
		isValueDynamic () {
			const paths = this.option.split('.')
			let currentModel = this.elementInfo.options
			const pathLength = paths.length
			let isDynamic = false

			paths.forEach((path, i) => {
				if (i === pathLength - 1) {
					if (typeof currentModel.__dynamic_content__ === 'object') {
						const finalOptionId = paths[paths.length - 1]
						isDynamic = currentModel.__dynamic_content__[finalOptionId]
					}

					// returnSchema = currentModel[path]
				} else if (currentModel[path]) {
					currentModel = currentModel[path]
				} else {
					// eslint-disable-next-line
					console.error(`model could not be found for ${this.option}`)
				}
			})

			return isDynamic
		},
		renderType () {
			if (this.optionType === 'editor' && !this.isValueDynamic) {
				return 'editor'
			} else if (this.isValueDynamic && (this.isValueDynamic.options || {})._enable_raw_html) {
				return 'dynamic_html'
			} else if (this.optionType === 'icon_library') {
				return 'icon'
			} else if (this.optionType === 'image') {
				return 'image'
			} else {
				return 'default'
			}
		}
	},
	methods: {
		...mapActions([
			'updateElementOptionValue'
		])
	}
}
</script>
