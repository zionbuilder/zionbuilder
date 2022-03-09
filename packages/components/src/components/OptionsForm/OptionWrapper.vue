<template>
	<component
		v-if="isValidInput && ( schema.barebone || optionTypeConfig.config && optionTypeConfig.config.barebone )"
		:is="optionTypeConfig.component"
		v-model="optionValue"
		v-bind="compiledSchema"
		:title="schema.title"
		@discard-changes="onDeleteOption"
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
			[`znpb-forms-input-wrapper--${schema.layout}`]: schema.layout
		}"
		:style="computedWrapperStyle"
		v-else-if="isValidInput"
	>

		<div
			class="znpb-form__input-title"
			v-if="schema.title && computedShowTitle"
		>
			<span v-html="schema.title"></span>

			<ChangesBullet
				v-if="showChanges && hasChanges"
				:content="$translate('discard_changes')"
				@remove-styles="onDeleteOption"
			/>

			<Icon
				icon="question-mark"
				v-if="schema.description"
				v-znpb-tooltip="schema.description"
				class="znpb-popper-trigger znpb-popper-trigger--circle"
			/>

			<Tooltip
				v-if="schema.pseudo_options"
				:show="showPseudo"
				:close-on-outside-click="true"
				:show-arrows="false"
				append-to="element"
				:trigger="null"
				@show="openPseudo"
				@hide="closePseudo"
			>

				<template #content>
					<div
						v-for="(pseudo_selector, index) in schema.pseudo_options"
						:key='index'
						@click="activatePseudo(pseudo_selector)"
						class="znpb-has-pseudo-options__icon-button znpb-options-devices-buttons"
					>
						<Icon :icon="getPseudoIcon(pseudo_selector)" />
					</div>
				</template>

				<div
					class="znpb-has-pseudo-options__icon-button znpb-options-devices-buttons znpb-has-responsive-options__icon-button--trigger"
					@click="showPseudo=!showPseudo"
				>
					<Icon :icon="getPseudoIcon(activePseudo)" />
				</div>
			</Tooltip>

			<Tooltip
				v-if="schema.responsive_options || schema.show_responsive_buttons"
				:show="showDevices"
				:show-arrows="false"
				append-to="element"
				:trigger="null"
				placement="bottom"
				tooltip-class="znpb-has-responsive-options"
				:close-on-outside-click="true"
				@show="openResponsive"
				@hide="closeresponsive"
			>
				<template #content>
					<div
						v-for="(device, index) in builtInResponsiveDevices"
						:key='index'
						@click="activateDevice(device)"
						class="znpb-options-devices-buttons znpb-has-responsive-options__icon-button"
						ref="dropdown"
					>
						<Icon :icon="device.icon" />
					</div>
				</template>
				<div
					class="znpb-has-responsive-options__icon-button--trigger"
					@click="showDevices=!showDevices"
				>
					<Icon :icon="activeResponsiveDeviceInfo.icon" />
				</div>

			</Tooltip>

			<Injection
				location="input_wrapper/end"
				class="znpb-options-injection--after-title"
			/>

		</div>
		<div class="znpb-input-content">
			<Icon
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
import { provide, inject, readonly, toRef, watch, watchEffect, ref, computed, markRaw } from "vue"
import { trigger } from "@zb/hooks"
import {
	useOptions,
	useOptionsSchemas,
	useResponsiveDevices,
} from "@composables"

// Components
import { Tooltip } from "@zionbuilder/tooltip"
import { ChangesBullet } from "../ChangesBullet"
import { InputLabel } from "../InputLabel"
import { Injection } from "../Injection"
import { get } from 'lodash-es'
export default {
	name: "OptionWrapper",
	provide () {
		return {
			inputWrapper: this,
		}
	},
	inject: {
		showChanges: {
			default: true,
		},
		optionsForm: {
			default: null,
		},
	},
	components: {
		ChangesBullet,
		InputLabel,
		Injection,
		Tooltip
	},
	props: {
		modelValue: {},
		schema: {
			type: Object,
			required: true,
		},
		optionId: {
			type: String,
			required: true,
		},
		search_tags: {
			type: Array,
		},
		label: {},
		getSchemaFromPath: {
			type: Function,
		},
		compilePlaceholder: {
			type: Function,
		},
		width: {
			type: Number,
			required: false,
		},
		allModelValue: {},
	},
	setup (props, { emit }) {
		const { getOption } = useOptions();
		const {
			getValueByPath,
			updateValueByPath,
			deleteValueByPath,
			getTopModelValueByPath,
			updateTopModelValueByPath,
			deleteTopModelValueByPath,
			deleteValues,
			modelValue
		} = inject('OptionsForm')
		const { getSchema } = useOptionsSchemas()
		const {
			activeResponsiveDeviceInfo,
			builtInResponsiveDevices,
			setActiveResponsiveDeviceId,
		} = useResponsiveDevices();
		const activePseudo = ref(null)
		const showDevices = ref(false)
		const showPseudo = ref(false)
		const panel = inject('panel', null)
		const optionTypeConfig = ref(null)

		const localSchema = toRef(props, "schema")
		provide("schema", readonly(localSchema.value))

		// Computed
		const computedWrapperStyle = computed(() => {
			const styles = {}

			if (props.schema.grow) {
				styles.flex = props.schema.grow
			}

			if (props.schema.width) {
				styles.width = `${props.schema.width}%`
			}

			return styles
		})

		const computedShowTitle = computed(() => {
			if (typeof props.schema.show_title !== "undefined") {
				return props.schema.show_title;
			}

			return true;
		})

		const labelAlignment = computed(() => {
			return props.schema["label-align"] || null
		})

		const activeResponsiveMedia = computed(() => {
			return activeResponsiveDeviceInfo.value.id
		})

		const compiledSchema = computed(() => {
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
			} = props.schema

			return {
				...(optionTypeConfig.value.componentProps || {}),
				...schema,
				hasChanges: !!hasChanges.value
			}
		})

		const savedOptionValue = computed(() => {
			return props.schema.sync ? getTopModelValueByPath(props.compilePlaceholder(props.schema.sync)) : props.modelValue;
		})

		const hasChanges = computed(() => {
			if (props.schema.is_layout) {
				const childOptionsIds = getChildOptionsIds(props.schema);

				return childOptionsIds.find((optionId) => {
					let hasDynamicValue = get(props.modelValue, `__dynamic_content__[${optionId}]`)
					return savedOptionValue.value && savedOptionValue.value[optionId] || hasDynamicValue !== undefined
				})
			} else {
				return typeof savedOptionValue.value !== 'undefined' && savedOptionValue.value !== null
			}
		})

		const optionValue = computed({
			get () {
				let value = typeof savedOptionValue.value !== "undefined" ? savedOptionValue.value : props.schema.default

				// Check to see if we need to save for responsive
				if (props.schema.responsive_options === true) {
					let schemaDefault = props.schema.default
					if (typeof props.schema.default === "object") {
						schemaDefault = (props.schema.default || {})[activeResponsiveMedia.value]
					}

					// Check to see if the option is saved as string
					if (value && typeof value !== 'object') {
						value = {
							default: value
						}
					}

					value = typeof (value || {})[activeResponsiveMedia.value] !== "undefined" ? (value || {})[activeResponsiveMedia.value] : schemaDefault;
				}

				// check to see if this has pseudo selectors
				if (Array.isArray(props.schema.pseudo_options)) {
					const activePseudoValue = activePseudo.value || props.schema.pseudo_options[0]
					value = typeof (value || {})[activePseudoValue] !== "undefined" ? (value || {})[activePseudoValue] : undefined;
				}

				return value;
			},
			set (newValue) {
				let valueToUpdate = newValue
				let newValues = newValue


				// check to see if this has pseudo selectors
				if (Array.isArray(props.schema.pseudo_options)) {
					const activePseudo = activePseudo.value || props.schema.pseudo_options[0]
					let oldValues = props.modelValue

					// Check to see if this also a responsive option
					if (props.schema.responsive_options === true) {
						oldValues = typeof (props.modelValue || {})[activeResponsiveMedia.value] !== "undefined" ? (props.modelValue || {})[activeResponsiveMedia.value] : undefined;
						newValues = {
							...oldValues,
							[activePseudo]: newValue,
						}
					} else {
						valueToUpdate = {
							...props.modelValue,
							[activePseudo]: newValues,
						}
					}

				}

				// Check to see if we need to save for responsive
				if (props.schema.responsive_options === true) {
					valueToUpdate = {
						...props.modelValue,
						[activeResponsiveMedia.value]: newValues,
					}
				}

				// Check if the option is synced
				if (props.schema.sync) {
					// Compile sync value as it may contain placeholders
					const syncValuePath = props.compilePlaceholder(props.schema.sync)

					// Check to see if we need to delete the option
					if (valueToUpdate === null) {
						deleteTopModelValueByPath(syncValuePath)
					} else {
						updateTopModelValueByPath(syncValuePath, valueToUpdate)
					}

					if (panel) {
						panel.addToLocalHistory()
					}
				} else {
					if (valueToUpdate === null) {
						onDeleteOption()
					} else {
						const optionId = props.schema.is_layout ? false : props.optionId
						emit("update:modelValue", [optionId, valueToUpdate])
					}
				}

				// Check to see if we need to refresh the iframe
				if (props.schema.on_change) {

					if (props.schema.on_change === "refresh_iframe") {
						trigger("refreshIframe");
					} else {
						window[props.schema.on_change].apply(null, [newValue])
					}
				}
			},
		})

		const isValidInput = computed(() => {
			return optionTypeConfig.value;
		})

		watchEffect(() => {
			optionTypeConfig.value = markRaw(getOption(
				props.schema,
				optionValue.value,
				modelValue.value
			))
		})


		// Methods
		function openResponsive () {
			showDevices.value = true
		}

		function closeresponsive () {
			showDevices.value = false
		}

		function closePseudo () {
			showPseudo.value = false
		}

		function openPseudo () {
			showPseudo.value = true
		}

		function activateDevice (device) {
			setActiveResponsiveDeviceId(device.id);
			setTimeout(() => {
				showDevices.value = false
			}, 50);
		}

		function activatePseudo (selector) {
			activePseudo.value = selector

			setTimeout(() => {
				showPseudo.value = false
			}, 50)
		}

		function getPseudoIcon (pseudo) {
			return pseudo === "hover" ? "hover-state" : "default-state"
		}

		/**
		 * On delete option
		 *
		 * Delete the value
		 */
		function onDeleteOption (optionId) {
			if (props.schema.sync) {
				let fullOptionIds = [];
				const childOptionsIds = getChildOptionsIds(props.schema, false);
				const compiledSync = props.compilePlaceholder(props.schema.sync);

				// Check if this has child options that needs to be deleted
				if (childOptionsIds.length > 0) {
					childOptionsIds.forEach((id) => {
						fullOptionIds.push(`${compiledSync}.${id}`);
					});
				} else {
					fullOptionIds.push(compiledSync);
				}

				deleteValues(fullOptionIds);
				deleteTopModelValueByPath(compiledSync);
			} else {
				if (props.schema.is_layout) {
					const childOptionsIds = getChildOptionsIds(props.schema)
					deleteValues(childOptionsIds)
				} else {
					optionId = optionId || props.optionId

					deleteValueByPath(optionId)
				}
			}
		}

		function getChildOptionsIds (schema, includeSchemaId = true) {
			let ids = []

			// Special options
			if (schema.type === "background") {
				const backgroundSchema = getSchema("backgroundImageSchema");
				Object.keys(backgroundSchema).forEach((optionId) => {
					const childIds = getChildOptionsIds(backgroundSchema[optionId])

					if (childIds) {
						ids = [
							...ids,
							...childIds,
							"background-color",
							"background-gradient",
							"background-video",
							"background-image",
						]
					}
				})
			} else if (
				schema.type === "dimensions" &&
				typeof schema.dimensions === "object"
			) {
				schema.dimensions.forEach((item) => {
					ids.push(item.id);
				});
			} else if (
				schema.type === "spacing"
			) {
				const spacingPositions = [
					'margin-top',
					'margin-right',
					'margin-bottom',
					'margin-left',
					'padding-top',
					'padding-right',
					'padding-bottom',
					'padding-left',
				]

				ids.push(...spacingPositions)
			} else if (schema.type === "typography") {
				const typographySchema = getSchema("typography")
				Object.keys(typographySchema).forEach((optionId) => {
					const childIds = getChildOptionsIds(
						typographySchema[optionId]
					);

					if (childIds) {
						ids = [...ids, ...childIds];
					}
				})
			} else if (schema.type === "responsive_group") {
				ids.push(activeResponsiveMedia.value)
			} else if (schema.type === "pseudo_group") {
				ids.push(activePseudo.value)
			}

			if (schema.is_layout && schema.child_options) {
				Object.keys(schema.child_options).forEach((optionId) => {
					const childIds = getChildOptionsIds(schema.child_options[optionId])

					if (childIds) {
						ids = [...ids, ...childIds]
					}
				})
			} else if (includeSchemaId) {
				ids.push(schema.id)
			}

			return ids;
		}

		return {
			// Refs
			activePseudo,
			showDevices,
			showPseudo,
			// Computed
			computedWrapperStyle,
			computedShowTitle,
			labelAlignment,
			activeResponsiveMedia,
			compiledSchema,
			savedOptionValue,
			hasChanges,
			isValidInput,
			optionTypeConfig,
			optionValue,
			// Methods
			openResponsive,
			closeresponsive,
			closePseudo,
			openPseudo,
			activateDevice,
			activatePseudo,
			getPseudoIcon,
			getChildOptionsIds,
			onDeleteOption,

			// old
			getValueByPath,
			updateValueByPath,
			deleteValueByPath,
			getTopModelValueByPath,
			updateTopModelValueByPath,
			deleteTopModelValueByPath,
			getSchema,
			activeResponsiveDeviceInfo,
			builtInResponsiveDevices,
			setActiveResponsiveDeviceId
		}
	}
};
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
		color: var(--zb-surface-text-hover-color);
		font-family: var(--zb-font-stack);
		font-size: 13px;
		font-weight: 500;
		line-height: 14px;

		.znpb-popper-trigger--circle {
			justify-content: center;
			flex-shrink: 0;
			margin-left: 5px;
		}

		.znpb-editor-icon-wrapper {
			color: var(--zb-surface-icon-color);
		}
	}

	&--valign-end {
		display: flex;
		align-items: flex-end;
	}
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
		background-color: var(--zb-surface-lighter-color);

		& > .znpb-editor-icon-wrapper {
			color: var(--zb-surface-icon-color);
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
.znpb-options-breadcrumbs-path--search {
	display: flex;
	width: 100%;
	margin-bottom: 10px;
}
</style>
