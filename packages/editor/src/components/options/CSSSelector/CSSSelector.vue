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
						<div class="znpb-option-cssSelectorTitle">{{title}}</div>
						<div class="znpb-option-cssSelector">{{selector}}</div>
					</div>
				</template>

				<template #actions>
					<AddChildActions
						v-if="allow_childs"
						:child-selectors="childSelectors"
						@add-child="onChildAdded"
						@toggle-view-childs="showChilds = !showChilds"
					/>

					<ChangesBullet
						:content="$translate('discard_changes')"
						v-if="show_changes && hasChanges"
						@remove-styles="resetChanges"
					/>

					<Icon
						icon="delete"
						v-if="allow_delete"
						@click.stop="deleteItem"
					/>
				</template>

				<OptionsForm
					:schema="schema"
					v-model="value"
					class="znpb-option-cssSelectorForm"
				/>

			</AccordionMenu>

		</div>
		<div v-if="showChilds && childSelectors.length > 0">
			<CSSSelector
				class="znpb-option-cssChildSelectorStyles"
				v-for="(childSelector, index) in childSelectors"
				:key="index"
				:modelValue="childSelector"
				@update:modelValue="onChildUpdate(childSelector, $event)"
				:is-child="true"
				:allow_class_assignments="false"
				:show_breadcrumbs="show_breadcrumbs"
			/>
		</div>
	</div>
</template>

<script>
import { computed, defineAsyncComponent, ref } from 'vue'

// Components
import AddChildActions from './AddChildActions.vue'
import PseudoSelector from './PseudoSelector.vue'

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
		}
	},
	setup (props, { emit }) {
		const showChilds = ref(false)

		const title = computed(() => {
			return props.name || props.modelValue.title || props.modelValue.id || props.selector || 'New item'
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
				console.log({ newValue });
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
			return {
				styles: {
					type: 'element_styles',
					id: 'styles',
					is_layout: true,
					selector: selector.value,
					title: title.value,
					allow_class_assignments: props.allow_class_assignments
				}
			}
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
			const childIndex = childSelectors.value.indexOf(child)
			if (newValue === null) {
				childSelectors.value.splice(childIndex, 1)
			} else {
				childSelectors.value.splice(childIndex, 1, newValue)
			}
		}

		function deleteItem () {
			emit('update:modelValue', null)
		}

		function resetChanges () {
			delete value.value.styles
		}

		return {
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
			resetChanges
		}
	}
}
</script>

<style lang="scss">
.znpb-option-cssSelectorWrapper {
	display: flex;
	align-items: flex-start;
}
.znpb-option-cssSelectorForm .znpb-options-form-wrapper {
	padding: 0;
}

.znpb-option-cssSelector {
	font-size: 9px;
	font-weight: 300;
	text-transform: none;
	opacity: .5;
}

.znpb-option-cssSelectorTitle {
	margin-bottom: 4px;
}

.znpb-option-cssSelectoritem--child {
	margin-left: 10px;
}

.znpb-option-cssChildSelectorPseudoSelector {
	display: flex;
	align-items: center;
	flex: 1 0 auto;
	padding: 14px 10px;
	margin: 0 5px 5px;
	color: #464646;
	font-size: 11px;
	line-height: 1;
	background: #f1f1f1;
	border-radius: 3px;
	cursor: pointer;

	&:hover {
		opacity: .8;
	}
}

/* Child styles */
.znpb-option-cssChildSelectorStyles {
	flex-grow: 1;

	& .znpb-option-cssSelectorAccordion > .znpb-horizontal-accordion__header {
		padding: 8px 10px;
	}

	.znpb-option-cssSelectorTitle {
		font-size: 11px;
	}
}

.znpb-option-cssSelectorAccordion {
	flex: 1 1 auto;
}
</style>