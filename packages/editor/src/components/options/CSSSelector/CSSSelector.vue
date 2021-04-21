<template>
	<div
		class="znpb-option-cssSelectoritem"
		:class="{'znpb-option-cssSelectoritem--child': isChild}"
	>
		<div class="znpb-option-cssSelectorWrapper">
			<div
				class="znpb-option-cssChildSelectorPseudoSelector"
				v-if="isChild"
			>
				PSEUDO
			</div>

			<AccordionMenu
				:show-trigger-arrow="true"
				:has-breadcrumbs="false"
				:title="title"
				:child_options="schema"
				v-model="value"
			>
				<template #title>
					<!-- <Icon icon="brush" /> -->
					<div>
						<div class="znpb-option-cssSelectorTitle">{{title}}</div>
						<div class="znpb-option-cssSelector">{{selector}}</div>
					</div>
				</template>

				<template #actions>
					<Icon
						icon="delete"
						@click.stop="showChilds = !showChilds"
					/>

					<Icon
						icon="delete"
						v-if="allow_childs"
						@click.stop="addChild"
					/>

					<Icon
						icon="delete"
						v-if="allow_delete"
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
			<ChildSelector
				v-for="(childSelector, index) in childSelectors"
				:key="index"
				:modelValue="childSelector"
			/>
		</div>
	</div>
</template>

<script>
import { computed, defineAsyncComponent, ref } from 'vue'

// Components

export default {
	name: 'CSSSelector',
	components: {
		AccordionMenu: defineAsyncComponent(() => import('../AccordionMenu/AccordionMenu.vue')),
		ChildSelector: defineAsyncComponent(() => import('./ChildSelector.vue'))
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
		}
	},
	setup (props, { emit }) {
		const showChilds = ref(false)

		const title = computed(() => {
			return props.modelValue.title || props.modelValue.id || 'New item'
		})

		const selector = computed(() => {
			return props.selector || `.${props.modelValue.id}`
		})

		const childSelectors = computed({
			get () {
				return props.modelValue.child_styles || []
			},
			set (newValue) {
				value.value = {
					...value.value,
					child_styles: newValue
				}
			}
		})

		const schema = computed(() => {
			return {
				styles: {
					type: 'element_styles',
					id: 'styles',
					is_layout: true,
					selector: selector.value,
					// selector: config.selector.replace('{{ELEMENT}}', this.element.uid),
					title: title.value,
					// allow_class_assignments: typeof config.allow_class_assignments !== 'undefined' ? config.allow_class_assignments : true
				}
			}
		})

		const value = computed({
			get () {
				return props.modelValue
			},
			set (newValue) {
				emit('update:modelValue', newValue)
			}
		})

		function addChild () {
			childSelectors.value = [
				...childSelectors.value,
				{}
			]

			showChilds.value = true
		}

		return {
			showChilds,
			title,
			selector,
			childSelectors,
			addChild,
			schema,
			value
		}
	}
}
</script>

<style>
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
	padding: 14px 10px;
	margin: 0 5px 5px;
	color: #464646;
	font-size: 11px;
	line-height: 1;
	background: #f1f1f1;
	border-radius: 3px;
	cursor: pointer;
}
</style>