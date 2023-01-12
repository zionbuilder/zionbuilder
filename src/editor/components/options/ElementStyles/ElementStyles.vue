<template>
	<div class="znpb-element-styles__wrapper">
		<div class="znpb-elementStylesStateWrapper">
			<span class="znpb-elementStylesStateTitle">{{ __('State:', 'zionbuilder') }}</span>

			<PseudoSelectors v-model="computedStyles" />
		</div>

		<OptionsForm
			v-model="computedStyles"
			:schema="getSchema('element_styles')"
			class="znpb-element-styles-option__options-wrapper"
		/>
	</div>
</template>

<script lang="ts" setup>
import { __ } from '@wordpress/i18n';
import { computed, inject, onMounted, onBeforeUnmount } from 'vue';
import PseudoSelectors from './PseudoSelectors.vue';

const { useOptionsSchemas } = window.zb.composables;

const props = withDefaults(
	defineProps<{
		modelValue: {
			classes?: string[];
		};
		// eslint-disable-next-line vue/prop-name-casing
		allow_class_assignments: boolean;
		elementStyleId?: string;
	}>(),
	{
		modelValue: () => ({}),
		allow_class_assignments: true,
		elementStyleId: '',
	},
);

const emit = defineEmits(['update:modelValue']);

const computedStyles = computed({
	get() {
		return props.modelValue.styles;
	},
	set(newValue) {
		updateValues('styles', newValue);
	},
});

const { getSchema } = useOptionsSchemas();

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

onMounted(() => {
	if (ElementOptionsPanelAPI && props.elementStyleId) {
		ElementOptionsPanelAPI.setActiveStyleElementId(props.elementStyleId);
	}
});

onBeforeUnmount(() => {
	if (ElementOptionsPanelAPI && props.elementStyleId) {
		ElementOptionsPanelAPI.resetActiveSelectorConfig();
	}
});
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
	padding: 0;
}

.znpb-elementStylesStateWrapper {
	display: flex;
	align-items: center;
	margin-bottom: 10px;
}

.znpb-elementStylesStateTitle {
	margin-right: 10px;
}
</style>
