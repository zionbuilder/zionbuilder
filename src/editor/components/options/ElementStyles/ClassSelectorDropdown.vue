<template>
	<div ref="root" class="znpb-class-selector">
		<Tooltip
			trigger="null"
			:show="dropdownState"
			placement="bottom-start"
			tooltip-class="znpb-class-selector__popper"
			:show-arrows="false"
		>
			<template #content>
				<div v-if="!allow_class_assignments">
					{{ __('Class assignments not allowed', 'zionbuilder') }}
				</div>
				<div
					v-else
					ref="dropDownWrapper"
					class="znpb-class-selector-body"
					tabindex="0"
					@keydown.down="onKeyDown"
					@keydown.up="onKeyUp"
					@keydown.enter="onKeyEnter"
					@keydown.esc.stop="dropdownState = false"
				>
					<div class="znpb-search-wrapper">
						<BaseInput
							ref="input"
							:modelValue="keyword"
							:filterable="true"
							:clearable="true"
							:placeholder="__('Enter Class Name', 'zionbuilder')"
							@update:modelValue="handleClassInput"
							@keydown.enter.stop="addNewCssClass"
						></BaseInput>
						<Button type="line" class="znpb-class-selector__add-class-button" @click="addNewCssClass">
							{{ __('Add Class', 'zionbuilder') }}
						</Button>
					</div>

					<template v-if="filteredClasses.length > 0">
						<CssSelector
							v-for="cssClassItem in filteredClasses"
							:key="cssClassItem.selector"
							:class-config="cssClassItem"
							:name="cssClassItem.name"
							:type="cssClassItem.type"
							:is-selected="cssClassItem.selector === activeClass"
							:show-delete="cssClassItem.deletable"
							@remove-class="removeClass"
							@click="selectClass(cssClassItem.selector), (dropdownState = false)"
							@copy-styles="onCopyStyles(cssClassItem)"
							@paste-styles="onPasteStyles(cssClassItem)"
						/>
					</template>
					<div v-if="!errorMessage && filteredClasses.length === 0" class="znpb-class-selector-noClass">
						{{
							__('No class found. Press "Add class" to create a new class and assign it to the element.', 'zionbuilder')
						}}
					</div>
					<div v-if="invalidClass" class="znpb-class-selector-validator">{{ errorMessage }}</div>
				</div>
			</template>

			<CssSelector
				v-bind="activeClassConfig"
				:class-config="activeClassConfig"
				:show-delete="false"
				:show-actions="false"
				class="znpb-class-selector-trigger"
				:show-changes-bullet="showRemoveExtraClasses"
				@click="dropdownState = !dropdownState"
				@remove-extra-classes="onRemoveExtraClasses"
			/>
		</Tooltip>
	</div>
</template>

<script lang="ts" setup>
import { __ } from '@wordpress/i18n';
import { computed, ref, watch, nextTick, onBeforeUnmount } from 'vue';
import CssSelector from './CssSelector.vue';
import { useCSSClassesStore } from '/@/editor/store';

const props = withDefaults(
	defineProps<{
		selector: string;
		modelValue: string[];
		title: string;
		activeClass: string;
		// eslint-disable-next-line vue/prop-name-casing
		allow_class_assignments?: boolean;
		activeModelValue?: Record<string, unknown>;
	}>(),
	{
		allow_class_assignments: true,
		activeModelValue: () => ({}),
	},
);

const emit = defineEmits([
	'update:modelValue',
	'update:activeClass',
	'remove-class',
	'remove-extra-classes',
	'copy-styles',
	'paste-styles',
	'paste-style-model',
]);

const cssClasses = useCSSClassesStore();

// Refs
const root = ref(null);
const inputRef = ref(null);
const dropDownWrapperRef = ref(null);
const dropdownState = ref(false);
const keyword = ref('');
const errorMessage = ref('');
const invalidClass = ref(false);
const focusClassIndex = ref(0);

// Computed
const computedValue = computed({
	get() {
		return props.modelValue;
	},
	set(value) {
		emit('update:modelValue', value);
	},
});

const computedSelectorConfig = computed(() => {
	return {
		type: 'selector',
		selector: props.selector,
		name: props.title,
		deletable: false,
	};
});

const activeClassConfig = computed(() => {
	if (props.activeClass !== props.selector) {
		const className = cssClasses.CSSClasses.find(({ id }) => id === props.activeClass);
		return {
			type: 'class',
			name: className ? className.name : props.activeClass,
		};
	} else {
		return computedSelectorConfig.value;
	}
});

const filteredClasses = computed(() => {
	if (keyword.value.length === 0) {
		const extraClasses: {
			type: 'class' | 'id';
			selector: string;
			name: string;
			deletable: boolean;
		}[] = [];
		computedValue.value.forEach(selector => {
			const className = cssClasses.CSSClasses.find(({ id }) => id === selector);

			if (className) {
				extraClasses.push({
					type: 'class',
					selector,
					name: className ? className.name : selector,
					deletable: true,
				});
			}
		});

		return [computedSelectorConfig.value, ...extraClasses];
	} else {
		return cssClasses.getClassesByFilter(keyword.value).map(selectorConfig => {
			return {
				type: 'class',
				selector: selectorConfig.id,
				name: selectorConfig.name,
			};
		});
	}
});

const showRemoveExtraClasses = computed(() => {
	return props.modelValue && props.modelValue.length > 0;
});

function onCopyStyles(selectorConfig) {
	// If this is a
	if (selectorConfig.type === 'class') {
		// Get the class config
		const stylesConfig = cssClasses.getStylesConfig(selectorConfig.selector);
		cssClasses.copyClassStyles(stylesConfig);
	} else {
		// Get the config from id
		cssClasses.copyClassStyles(props.activeModelValue);
	}
}

function onPasteStyles(selectorConfig) {
	// If this is a
	if (selectorConfig.type === 'class') {
		// Get the class config
		cssClasses.pasteClassStyles(selectorConfig.selector);
	} else {
		// Get the config from id
		emit('paste-style-model');
	}
}

// Watchers
watch(dropdownState, newState => {
	if (newState) {
		document.addEventListener('click', closePanel);

		nextTick(() => {
			if (inputRef.value) {
				// Element not focused on next tick alone
				setTimeout(() => {
					inputRef.value.focus();
				}, 50);
			}
		});

		keyword.value = '';
	} else {
		document.removeEventListener('click', closePanel);
		errorMessage.value = null;
		focusClassIndex.value = 0;
	}
});

onBeforeUnmount(() => {
	document.removeEventListener('click', closePanel);
});

// Methods
function onKeyDown() {
	let nextClass;

	if (filteredClasses.value.length !== 0) {
		inputRef.value.blur();
		dropDownWrapperRef.value.focus();

		if (filteredClasses.value[focusClassIndex.value + 1]) {
			nextClass = filteredClasses.value[focusClassIndex.value + 1].selector;
			selectClass(nextClass);
			focusClassIndex.value += 1;
		}
	}
}

function onKeyUp() {
	let previousClass;

	if (filteredClasses.value.length !== 0) {
		inputRef.value.blur();
		dropDownWrapperRef.value.focus();

		if (filteredClasses.value[focusClassIndex.value - 1]) {
			previousClass = filteredClasses.value[focusClassIndex.value - 1].selector;
			selectClass(previousClass);
			focusClassIndex.value -= 1;
		}
	}
}

function onKeyEnter() {
	dropdownState.value = false;
}

function closePanel(event: MouseEvent) {
	if (event.target === document) {
		dropdownState.value = false;
		return;
	}
	if (
		!root.value.contains(event.target) &&
		event.target.tagName !== 'INPUT' &&
		!event.target.classList.contains('znpb-class-selector__add-class-button')
	) {
		dropdownState.value = false;
	}
}

function onRemoveExtraClasses() {
	computedValue.value = [];
	selectClass(props.selector);
}

function removeClass(selector: string) {
	const classIndex = computedValue.value.indexOf(selector);
	const clonedValue = [...computedValue.value];
	const previousClassIndex = classIndex - 1;
	const previousClassSelector = computedValue.value[previousClassIndex];

	clonedValue.splice(classIndex, 1);

	// Update the value
	computedValue.value = clonedValue;

	// Change active index if this was deleted
	if (selector === props.activeClass) {
		const newSelector = previousClassSelector || props.selector;
		const newSelectorIndex = clonedValue.indexOf(newSelector);

		selectClass(newSelector);

		// Add +1 as the main selector is not inside the selectors array
		focusClassIndex.value = newSelectorIndex !== -1 ? newSelectorIndex + 1 : 0;
	}

	// clear the keyword
	keyword.value = '';
	errorMessage.value = '';
}

function handleClassInput(event) {
	keyword.value = event;

	// Add new class to store
	if (!/-?[_a-zA-Z]+[_a-zA-Z0-9-]*/i.test(keyword.value)) {
		errorMessage.value = 'Invalid class name, classes must not start with numbers and cannot contain spaces';
		invalidClass.value = true;
	} else {
		invalidClass.value = false;
		errorMessage.value = '';
	}

	if (!keyword.value.length) {
		errorMessage.value = '';
		invalidClass.value = false;
	}
}

function addNewCssClass() {
	if (!invalidClass.value && keyword.value.length) {
		dropdownState.value = false;

		// check if the class already exists
		const existingClass = cssClasses.CSSClasses.find(classItem => {
			return classItem.name.toLowerCase() === keyword.value;
		});

		if (!existingClass) {
			cssClasses.addCSSClass({
				id: keyword.value,
				name: keyword.value,
			});
		}

		// Add css class to element options
		selectClass(keyword.value);

		// clear the keyword
		keyword.value = '';
	}
}

function selectClass(selector: string) {
	// Check to see if we need to add the class to the element
	if (selector && selector !== props.selector && computedValue.value.indexOf(selector) === -1) {
		// Make a clone as computed properties with setters are not
		// triggered by array methods
		const clonedValue = [...computedValue.value];
		clonedValue.push(selector);
		computedValue.value = clonedValue;
	}

	emit('update:activeClass', selector);
}
</script>

<style lang="scss">
.znpb-button.znpb-class-selector__add-class-button {
	padding: 12px;
	margin-left: 5px;
	white-space: nowrap;
}

.znpb-class-selector {
	flex: 6;
	margin-right: 10px;
	background: var(--zb-input-bg-color);
	border: 2px solid var(--zb-input-border-color);
	border-radius: 3px;
	.selected-class {
		.znpb-item {
			padding-top: 0;
		}
	}
	.znpb-css-class-selector__item-type {
		display: inline-block;
	}

	&-noClass {
		padding: 0 15px;
		line-height: 20px;
	}
	&-validator {
		color: #fff;
		text-align: center;
		background-color: var(--zb-error-color);
		border-radius: 3px;
		padding: 8px 12px;
	}
}

.znpb-class-selector-body {
	width: 288px;

	&:focus {
		outline: none;
	}

	.znpb-search-wrapper {
		display: flex;
		margin-bottom: 10px;
	}
}
</style>
