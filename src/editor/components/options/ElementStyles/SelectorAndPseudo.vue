<template>
	<div class="znpb-element-styles__media-wrapper">
		<ClassSelectorDropdown
			v-model="computedClasses"
			v-model:activeClass="activeClass"
			:selector="selector"
			:title="title"
			:allow_class_assignments="allow_class_assignments"
			:active-model-value="modelValue.styles"
			@paste-style-model="onPasteToSelector"
		/>

		<PseudoSelectors v-model="computedStyles" />
	</div>
</template>

<script lang="ts" setup>
import { ref, computed } from 'vue';
import { merge, cloneDeep } from 'lodash-es';
import PseudoSelectors from './PseudoSelectors.vue';
import ClassSelectorDropdown from './ClassSelectorDropdown.vue';
import { useCSSClassesStore } from '/@/editor/store';

const cssClasses = useCSSClassesStore();

const props = withDefaults(
	defineProps<{
		modelValue: Record<string, unknown>;
		title: string;
		selector: string;
		// eslint-disable-next-line vue/prop-name-casing
		allow_class_assignments: boolean;
	}>(),
	{
		modelValue: () => ({}),
		allow_class_assignments: true,
	},
);

const emit = defineEmits(['update:modelValue']);

// Refs
const activeClass = ref(props.selector);

// Computed
const computedStyles = computed({
	get() {
		if (activeClass.value !== props.selector) {
			const activeClassConfig = cssClasses.getClassConfig(activeClass.value);
			if (activeClassConfig) {
				return activeClassConfig.styles;
			}

			// eslint-disable-next-line
			console.warn(`Class with id ${activeClass.value} not found`)
			return {};
		} else {
			return props.modelValue.styles;
		}
	},
	set(newValue) {
		if (activeClass.value !== props.selector) {
			const activeClassConfig = cssClasses.getClassConfig(activeClass.value);
			if (activeClassConfig) {
				activeClassConfig.styles = newValue;
			}
		} else {
			updateValues('styles', newValue);
		}
	},
});

const computedClasses = computed({
	get() {
		return props.modelValue ? props.modelValue.classes || [] : [];
	},
	set(newValue) {
		emit('update:modelValue', {
			...props.modelValue,
			classes: newValue,
		});
	},
});

function onPasteToSelector() {
	const clonedCopiedStyles = cloneDeep(cssClasses.copiedStyles);
	if (!props.modelValue.styles) {
		if (clonedCopiedStyles !== null) {
			updateValues('styles', clonedCopiedStyles);
		}
	} else {
		updateValues('styles', merge(props.modelValue.styles, clonedCopiedStyles));
	}
}

function updateValues(type: string, newValue: Record<string, unknown>) {
	const clonedValue = { ...props.modelValue };
	if (newValue === null && typeof clonedValue[type]) {
		// If this is used as layout, we need to delete the active pseudo selector
		delete clonedValue[type];
	} else {
		clonedValue[type] = newValue;
	}

	emit('update:modelValue', clonedValue);
}
</script>
