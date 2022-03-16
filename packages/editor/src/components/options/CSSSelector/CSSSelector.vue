<template>
	<div
		class="znpb-option-cssSelectoritem"
		:class="{'znpb-option-cssSelectoritem--child': isChild}"
	>
		<div class="znpb-option-cssSelectorWrapper">
			<PseudoSelector
				v-if="isChild"
				v-model:states="pseudoState"
			/>

			<AccordionMenu
				:show-trigger-arrow="true"
				:has-breadcrumbs="show_breadcrumbs"
				:title="title"
				:child_options="schema"
				v-model="value"
				class="znpb-option-cssSelectorAccordion"
			>
				<template #title>
					<!-- <Icon icon="brush" /> -->
					<div>
						<InlineEdit
							v-model="title"
							class="znpb-option-cssSelectorTitle"
							:class="{
								'znpb-option-cssSelectorTitle--allowRename': allowRename
							}"
							@click="onRenameItemClick"
							:enabled="allowRename"
						/>

						<div
							class="znpb-option-cssSelector"
							:title="selector"
						>{{selector}}</div>
					</div>
				</template>

				<template #actions>
					<AddChildActions
						v-if="allow_childs"
						:child-selectors="childSelectors"
						@add-selector="onChildAdded"
						@toggle-view-childs="showChilds = !showChilds"
					/>

					<ChangesBullet
						:content="$translate('discard_changes')"
						v-if="show_changes && hasChanges"
						@remove-styles="resetChanges"
					/>

					<HiddenMenu :actions="classActions" />

				</template>

				<OptionsForm
					:schema="schema"
					v-model="value"
					class="znpb-option-cssSelectorForm"
				/>

			</AccordionMenu>

		</div>
		<div v-if="showChilds && childSelectors.length > 0">
			<Sortable
				class="znpb-admin-colors__container"
				v-model="childSelectors"
				handle=".znpb-option-cssSelectorAccordion > .znpb-horizontal-accordion__header"
				:drag-delay="0"
				:drag-treshold="10"
				:disabled="false"
				:revert="true"
				axis="vertical"
				:group="uid"
			>
				<CSSSelector
					class="znpb-option-cssChildSelectorStyles"
					v-for="(childSelector, index) in childSelectors"
					:key="childSelector.title + childSelector.selector + index"
					:modelValue="childSelector"
					@update:modelValue="onChildUpdate(childSelector, $event)"
					:is-child="true"
					:allow_class_assignments="false"
					:allow_custom_attributes="false"
					:show_breadcrumbs="show_breadcrumbs"
				/>
			</Sortable>
		</div>
	</div>
</template>

<script>
import { computed, defineAsyncComponent, ref } from 'vue'
import { merge, cloneDeep } from 'lodash-es'
import { applyFilters } from '@zb/hooks'
import { translate } from '@zb/i18n'
import { generateUID } from '@zb/utils'

// Components
import AddChildActions from './AddChildActions.vue'
import PseudoSelector from './PseudoSelector.vue'
import { useCSSClasses } from '@composables'

export default {
	name: 'CSSSelector',
	components: {
		AccordionMenu: defineAsyncComponent(() => import('../AccordionMenu/AccordionMenu.vue')),
		AddChildActions,
		PseudoSelector
	},
	props: {
		modelValue: {
			type: Object,
			default: {}
		},
		allow_delete: {
			type: Boolean,
			default: true
		},
		allow_childs: {
			type: Boolean,
			default: true
		},
		isChild: {
			type: Boolean,
			default: false
		},
		allow_class_assignments: {
			type: Boolean,
			default: true
		},
		allow_custom_attributes: {
			type: Boolean,
			default: true
		},
		selector: {
			type: String,
			required: false
		},
		name: {
			type: String,
			required: false
		},
		show_breadcrumbs: {
			type: Boolean,
			default: false
		},
		show_changes: {
			type: Boolean,
			default: true
		},
		allowRename: {
			type: Boolean,
			default: true
		}
	},
	setup (props, { emit }) {
		const { copiedStyles, copyClassStyles } = useCSSClasses()
		const showChilds = ref(false)
		const uid = generateUID()
		const canShow = ref(false)
		const showClassMenu = ref(false)
		const classMenuIcon = ref(null)

		// Computed
		const classActions = computed(() => {
			return [
				{
					title: translate('copy_element_styles'),
					action: () => {
						copyClassStyles(value.value.styles)
					},
					icon: 'copy'
				},
				{
					title: translate('paste_element_styles'),
					action: () => {
						const { copiedStyles } = useCSSClasses()
						const clonedCopiedStyles = cloneDeep(copiedStyles.value)
						if (!value.value.styles) {
							value.value.styles = clonedCopiedStyles
						} else {
							value.value.styles = merge(value.value.styles, clonedCopiedStyles)
						}
					},
					show: !!copiedStyles.value,
					icon: 'paste'
				},
				{
					title: translate('delete_selector'),
					action: deleteItem,
					icon: 'delete'
				}
			]
		})

		const title = computed({
			get () {
				return props.name || props.modelValue.name || props.modelValue.title || props.modelValue.id || props.selector || 'New item'
			},
			set (newValue) {
				value.value = {
					...value.value,
					name: newValue
				}
			}
		})

		const selector = computed(() => {
			if (props.selector) {
				return props.selector
			} else if (props.modelValue.id) {
				return `.${props.modelValue.id}`
			} else if (props.modelValue.selector) {
				return props.modelValue.selector
			}
		})

		const childSelectors = computed({
			get () {
				return props.modelValue.child_styles || []
			},
			set (newValue) {
				if (null === newValue || newValue.length === 0) {
					delete value.value.child_styles
				} else {
					value.value = {
						...value.value,
						child_styles: newValue
					}
				}

			}
		})

		const pseudoState = computed({
			get () {
				return value.value.states || ['default']
			},
			set (newStateValue) {
				value.value.states = newStateValue
			}
		})

		const schema = computed(() => {
			const schema = {
				styles: {
					type: 'element_styles',
					id: 'styles',
					is_layout: true,
					selector: selector.value,
					title: title.value,
					allow_class_assignments: props.allow_class_assignments
				}
			}

			// attach the attribute options
			if (props.allow_custom_attributes) {
				schema.attributes = applyFilters('zionbuilder/options/attributes', {
					type: 'accordion_menu',
					title: 'custom attributes',
					icon: 'tags-attributes',
					is_layout: true,
					label: {
						type: translate('pro'),
						text: translate('pro')
					},
					show_title: false,
					child_options: {
						upgrade_message: {
							type: 'upgrade_to_pro',
							message_title: translate('meet_custom_attributes'),
							message_description: translate('meet_custom_attributes_desc'),
							info_text: translate('meet_custom_attributes_link')
						}
					}
				})
			}



			return schema
		})

		const hasChanges = computed(() => Object.keys(value.value.styles || {}).length > 0)

		const value = computed({
			get () {
				return props.modelValue
			},
			set (newValue) {
				emit('update:modelValue', newValue)
			}
		})

		function onChildAdded (childData) {
			childSelectors.value = [
				...childSelectors.value,
				childData
			]

			showChilds.value = true
		}

		function onChildUpdate (child, newValue) {
			const value = childSelectors.value.slice()
			const childIndex = childSelectors.value.indexOf(child)

			if (newValue === null) {
				value.splice(childIndex, 1)
			} else {
				value.splice(childIndex, 1, newValue)
			}

			childSelectors.value = value
		}

		function deleteItem () {
			emit('update:modelValue', null)
		}

		function resetChanges () {
			delete value.value.styles
		}

		function onRenameItemClick (event) {
			if (props.allowRename) {
				event.stopPropagation()
			}
		}

		return {
			// Refs
			showClassMenu,
			classMenuIcon,
			canShow,

			// Computed
			classActions,

			onChildAdded,
			showChilds,
			title,
			selector,
			childSelectors,
			deleteItem,
			schema,
			value,
			onChildUpdate,
			pseudoState,
			hasChanges,
			resetChanges,
			uid,

			// Methods
			onRenameItemClick
		}
	}
}
</script>

<style lang="scss">
.znpb-option-cssSelectorWrapper {
	display: flex;
	align-items: center;
	overflow: visible;
}
.znpb-option-cssSelectorForm .znpb-options-form-wrapper {
	padding: 0;
}

.znpb-option-cssSelectorAccordion
	> .znpb-horizontal-accordion__header
	> .znpb-horizontal-accordion__title {
	position: relative;
	// overflow: hidden;
	padding-right: 0;
	padding-bottom: 12px;
	margin-right: 15px;
}

.znpb-option-cssSelector {
	position: absolute;
	bottom: 1px;
	width: 100%;
	font-size: 13px;
	font-weight: 500;
	text-transform: none;
	white-space: nowrap;
	opacity: 0.6;

	&::after {
		content: "";
		position: absolute;
		top: 0;
		right: 0;
		z-index: 1;
		width: 20px;
		height: 100%;
		background: linear-gradient(
			90deg,
			rgba(241, 241, 241, 0) 0%,
			var(--zb-surface-lighter-color) 100%
		);
	}
}

.znpb-horizontal-accordion__header:hover .znpb-option-cssSelector::after {
	background: linear-gradient(
		90deg,
		rgba(241, 241, 241, 0) 0%,
		var(--zb-surface-lightest-color) 100%
	);
}

.znpb-option-cssSelectorTitle {
	padding-right: 20px;
	margin-bottom: 8px;

	&--allowRename {
		cursor: text;
	}

	&:focus, &:focus-visible {
		outline: 0;
	}
}

.znpb-option-cssSelectoritem--child .znpb-option-cssSelectoritem--child {
	padding-left: 61px;
}

.znpb-option-cssChildSelectorPseudoSelector {
	position: relative;
	display: flex;
	justify-content: center;
	align-items: center;
	flex: 1 0 auto;
	width: 56px;
	padding: 5px 8px;
	margin: 0 5px 5px 0;
	color: #fff;
	font-size: 11px;
	font-weight: 500;
	line-height: 1;
	background: #8bc88a;
	border-radius: 2px;
	transition: background 0.2s;
	cursor: pointer;

	&:hover {
		background: darken(#8bc88a, 5%);
	}

	&::before,
	&::after {
		content: "";
		position: absolute;
		z-index: -1;
		background: var(--zb-surface-border-color);
	}

	&::before {
		bottom: 100%;
		left: 50%;
		width: 1px;
		height: 25px;
	}

	&::after {
		top: 50%;
		left: 100%;
		width: 5px;
		height: 1px;
	}
}

.znpb-option-cssSelectoritem--child
	+ .znpb-option-cssSelectoritem--child
	.znpb-option-cssChildSelectorPseudoSelector::before {
	height: 42px;
}

/* Child styles */
.znpb-option-cssChildSelectorStyles {
	flex-grow: 1;

	& .znpb-option-cssSelectorAccordion > .znpb-horizontal-accordion__header {
		padding: 12px;
	}

	&.vuebdnd__source--dragging
		.znpb-option-cssChildSelectorPseudoSelector:before {
		display: none;
	}
}

.znpb-option-cssSelectorAccordion {
	flex: 1 1 auto;
}

.znpb-cssSelectorDialog {
	text-align: center;

	.hg-popper {
		padding: 15px;
	}

	&__text {
		margin-bottom: 10px;
	}

	.znpb-button {
		margin: 0 2px;
	}
}
</style>