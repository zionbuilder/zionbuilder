<template>
	<component
		v-if="isValidInput && optionTypeConfig.config && optionTypeConfig.config.barebone"
		:is="optionTypeConfig.component"
		v-model="optionValue"
		v-bind="compiledSchema"
		:title="schema.title"
	>
		<template v-if="schema.content">
			{{schema.content}}
		</template>
	</component>
	<div
		class="znpb-input-wrapper"
		:class="{
			[`znpb-input-type--${schema.type}`]: true,
			[`${schema.css_class}`]: schema.css_class,
		}"
		:style="computedWrapperStyle"
		v-else-if="isValidInput"
	>

		<div
			class="znpb-form__input-title"
			v-if="schema.title && computedShowTitle"
		>
			<!-- schema.description -->
			{{schema.title}}

			<HasChanges
				v-if="showChanges && hasChanges"
				:content="$translate('discard_changes')"
				@remove-styles="onDeleteOption"
			/>

			<Tooltip
				v-if="schema.description"
				placement="top"
				:modifiers="{
					preventOverflow: {
						boundariesElement: 'viewport'
					}
				}"
				:enterable="false"

				class="znpb-popper-trigger znpb-popper-trigger--circle"
				tooltip-class="znpb-form__input-description-tooltip"
			>
				<div slot="content">
					{{schema.description}}
				</div>

				<BaseIcon icon="question-mark" />

			</Tooltip>

			<!-- pseudo option -->
			<Tooltip
				v-if="schema.pseudo_options"
				:show="showPseudo"
				:close-on-outside-click="true"
				:show-arrows="false"
				append-to="element"
				:trigger="null"
				placement="bottom-end"
				:modifiers="{ offset: {offset: '5px,5px' } }"
				@show="openPseudo"
				@hide="closePseudo"
			>

				<div
					slot="content"
					v-for="(pseudo_selector, index) in schema.pseudo_options"
					:key='index'
					@click="activatePseudo(pseudo_selector)"
					class="znpb-has-pseudo-options__icon-button znpb-options-devices-buttons"
				>
					<BaseIcon :icon="getPseudoIcon(pseudo_selector)" />
				</div>

				<div
					class="znpb-has-pseudo-options__icon-button znpb-options-devices-buttons znpb-has-responsive-options__icon-button--trigger"
					@click="showPseudo=!showPseudo"
				>
					<BaseIcon :icon="getPseudoIcon(activePseudo)" />
				</div>
			</Tooltip>

			<!-- responsive option -->
			<Tooltip
				v-if="schema.responsive_options || schema.show_responsive_buttons"
				:show="showDevices"
				:show-arrows="false"
				append-to="element"
				:trigger="null"
				placement="bottom-end"
				:modifiers="{ offset: {offset: '5px,5px' } }"
				tooltip-class="znpb-has-responsive-options"
				:close-on-outside-click="true"
				@show="openResponsive"
				@hide="closeresponsive"
			>

				<div
					slot="content"
					v-for="(device, index) in getDeviceList"
					:key='index'
					@click="activateDevice(device)"
					class="znpb-options-devices-buttons znpb-has-responsive-options__icon-button"
					ref="dropdown"
				>
					<BaseIcon :icon="device.icon" />
				</div>

				<div
					class="znpb-has-responsive-options__icon-button--trigger"
					@click="showDevices=!showDevices"
				>
					<BaseIcon :icon="getActiveDevice.icon" />
				</div>

			</Tooltip>

			<!-- Injection point -->
			<Injection
				location="input_wrapper/end"
				class="znpb-options-injection--after-title"
			/>

		</div>
		<div class="znpb-input-content">
			<BaseIcon
				v-if="schema.itemIcon"
				:icon="schema.itemIcon"
			/>
			<InputLabel
				v-if="schema.label || schema['label-icon']"
				:label="schema.label"
				:align="schema['label-align']"
				:position="schema['label-position']"
				:title="schema['label-title']"
				:icon="schema['label-icon']"
			>
				<component
					:is="optionTypeConfig.component"
					v-model="optionValue"
					v-bind="compiledSchema"
				>
					<template v-if="schema.content">
						{{schema.content}}
					</template>
				</component>
			</InputLabel>
			<component
				v-else
				:is="optionTypeConfig.component"
				v-model="optionValue"
				v-bind="compiledSchema"
			>
				<template v-if="schema.content">
					{{schema.content}}
				</template>
			</component>
		</div>
	</div>
</template>
<script>
import { mapGetters, mapActions } from 'vuex'
import OptionsManager from '@/editor/manager/options'
import HasChanges from '@/editor/components/elementOptions/HasChanges'
import { InputLabel } from '@/common/components/forms'
import { Injection } from '@/common/components/injections'
import { Tooltip } from '@/common/components/tooltip'

export default {
	name: 'InputWrapper',
	provide () {
		return {
			inputWrapper: this
		}
	},
	inject: {
		panel: {
			default: null
		},
		showChanges: {
			default: true
		},
		optionsForm: {
			default: null
		}
	},
	components: {
		HasChanges,
		InputLabel,
		Injection,
		Tooltip
	},
	props: {
		value: {},
		schema: {
			type: Object,
			required: true
		},
		optionId: {
			type: String,
			required: true
		},
		search_tags: {
			type: Array
		},
		label: {

		},
		deleteValue: {
			type: Function
		},
		getSchemaFromPath: {
			type: Function
		},
		compilePlaceholder: {
			type: Function
		},
		width: {
			type: Number,
			required: false
		}
	},
	data () {
		return {
			activePseudo: null,
			showDevices: false,
			showPseudo: false
		}
	},
	computed: {
		...mapGetters([
			'getActiveElementOptionValue',
			'getActiveDevice',
			'getActivePseudoSelector',
			'getDeviceList',
			'getBackgroundImageOptionSchema',
			'getTypographyOptionSchema'
		]),
		isValidInput () {
			return this.optionTypeConfig
		},
		computedShowTitle () {
			if (typeof this.schema.show_title !== 'undefined') {
				return this.schema.show_title
			}

			return true
		},
		computedWrapperStyle () {
			const styles = {}

			if (this.schema.grow) {
				styles.flex = this.schema.grow
			}

			if (this.schema.width) {
				styles.width = `${this.schema.width}%`
			}

			return styles
		},
		hasChanges () {
			if (this.schema.is_layout) {
				const childOptionsIds = this.getChildOptionsIds(this.schema)

				return childOptionsIds.find((optionId) => {
					return this.savedOptionValue && this.savedOptionValue[optionId]
				})
			} else {
				return typeof this.savedOptionValue !== 'undefined'
			}
		},

		optionTypeConfig () {
			return OptionsManager.getOption(this.schema, this.optionValue, this.optionsForm.options)
		},
		labelAlignment () {
			return this.schema['label-align'] || null
		},
		activeResponsiveMedia () {
			return this.getActiveDevice.id
		},
		savedOptionValue () {
			let value = null

			if (this.compiledSchema.sync) {
				value = this.getActiveElementOptionValue(this.compilePlaceholder(this.compiledSchema.sync))
			} else {
				value = this.value
			}

			return value
		},

		optionValue: {
			get () {
				let value = typeof this.savedOptionValue !== 'undefined' ? this.savedOptionValue : this.schema.default

				// Check to see if we need to save for responsive
				if (this.schema.responsive_options === true) {
					let schemaDefault = this.schema.default
					if (typeof this.schema.default === 'object') {
						schemaDefault = (this.schema.default || {})[this.activeResponsiveMedia]
					}
					value = typeof (value || {})[this.activeResponsiveMedia] !== 'undefined' ? (value || {})[this.activeResponsiveMedia] : schemaDefault
				}

				// check to see if this has pseudo selectors
				if (Array.isArray(this.schema.pseudo_options)) {
					const activePseudo = this.activePseudo || this.schema.pseudo_options[0]
					value = typeof (value || {})[activePseudo] !== 'undefined' ? (value || {})[activePseudo] : undefined
				}

				return value
			},
			set (newValue) {
				let valueToUpdate = newValue
				let newValues = newValue

				// check to see if this has pseudo selectors
				if (Array.isArray(this.schema.pseudo_options)) {
					const activePseudo = this.activePseudo || this.schema.pseudo_options[0]
					let oldValues = this.value

					// Check to see if this also a responsive option
					if (this.compiledSchema.responsive_options === true) {
						oldValues = typeof (this.value || {})[this.activeResponsiveMedia] !== 'undefined' ? (this.value || {})[this.activeResponsiveMedia] : undefined
						newValues = {
							...oldValues,
							[activePseudo]: newValue
						}
					} else {
						valueToUpdate = {
							...this.value,
							[activePseudo]: newValues
						}
					}
				}

				// Check to see if we need to save for responsive
				if (this.compiledSchema.responsive_options === true) {
					valueToUpdate = {
						...this.value,
						[this.activeResponsiveMedia]: newValues
					}
				}

				// Check if the option is synced
				if (this.compiledSchema.sync) {
					// Compile sync value as it may contain placeholders
					const syncValuePath = this.compilePlaceholder(this.compiledSchema.sync)

					// Check to see if we need to delete the option
					if (valueToUpdate === null) {
						this.deleteActiveElementValue({
							path: syncValuePath
						})
					} else {
						this.updateActiveElementValue({
							path: syncValuePath,
							newValue: valueToUpdate
						})
					}

					if (this.panel) {
						this.panel.addToLocalHistory()
					}
				} else {
					if (valueToUpdate === null) {
						this.onDeleteOption()
					} else {
						const optionId = this.schema.is_layout ? false : this.optionId
						this.$emit('input', [optionId, valueToUpdate])
					}
				}

				// Check to see if we need to refresh the iframe
				if (this.schema.on_change) {
					if (this.schema.on_change === 'refresh_iframe') {
						window.ZionBuilderApi.trigger('refreshIframe')
					} else if (this.schema.on_change.condition.value[0] !== newValue) {
						// Check if we need to clear path option
						this.deleteActiveElementValue({
							path: this.schema.on_change.option_path
						})
					}
				}
			}
		},
		/**
		 * Compiled Schema
		 *
		 * WIll search for predefined constants and replace them with correct information
		 * from the currently edited element
		 */
		compiledSchema () {
			// Remove unnecesarry data from schema so we don't overpopulate DOM with unnecessary attributes
			const {
				description,
				type,
				is_layout: isLayout,
				title,
				search_tags: searchTags,
				id,
				css_class: cssClass,
				...schema
			} = this.schema

			return schema
		}
	},

	methods: {
		...mapActions([
			'updateActiveElementValue',
			'setActiveDevice',
			'deleteActiveElementValue'
		]),

		getChildOptionsIds (schema, includeSchemaId = true) {
			let ids = []

			// Special options
			if (schema.type === 'background') {
				const backgroundSchema = this.getBackgroundImageOptionSchema
				Object.keys(backgroundSchema).forEach(optionId => {
					const childIds = this.getChildOptionsIds(backgroundSchema[optionId])

					if (childIds) {
						ids = [
							...ids,
							...childIds,
							'background-color',
							'background-gradient',
							'background-video',
							'background-image'
						]
					}
				})
			} else if (schema.type === 'dimensions' && typeof schema.dimensions === 'object') {
				schema.dimensions.forEach(item => {
					ids.push(item.id)
				})
			} else if (schema.type === 'typography') {
				const typographySchema = this.getTypographyOptionSchema
				Object.keys(typographySchema).forEach(optionId => {
					const childIds = this.getChildOptionsIds(typographySchema[optionId])

					if (childIds) {
						ids = [
							...ids,
							...childIds
						]
					}
				})
			} else if (schema.type === 'responsive_group') {
				ids.push(this.activeResponsiveMedia)
			} else if (schema.type === 'pseudo_group') {
				ids.push(this.activePseudo)
			}

			if (schema.is_layout && schema.child_options) {
				Object.keys(schema.child_options).forEach(optionId => {
					const childIds = this.getChildOptionsIds(schema.child_options[optionId])

					if (childIds) {
						ids = [...ids, ...childIds]
					}
				})
			} else if (includeSchemaId) {
				ids.push(schema.id)
			}

			return ids
		},

		openResponsive () {
			this.showDevices = true
		},
		closeresponsive () {
			this.showDevices = false
		},
		closePseudo () {
			this.showPseudo = false
		},
		openPseudo () {
			this.showPseudo = true
		},
		activateDevice (device) {
			this.setActiveDevice(device.id)
			setTimeout(() => {
				this.showDevices = false
			}, 50)
		},
		activatePseudo (selector) {
			this.activePseudo = selector

			setTimeout(() => {
				this.showPseudo = false
			}, 50)
		},
		getPseudoIcon (pseudo) {
			return pseudo === 'hover' ? 'hover-state' : 'default-state'
		},

		/**
		 * On delete option
		 *
		 * Delete the value
		 */
		onDeleteOption (optionId) {
			if (this.schema.sync) {
				let fullOptionIds = []
				const childOptionsIds = this.getChildOptionsIds(this.schema, false)
				const compiledSync = this.compilePlaceholder(this.schema.sync)

				// Check if this has child options that needs to be deleted
				if (childOptionsIds.length > 0) {
					childOptionsIds.forEach((id) => {
						fullOptionIds.push(`${compiledSync}.${id}`)
					})
				} else {
					fullOptionIds.push(compiledSync)
				}

				this.$parent.deleteValues(fullOptionIds)
			} else {
				if (this.schema.is_layout) {
					const childOptionsIds = this.getChildOptionsIds(this.schema)
					this.$parent.deleteValues(childOptionsIds)
				} else {
					optionId = optionId || this.optionId
					this.deleteValue(optionId)
				}
			}
		},
		getNestedOptionsIds (schemas) {
			let ids = []

			Object.keys(schemas).forEach((optionId) => {
				ids.push(optionId)
			})

			return ids
		}
	}
}
</script>
<style lang="scss">
.znpb-input-content {
	flex-grow: 1;
}
.znpb-input-wrapper {
	width: 100%;
	padding: 0 5px 20px;

	.znpb-form__input-title {
		position: relative;
		display: flex;
		align-items: center;
		margin-bottom: 10px;
		color: darken($font-color, 15%);
		font-family: $font-stack;
		font-size: 13px;
		font-weight: 500;
		line-height: 14px;

		.znpb-popper-trigger--circle {
			justify-content: center;
			flex-shrink: 0;
			margin-left: 5px;
		}

		.znpb-editor-icon-wrapper {
			color: $surface-headings-color;
		}
	}
}

.znpb-form__input-description-tooltip {
	max-width: 200px;
	padding: 5px 10px;
	line-height: 1.7;
	text-align: center;
}

.znpb-has-pseudo-options {
	&__icon-button {
		padding: 2px;

		& > .znpb-editor-icon-wrapper {
			font-size: 22px;
		}
	}
}

.znpb-options-devices-buttons {
	cursor: pointer;

	&:last-child {
		margin-bottom: 0;
	}

	&:hover, &:active {
		background-color: $surface-variant;

		& > .znpb-editor-icon-wrapper {
			color: $surface-active-color;
		}
	}
}

.znpb-has-responsive-options {
	padding: 8px 0;

	&__icon-button {
		padding: 8px 16px;

		&--trigger {
			display: flex;
			margin-bottom: 0;
			margin-left: 7px;
			cursor: pointer;
		}
	}
}

.znpb-options-injection--after-title {
	flex-direction: column;
	flex-grow: 1;
}
</style>
