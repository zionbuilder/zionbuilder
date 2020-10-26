<template>
	<div class="znpb-options-form-wrapper">
		<InputWrapper
			v-for="(optionConfig, optionId) in optionsSchema"
			:key="optionId"
			:schema="optionConfig"
			:option-id="optionId"
			:modelValue="optionConfig.is_layout ? modelValue : modelValue[optionId]"
			:delete-value="deleteValue"
			:get-schema-from-path="getOptionSchemaFromPath"
			:compile-placeholder="compilePlaceholder"
			@update:modelValue="setValue(...$event)"
			@change="onOptionChange"
		/>

	</div>
</template>

<script>
import InputWrapper from './InputWrapper.vue'
import { mapGetters } from 'vuex'
import { updateOptionValue, getOptionValue } from '@zionbuilder/utils'
import { useResponsiveDevices } from '@data'

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
		modelValue: {},
		schema: {
			type: Object,
			required: true
		},

		showChanges: {
			required: false,
			default: true
		}
	},
	setup() {
		const { activeResponsiveDeviceInfo } = useResponsiveDevices()

		return {
			activeResponsiveDeviceInfo
		}
	},
	computed: {
		...mapGetters([
			'getActiveElementOptionValue',
			'getActivePseudoSelector',
			'getFontListForOption'
		]),
		optionsSchema () {
			const schema = {}

			Object.keys(this.schema).forEach((optionId) => {
				const optionConfig = this.getProperSchema(this.schema[optionId])
				const { dependency } = optionConfig

				if (!dependency) {
					schema[optionId] = optionConfig
					return
				}

				let conditionsMet = true

				// Check the option dependency
				dependency.forEach(element => {
					const { option, value, type, option_path: optionPath } = element
					let optionSchema
					let savedValue

					// Check to see if we need to get the schema from the path
					if (optionPath) {
						optionSchema = this.getOptionSchemaFromPath(optionPath)
					} else {
						optionSchema = this.getOptionConfigFromId(option)
					}

					// Check to see if we need to get the schema from the path
					if (optionPath) {
						const defaultValue = optionSchema ? optionSchema.default : false
						savedValue = this.getActiveElementOptionValue(optionPath, defaultValue)
					} else {
						// Get the saved value from option schema
						savedValue = typeof this.modelValue[option] !== 'undefined' ? this.modelValue[option] : optionSchema.default
						if (optionSchema.sync) {
							// Check to see if the option is actually a sync option
							const syncValue = this.compilePlaceholder(optionSchema.sync)
							savedValue = this.getActiveElementOptionValue(syncValue, savedValue)
						}
					}

					const validationType = type || 'includes'

					if (conditionsMet && validationType === 'includes' && value.includes(savedValue)) {
						conditionsMet = true
					} else if (conditionsMet && validationType === 'not_in' && !value.includes(savedValue)) {
						conditionsMet = true
					} else {
						conditionsMet = false
					}
				})

				if (conditionsMet) {
					schema[optionId] = optionConfig
				}
			})

			return schema
		}
	},
	components: {
		InputWrapper
	},
	methods: {
		getValueByPath (path, defaultValue = null) {
			return getOptionValue(this.modelValue, path, defaultValue)
		},
		updateValueByPath (path, newValue) {
			const updatedValues = updateOptionValue(this.modelValue, path, newValue)

			this.$emit('update:modelValue', updatedValues)
		},
		setValue (optionId, newValue) {
			let newValueToSend

			// If this is a normal option
			if (optionId) {
				// Check to see if we need to delete the value
				// || (typeof newValue === 'object' && Object.keys(newValue).length === 0) --- there is a problem with default value setting
				if (newValue === null) {
					const clonedValue = { ...this.modelValue }
					delete clonedValue[optionId]

					if (Object.keys(clonedValue).length === 0) {
						this.$emit('update:modelValue', null)
					} else {
						this.$emit('update:modelValue', clonedValue)
					}
				} else {
					this.$emit('update:modelValue', {
						...this.modelValue,
						[optionId]: newValue
					})
				}
			} else {
				// Check to see if the value was actually deleted
				if (newValue === null || (Object.keys(newValue).length === 0)) {
					this.$emit('update:modelValue', null)
				} else {
					let clonedValue = { ...this.modelValue }
					Object.keys(clonedValue).reduce((acc, key, index) => {
						if (typeof newValue[key] === 'undefined') {
							delete acc[key]
						}

						return acc
					}, clonedValue)

					this.$emit('update:modelValue', {
						...clonedValue,
						...newValue
					})
				}
			}
		},
		deleteValue (path) {
			const paths = path.split('.')
			let newValues = { ...this.modelValue }

			paths.reduce((acc, key, index) => {
				if (index === paths.length - 1) {
					delete acc[key]
					return true
				}

				acc[key] = acc[key] ? { ...acc[key] } : {}
				return acc[key]
			}, newValues)

			this.$emit('update:modelValue', newValues)
		},
		deleteValues (allPaths) {
			let newValues = { ...this.modelValue }
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

			this.$emit('update:modelValue', newValues)
		},
		onDeleteOptions (optionIds) {
			this.deleteValues(optionIds)
		},
		getValue (optionSchema) {
			if (optionSchema.is_layout) {
				return this.modelValue
			} else {
				return this.modelValue[optionSchema.id]
			}
		},
		getOptionConfigFromId (optionId) {
			// Check to see if the option id is a direct child
			if (this.schema[optionId] && !this.schema[optionId].is_layout) {
				return this.schema[optionId]
			} else {
				return this.findOptionConfig(this.schema, optionId)
			}
		},
		findOptionConfig (schema, searchId) {
			let optionConfig

			for (let [optionId, optionConfig] of Object.entries(schema)) {
				if (optionConfig.is_layout && optionConfig.child_options) {
					optionConfig = this.findOptionConfig(optionConfig.child_options, searchId)
				}

				if (optionConfig && optionConfig.id === searchId) {
					return optionConfig
				}
			}

			return optionConfig
		},
		getOptionSchemaFromPath (optionPath) {
			if (!this.elementInfo.optionsSchema) {
				return false
			}

			const pathArray = optionPath.split('.')
			return pathArray.reduce((acc, path, index) => {
				if (acc[path]) {
					return acc[path]
				} else {
					return false
				}
			}, this.elementInfo.optionsSchema)
		},
		onOptionChange (changed) {
			this.$emit('change', changed)
		},

		getProperSchema (schema) {
			// Set data sources
			if (typeof schema.data_source !== 'undefined') {
				if (schema.data_source === 'fonts') {
					schema.options = this.getFontListForOption
					delete schema.data_source
				}
			}

			// Special cases for inputs
			if (schema.type === 'textarea') {
				schema.type = 'textarea'
			}

			// Don't proceed if we have no information about the current edited element
			if (!this.elementInfo) {
				return schema
			}

			schema = this.compilePlaceholders(schema)

			return schema
		},

		isIterable (schema) {
			return Array.isArray(schema) || (schema === Object(schema) && typeof schema !== 'function')
		},

		compilePlaceholders (schema) {
			// Don't proceed if the object is not iterable
			if (!this.isIterable(schema)) {
				return this.compilePlaceholder(schema)
			} else {
				for (const prop in schema) {
					// Don't process sync values
					if (prop !== 'sync') {
						if (schema.hasOwnProperty(prop)) {
							schema[prop] = this.compilePlaceholders(schema[prop])
						}
					}
				}
			}

			return schema
		},

		compilePlaceholder (value) {
			if (typeof value !== 'string') {
				return value
			}

			const replacements = [
				{
					search: /%%ELEMENT_TYPE%%/g,
					replacement: this.replaceElementType
				},
				{
					search: /%%ELEMENT_UID%%/g,
					replacement: this.replaceElementUid
				},
				{
					search: /%%RESPONSIVE_DEVICE%%/g,
					replacement: this.replaceResponsiveDevice
				},
				{
					search: /%%PSEUDO_SELECTOR%%/g,
					replacement: this.replacePseudoSelector
				}
			]

			replacements.forEach((replacementConfig) => {
				value = value.replace(replacementConfig.search, replacementConfig.replacement)
			})

			return value
		},
		/**
		 * Replace %%ELEMENT_TYPE%% with the element name
		 */
		replaceElementType (match) {
			return this.elementInfo.data.elementTypeConfig.name
		},
		/**
		 * Replace %%ELEMENT_UID%% constant with the element UID
		 */
		replaceElementUid (match) {
			return this.elementInfo.data.uid
		},
		/**
		 * Replace %%RESPONSIVE_DEVICE%% constant with the element UID
		 */
		replaceResponsiveDevice (match) {
			return this.activeResponsiveDeviceInfo.id
		},
		/**
		 * Replace %%PSEUDO_SELECTOR%% constant with the element UID
		 */
		replacePseudoSelector (match) {
			return this.getActivePseudoSelector.id
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
