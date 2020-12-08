<template>
	<InlineEditor
		v-if="renderType === 'editor'"
		v-model="optionValue"
		v-bind="$attrs"
	/>

	<span
		v-else-if="renderType === 'dynamic_html'"
		v-html="optionValue"
		v-bind="$attrs"
	></span>

	<ElementIcon
		v-else-if="renderType === 'icon'"
		:iconConfig="optionValue"
		v-bind="$attrs"
	/>

	<img
		v-else-if="renderType === 'image'"
		:src="optionValue"
		v-bind="$attrs"
	/>

	<component
		:is="htmlTag"
		v-bind="$attrs"
		v-else
	>
		{{optionValue}}
	</component>
</template>

<script>
// Utils
import { inject, computed } from 'vue'
import { getOptionValue } from '@zb/utils'

export default {
	name: 'RenderValue',
	inheritAttrs: false,

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
	setup (props) {
		const elementInfo = inject('elementInfo')
		const elementOptions = inject('elementOptions')


		const elementOptionsSchema = computed(() => {
			return elementInfo.elementTypeModel.options
		})

		const optionType = computed(() => {
			return getOptionSchemaFromPath.value.type
		})

		const getOptionSchemaFromPath = computed(() => {
			const paths = props.option.split('.')
			let currentSchema = elementOptionsSchema.value
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
		})

		const isValueDynamic = computed(() => {
			const paths = props.option.split('.')
			let currentModel = elementInfo.options
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
					console.error(`model could not be found for ${props.option}`)
				}
			})

			return isDynamic
		})


		const renderType = computed(() => {
			if (optionType.value === 'editor' && !isValueDynamic.value) {
				return 'editor'
			} else if (isValueDynamic.value && (isValueDynamic.value.options || {})._enable_raw_html) {
				return 'dynamic_html'
			} else if (optionType.value === 'icon_library') {
				return 'icon'
			} else if (optionType.value === 'image') {
				return 'image'
			} else {
				return 'default'
			}
		})

		const optionValue = computed({
			get () {
				const schema = getOptionSchemaFromPath.value
				return getOptionValue(elementOptions.value, props.option, schema.default)
			},
			set (newValue) {
				elementInfo.updateOptionValue(props.option, newValue)
			}
		})

		return {
			elementInfo,
			elementOptions,
			renderType,
			optionValue
		}
	}
}
</script>
