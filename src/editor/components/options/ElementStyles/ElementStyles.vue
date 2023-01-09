<template>
	<div class="znpb-element-styles__wrapper">
		<SelectorAndPseudo
			v-if="ElementOptionsPanelAPI"
			v-model="computedModelValue"
			v-model:activeClass="activeClass"
			:title="title"
			:selector="selector"
			:allow_class_assignments="allow_class_assignments"
		/>

		<OptionsForm
			v-model="computedStyles"
			:schema="getSchema('element_styles')"
			class="znpb-element-styles-option__options-wrapper"
		/>
	</div>
</template>

<script lang="ts" setup>
import { ref, watch, computed, inject } from 'vue';
import { useCSSClassesStore } from '/@/editor/store';
import SelectorAndPseudo from './SelectorAndPseudo.vue';

const { useOptionsSchemas } = window.zb.composables;

const props = withDefaults(
	defineProps<{
		modelValue: {
			classes?: string[];
		};
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

const computedModelValue = computed({
	get() {
		return props.modelValue;
	},
	set(newValue) {
		emit('update:modelValue', newValue);
	},
});

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

const { getSchema } = useOptionsSchemas();
const cssClasses = useCSSClassesStore();
const activeClass = ref(props.selector);

watch(
	() => props.selector,
	newValue => {
		activeClass.value = newValue;
	},
);

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

const ElementOptionsPanelAPI = inject('ElementOptionsPanelAPI', null);
</script>
<style lang="scss">
.znpb-element-styles {
	&__wrapper {
		display: flex;
		flex-direction: column;
		height: 100%;
	}
}
.znpb-options-form-wrapper.znpb-element-styles-option__options-wrapper {
	padding: 20px 0 0;
}
</style>
