<template>
	<div class="znpb-options-form-wrapper">
		<InputWrapper
			v-for="(optionConfig, optionId) in schema"
			:key="optionId"
			:schema="optionConfig"
			:option-id="optionId"
			:value="optionConfig.is_layout ? value : value[optionId]"
			:delete-value="deleteValue"
			@input="setValue(...$event)"
			@change="onOptionChange"
		/>

	</div>
</template>

<script>
import InputWrapper from './InputWrapper'
import { updateOptionValue, getOptionValue } from '@zb/utils'

export default {
	name: 'OptionsForm',
	provide () {
		return {
			showChanges: this.showChanges,
			optionsForm: this
		}
	},
	inject: {
		elementInfo: {
			default: null
		}
	},
	props: {
		value: {},
		schema: {
			type: Object,
			required: true
		},

		showChanges: {
			required: false,
			default: true
		}
	},
	components: {
		InputWrapper
	},
	methods: {
		getValueByPath (path, defaultValue = null) {
			return getOptionValue(this.value, path, defaultValue)
		},
		updateValueByPath (path, newValue) {
			const updatedValues = updateOptionValue(this.value, path, newValue)

			this.$emit('input', updatedValues)
		},
		setValue (optionId, newValue) {
			let newValueToSend

			// If this is a normal option
			if (optionId) {
				// Check to see if we need to delete the value
				// || (typeof newValue === 'object' && Object.keys(newValue).length === 0) --- there is a problem with default value setting
				if (newValue === null) {
					const clonedValue = { ...this.value }
					delete clonedValue[optionId]

					if (Object.keys(clonedValue).length === 0) {
						this.$emit('input', null)
					} else {
						this.$emit('input', clonedValue)
					}
				} else {
					this.$emit('input', {
						...this.value,
						[optionId]: newValue
					})
				}
			} else {
				// Check to see if the value was actually deleted
				if (newValue === null || (Object.keys(newValue).length === 0)) {
					this.$emit('input', null)
				} else {
					let clonedValue = { ...this.value }
					Object.keys(clonedValue).reduce((acc, key, index) => {
						if (typeof newValue[key] === 'undefined') {
							delete acc[key]
						}

						return acc
					}, clonedValue)

					this.$emit('input', {
						...clonedValue,
						...newValue
					})
				}
			}
		},
		deleteValue (path) {
			const paths = path.split('.')
			let newValues = { ...this.value }

			paths.reduce((acc, key, index) => {
				if (index === paths.length - 1) {
					delete acc[key]
					return true
				}

				acc[key] = acc[key] ? { ...acc[key] } : {}
				return acc[key]
			}, newValues)

			this.$emit('input', newValues)
		},
		deleteValues (allPaths) {
			let newValues = { ...this.value }
			allPaths.forEach((path) => {
				const paths = path.split('.')

				paths.reduce((acc, key, index) => {
					if (index === paths.length - 1) {
						delete acc[key]
						return true
					}

					acc[key] = acc[key] ? { ...acc[key] } : {}
					return acc[key]
				}, newValues)
			})

			this.$emit('input', newValues)
		},
		onDeleteOptions (optionIds) {
			this.deleteValues(optionIds)
		},
		getValue (optionSchema) {
			if (optionSchema.is_layout) {
				return this.value
			} else {
				return this.value[optionSchema.id]
			}
		},
		onOptionChange (changed) {
			this.$emit('change', changed)
		}
	}
}
</script>

<style lang="scss">
.znpb-options-form-wrapper {
	display: flex;
	flex-wrap: wrap;
	padding: 20px 15px 0;
}
</style>
