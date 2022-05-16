<template>
	<div class="znpb-shadow-option-wrapper__outer" :class="`znpb-shadow-option--${shadow_type}`">
		<OptionsForm v-model="valueModel" :schema="schema" class="znpb-shadow-option" />
	</div>
</template>

<script lang="ts">
export default {
	name: 'InputTextShadow',
};
</script>

<script lang="ts" setup>
import { computed } from 'vue';
import { useOptionsSchemas } from '../../composables';

interface Shadow {
	'offset-x'?: string;
	'offset-y'?: string;
	blur?: string;
	color?: string;
}

const props = withDefaults(
	defineProps<{
		modelValue?: Shadow;
		inset?: boolean;
		shadow_type?: string;
	}>(),
	{
		modelValue: () => {
			return {};
		},
		shadow_type: 'text-shadow',
	},
);

const emit = defineEmits(['update:modelValue']);

const { getSchema } = useOptionsSchemas();

const schema = computed(() => {
	if (props.shadow_type === 'text-shadow') {
		const { inset, spread, ...shadowSchema } = getSchema('shadowSchema');
		return shadowSchema;
	}

	return getSchema('shadowSchema');
});

const valueModel = computed({
	get() {
		return props.modelValue || {};
	},
	set(newValue: Shadow) {
		emit('update:modelValue', newValue);
	},
});
</script>

<style lang="scss">
.znpb-shadow-option {
	display: flex;
	justify-content: space-between;
	align-items: flex-start;

	.znpb-input-wrapper {
		padding-bottom: 10px;
	}

	.znpb-options-form-wrapper & {
		padding: 0;
		margin: 0 -5px;
	}

	&--text-shadow {
		.znpb-shadow-option__colorpicker.znpb-input-wrapper {
			width: 100% !important;

			> * {
				width: calc(50% - 5px);
			}
		}
	}

	&--ptop.znpb-input-wrapper {
		padding-top: 10px;
	}
}
</style>
