<template>
	<div ref="root" class="znpb-class-selector">
		<div>
			<CssSelector
				class="znpb-class-selector-trigger"
				:show-delete="false"
				:show-actions="false"
				:name="name"
				:type="activeGlobalClass ? 'class' : 'id'"
				@click="dropdownState = !dropdownState"
				@copy-styles="onCopyStyles"
				@paste-styles="onPasteStyles"
			/>
		</div>

		<div v-if="dropdownState" ref="dropDownWrapperRef" class="hg-popper znpb-class-selector__popper">
			<div v-if="!allowClassAssignment">
				{{ i18n.__('Class assignments not allowed', 'zionbuilder') }}
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
						ref="inputRef"
						:modelValue="keyword"
						:filterable="true"
						:clearable="true"
						:placeholder="i18n.__('Enter class name', 'zionbuilder')"
						@update:modelValue="handleClassInput($event as string)"
						@keydown.enter.stop="addNewCssClass"
					></BaseInput>
					<Button type="line" class="znpb-class-selector__add-class-button" @click="addNewCssClass">
						{{ i18n.__('Add Class', 'zionbuilder') }}
					</Button>
				</div>

				<template v-if="filteredClasses.length > 0">
					<CssSelector
						v-for="cssClassItem in filteredClasses"
						:key="cssClassItem.name"
						:name="cssClassItem.name"
						:type="cssClassItem.type"
						:show-delete="cssClassItem.deletable"
						:show-copy-paste="cssClassItem.type !== 'static_class'"
						:is-selected="cssClassItem.selected"
						@remove-class="removeClass(cssClassItem)"
						@click="selectClass(cssClassItem), (dropdownState = false)"
						@copy-styles="onCopyStyles(cssClassItem)"
						@paste-styles="onPasteStyles(cssClassItem)"
					/>
				</template>
				<div v-if="errorMessage.length === 0 && filteredClasses.length === 0" class="znpb-class-selector-noClass">
					{{
						i18n.__(
							'No class found. Press "Add class" to create a new class and assign it to the element.',
							'zionbuilder',
						)
					}}
				</div>
				<div v-if="invalidClass" class="znpb-class-selector-validator">{{ errorMessage }}</div>
			</div>
		</div>
	</div>
</template>

<script lang="ts" setup>
import * as i18n from '@wordpress/i18n';
import { computed, ref, watch, nextTick, onBeforeUnmount, Ref } from 'vue';
import CssSelector from './CssSelector.vue';
import { useCSSClassesStore } from '/@/editor/store';
import { type BaseInput } from '@zb/components';

export type SelectorConfig = {
	type: 'class' | 'id' | 'static_class';
	name: string;
	id: string;
	deletable: boolean;
	selected: boolean;
	uid: string;
};

const props = withDefaults(
	defineProps<{
		element: ZionElement;
		activeGlobalClass: string | null;
		name: string;
		allowClassAssignment?: boolean;
		assignedClasses: string[];
		assignedStaticClasses: string[];
		activeStyleElementId: string;
	}>(),
	{
		allowClassAssignment: true,
	},
);

const emit = defineEmits([
	'update:activeClass',
	'remove-class',
	'add-class',
	'update:activeGlobalClass',
	'paste-styles',
	'add-static-class',
	'remove-static-class',
]);

const cssClasses = useCSSClassesStore();

// Refs
const errorMessage = ref('');
const invalidClass = ref(false);
const focusClassIndex = ref(0);
const root: Ref<HTMLElement | null> = ref(null);
const inputRef: Ref<typeof BaseInput | null> = ref(null);
const dropDownWrapperRef: Ref<HTMLElement | null> = ref(null);
const dropdownState = ref(false);
const keyword = ref('');

const filteredClasses = computed(() => {
	if (keyword.value.length === 0) {
		const extraClasses: SelectorConfig[] = [
			{
				type: 'id',
				name: props.activeStyleElementId,
				deletable: false,
				id: props.activeStyleElementId,
				selected: props.activeGlobalClass === null,
				uid: props.activeStyleElementId,
			},
		];

		props.assignedClasses.forEach(cssClass => {
			const classConfig = cssClasses.getClassConfig(cssClass);

			if (classConfig) {
				extraClasses.push({
					type: 'class',
					name: classConfig.name,
					id: classConfig.id,
					deletable: true,
					selected: props.activeGlobalClass === classConfig.id,
					uid: classConfig.uid,
				});
			}
		});

		props.assignedStaticClasses.forEach(cssClassName => {
			extraClasses.push({
				type: 'static_class',
				name: cssClassName,
				id: cssClassName,
				deletable: true,
				selected: false,
				uid: cssClassName,
			});
		});

		return extraClasses;
	} else {
		const foundClasses: SelectorConfig[] = [];

		// Search in global classes
		cssClasses.getClassesByFilter(keyword.value).map(selectorConfig => {
			foundClasses.push({
				type: 'class',
				name: selectorConfig.name,
				id: selectorConfig.id,
				deletable: false,
				selected: false,
				uid: selectorConfig.uid,
			});
		});

		// search in static classes
		cssClasses.getStaticClassesByFilter(keyword.value).map(cssClassName => {
			foundClasses.push({
				type: 'static_class',
				name: cssClassName,
				id: cssClassName,
				deletable: true,
				selected: false,
				uid: cssClassName,
			});
		});

		return foundClasses;
	}
});

function removeClass(selectorConfig: SelectorConfig) {
	if (selectorConfig.type === 'class') {
		emit('remove-class', selectorConfig.uid);
	} else if (selectorConfig.type === 'static_class') {
		emit('remove-static-class', selectorConfig.uid);
	}

	// clear the keyword
	keyword.value = '';
	errorMessage.value = '';
}

function selectClass(selectorConfig: SelectorConfig) {
	if (selectorConfig.type === 'id') {
		emit('update:activeGlobalClass', null);
	} else if (selectorConfig.type === 'class') {
		// Check to see if we need to add the class to the element
		emit('add-class', selectorConfig.uid);
	} else if (selectorConfig.type === 'static_class') {
		emit('add-static-class', selectorConfig.uid);
	}

	nextTick(() => {
		focusClassIndex.value = filteredClasses.value.findIndex(item => item.id === selectorConfig.id);
	});
}

function addNewCssClass() {
	if (!invalidClass.value && keyword.value.length) {
		dropdownState.value = false;

		// check if the class already exists
		const existingClass = cssClasses.getClassConfig(keyword.value);

		if (existingClass) {
			// Add css class to element options
			emit('add-class', existingClass.uid);
		} else {
			const newClass = cssClasses.addCSSClass({
				id: keyword.value,
				name: keyword.value,
			});

			// Add css class to element options
			emit('add-class', newClass.uid);
		}

		// clear the keyword
		keyword.value = '';
	}
}

function handleClassInput(newCssClass: string) {
	keyword.value = newCssClass;

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

function onCopyStyles(selectorConfig: SelectorConfig) {
	// If this is a
	if (selectorConfig.type === 'class') {
		// Get the class config
		const stylesConfig = cssClasses.getStylesConfig(selectorConfig.uid);
		cssClasses.copyClassStyles(stylesConfig);
	} else {
		// Get the config from id
		cssClasses.copyClassStyles(props.element.getOptionValue(`_styles.${props.activeStyleElementId}.styles`, {}));
	}
}

function onPasteStyles(selectorConfig: SelectorConfig) {
	// If this is a
	if (selectorConfig.type === 'class') {
		// Get the class config
		cssClasses.pasteClassStyles(selectorConfig.uid);
	} else {
		// Get the config from id
		emit('paste-styles', selectorConfig);
	}
}

// Watchers
watch(dropdownState, newState => {
	if (newState) {
		document.addEventListener('click', closePanel);

		nextTick(() => {
			if (inputRef.value) {
				// Element not focused on next tick alone
				inputRef.value.focus();
			}
		});

		keyword.value = '';
	} else {
		document.removeEventListener('click', closePanel);
		errorMessage.value = '';
		focusClassIndex.value = 0;
	}
});

onBeforeUnmount(() => {
	document.removeEventListener('click', closePanel);
});

// Methods
function onKeyDown() {
	let nextClass;

	if (filteredClasses.value.length > 0) {
		if (dropDownWrapperRef.value) {
			dropDownWrapperRef.value.focus();
		}

		if (filteredClasses.value[focusClassIndex.value + 1]) {
			nextClass = filteredClasses.value[focusClassIndex.value + 1];
			selectClass(nextClass);
			focusClassIndex.value = focusClassIndex.value + 1;
		}
	}
}

function onKeyUp() {
	let previousClass;

	if (filteredClasses.value.length > 0) {
		if (dropDownWrapperRef.value) {
			dropDownWrapperRef.value.focus();
		}

		if (filteredClasses.value[focusClassIndex.value - 1]) {
			previousClass = filteredClasses.value[focusClassIndex.value - 1];
			selectClass(previousClass);
			focusClassIndex.value = focusClassIndex.value - 1;
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

	if (root.value && event.target instanceof Element) {
		if (
			!root.value.contains(event.target) &&
			event.target.tagName !== 'INPUT' &&
			!event.target.classList.contains('znpb-class-selector__add-class-button')
		) {
			dropdownState.value = false;
		}
	}
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

.znpb-class-selector__popper {
	position: absolute;
	left: 0;
	top: calc(100% + 5px);
}
</style>
