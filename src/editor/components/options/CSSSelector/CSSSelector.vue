<template>
	<div class="znpb-option-cssSelectoritem" :class="{ 'znpb-option-cssSelectoritem--child': isChild }">
		<div class="znpb-option-cssSelectorWrapper">
			<PseudoSelector v-if="isChild" v-model:states="pseudoState" />

			<AccordionMenu
				v-model="value"
				:show-trigger-arrow="true"
				:has-breadcrumbs="show_breadcrumbs"
				:title="title"
				:child_options="schema"
				class="znpb-option-cssSelectorAccordion"
			>
				<template #title>
					<!-- <Icon icon="brush" /> -->
					<div @mouseenter="onMouseOver" @mouseleave="onMouseOut">
						<InlineEdit
							v-model="title"
							class="znpb-option-cssSelectorTitle"
							:class="{
								'znpb-option-cssSelectorTitle--allowRename': allowRename,
							}"
							:enabled="allowRename"
							@click="onRenameItemClick"
						/>

						<div class="znpb-option-cssSelector" :title="selector">{{ selector }}</div>
					</div>
				</template>

				<template #actions>
					<AddChildActions
						v-if="allow_childs"
						:child-selectors="childSelectors"
						@add-selector="onChildAdded"
						@toggle-view-children="showChildren = !showChildren"
					/>

					<ChangesBullet
						v-if="show_changes && hasChanges"
						:content="__('Discard changes', 'zionbuilder')"
						@remove-styles="resetChanges"
					/>

					<HiddenMenu :actions="classActions" />
				</template>

				<OptionsForm v-model="value" :schema="schema" class="znpb-option-cssSelectorForm" />
			</AccordionMenu>
		</div>
		<div v-if="showChildren && childSelectors.length > 0">
			<Sortable
				v-model="childSelectors"
				class="znpb-admin-colors__container"
				handle=".znpb-option-cssSelectorAccordion > .znpb-horizontal-accordion__header"
				:drag-delay="0"
				:drag-treshold="10"
				:disabled="false"
				:revert="true"
				axis="vertical"
				:group="uid"
			>
				<CSSSelector
					v-for="(childSelector, index) in childSelectors"
					:key="childSelector.title + childSelector.selector + index"
					class="znpb-option-cssChildSelectorStyles"
					:model-value="childSelector"
					:is-child="true"
					:allow_class_assignments="false"
					:allow_custom_attributes="false"
					:show_breadcrumbs="show_breadcrumbs"
					@update:modelValue="onChildUpdate(childSelector, $event)"
				/>
			</Sortable>
		</div>
	</div>
</template>

<script lang="ts" setup>
import { __ } from '@wordpress/i18n';
import { computed, defineAsyncComponent, onBeforeUnmount, ref } from 'vue';
import { merge, cloneDeep } from 'lodash-es';

// Components
import AddChildActions from './AddChildActions.vue';
import PseudoSelector from './PseudoSelector.vue';
import { useCSSClassesStore } from '/@/editor/store';

// Common API
const { generateUID } = window.zb.utils;
const { applyFilters } = window.zb.hooks;

const props = withDefaults(
	defineProps<{
		modelValue: Record<string, unknown>;
		// eslint-disable-next-line vue/prop-name-casing
		allow_delete?: boolean;
		// eslint-disable-next-line vue/prop-name-casing
		allow_childs?: boolean;
		isChild?: boolean;
		// eslint-disable-next-line vue/prop-name-casing
		allow_class_assignments?: boolean;
		// eslint-disable-next-line vue/prop-name-casing
		allow_custom_attributes?: boolean;
		selector?: string;
		name?: string;
		// eslint-disable-next-line vue/prop-name-casing
		show_breadcrumbs?: boolean;
		// eslint-disable-next-line vue/prop-name-casing
		show_changes?: boolean;
		allowRename?: boolean;
	}>(),
	{
		modelValue: () => ({}),
		allow_delete: true,
		allow_childs: true,
		isChild: false,
		allow_class_assignments: true,
		allow_custom_attributes: true,
		selector: '',
		name: '',
		show_breadcrumbs: false,
		show_changes: true,
		allowRename: true,
	},
);

const emit = defineEmits([
	'update:modelValue',
	'delete',
	'rename',
	'add-selector',
	'update-selector',
	'toggle-view-children',
]);

const AccordionMenu = defineAsyncComponent(() => import('../AccordionMenu/AccordionMenu.vue'));

const cssClassesStore = useCSSClassesStore();
const showChildren = ref(false);
const uid = generateUID();

// Computed
const classActions = computed(() => {
	return [
		{
			title: __('Copy styles', 'zionbuilder'),
			action: () => {
				cssClassesStore.copyClassStyles(value.value.styles);
			},
			icon: 'copy',
		},
		{
			title: __('Paste styles', 'zionbuilder'),
			action: () => {
				const clonedCopiedStyles = cloneDeep(cssClassesStore.copiedStyles);
				if (!value.value.styles) {
					value.value.styles = clonedCopiedStyles;
				} else {
					value.value.styles = merge(value.value.styles, clonedCopiedStyles);
				}
			},
			show: !!cssClassesStore.copiedStyles,
			icon: 'paste',
		},
		{
			title: __('Delete selector', 'zionbuilder'),
			action: deleteItem,
			icon: 'delete',
		},
	];
});

const title = computed({
	get() {
		return (
			props.name ||
			props.modelValue.name ||
			props.modelValue.title ||
			props.modelValue.id ||
			props.selector ||
			'New item'
		);
	},
	set(newValue) {
		value.value = {
			...value.value,
			name: newValue,
		};
	},
});

const selector = computed(() => {
	if (props.selector) {
		return props.selector;
	} else if (props.modelValue.id) {
		return `.${props.modelValue.id}`;
	} else if (props.modelValue.selector) {
		return props.modelValue.selector;
	}
});

function onMouseOver() {
	const iframe = window.document.getElementById('znpb-editor-iframe');

	if (!iframe) {
		return;
	}

	try {
		const domElements = iframe.contentWindow.document.querySelectorAll(selector.value);

		if (domElements.length) {
			domElements.forEach(element => {
				element.style.outline = '2px solid #14ae5c';
			});
		}
	} catch (error) {
		// console.log(error);
	}
}

function onMouseOut() {
	const iframe = window.document.getElementById('znpb-editor-iframe');

	if (!iframe) {
		return;
	}

	try {
		const domElements = iframe.contentWindow.document.querySelectorAll(selector.value);

		if (domElements.length) {
			domElements.forEach(element => {
				element.style.outline = null;
			});
		}
	} catch (error) {
		// console.log(error);
	}
}

// Cleanup before unmount
onBeforeUnmount(() => onMouseOut());

const childSelectors = computed({
	get() {
		return props.modelValue.child_styles || [];
	},
	set(newValue) {
		if (null === newValue || newValue.length === 0) {
			delete value.value.child_styles;
		} else {
			value.value = {
				...value.value,
				child_styles: newValue,
			};
		}
	},
});

const pseudoState = computed({
	get() {
		return value.value.states || ['default'];
	},
	set(newStateValue) {
		value.value.states = newStateValue;
	},
});

const schema = computed(() => {
	const schema = {
		styles: {
			type: 'element_styles',
			id: 'styles',
			is_layout: true,
			selector: selector.value,
			title: title.value,
			allow_class_assignments: props.allow_class_assignments,
		},
	};

	// attach the attribute options
	if (props.allow_custom_attributes) {
		schema.attributes = applyFilters('zionbuilder/options/attributes', {
			type: 'accordion_menu',
			title: 'custom attributes',
			icon: 'tags-attributes',
			is_layout: true,
			label: {
				type: __('pro', 'zionbuilder'),
				text: __('pro', 'zionbuilder'),
			},
			show_title: false,
			child_options: {
				upgrade_message: {
					type: 'upgrade_to_pro',
					message_title: __('Meet custom attributes', 'zionbuilder'),
					message_description: __('Generate custom attributes to every inner parts of the element', 'zionbuilder'),
					info_text: __('Click here to learn more about PRO.', 'zionbuilder'),
				},
			},
		});
	}

	return schema;
});

const hasChanges = computed(
	() => Object.keys(value.value.styles || {}).length > 0 || Object.keys(value.value.attributes || {}).length > 0,
);

const value = computed({
	get() {
		return props.modelValue || {};
	},
	set(newValue) {
		emit('update:modelValue', newValue);
	},
});

function onChildAdded(childData) {
	childSelectors.value = [...childSelectors.value, childData];

	showChildren.value = true;
}

function onChildUpdate(child, newValue) {
	const value = childSelectors.value.slice();
	const childIndex = childSelectors.value.indexOf(child);

	if (newValue === null) {
		value.splice(childIndex, 1);
	} else {
		value.splice(childIndex, 1, newValue);
	}

	childSelectors.value = value;
}

function deleteItem() {
	emit('update:modelValue', null);
}

function resetChanges() {
	emit('update:modelValue', null);
}

function onRenameItemClick(event) {
	if (props.allowRename) {
		event.stopPropagation();
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

.znpb-option-cssSelectorAccordion > .znpb-horizontal-accordion__header > .znpb-horizontal-accordion__title {
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
	overflow: hidden;

	&::after {
		content: '';
		position: absolute;
		top: 0;
		right: 0;
		z-index: 1;
		width: 20px;
		height: 100%;
		background: linear-gradient(90deg, rgba(241, 241, 241, 0) 0%, var(--zb-surface-lighter-color) 100%);
	}
}

.znpb-horizontal-accordion__header:hover .znpb-option-cssSelector::after {
	background: linear-gradient(90deg, rgba(241, 241, 241, 0) 0%, var(--zb-surface-lightest-color) 100%);
}

.znpb-option-cssSelectorTitle {
	width: 100%;
	font-weight: 500;
	padding-right: 20px;
	margin-bottom: 8px;

	&--allowRename {
		cursor: text;
	}

	&:focus,
	&:focus-visible {
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
		content: '';
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

	&.vuebdnd__source--dragging .znpb-option-cssChildSelectorPseudoSelector:before {
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
