<template>
	<div>
		<InputCustomSelector v-model="activeMaskPosition" :options="maskPosOptions" :columns="2" />

		<OptionsForm v-model="computedValue" :schema="schema" />
	</div>
</template>

<script lang="ts">
export default {
	name: 'InputShapeDividers',
};
</script>

<script lang="ts" setup>
import { computed, ref } from 'vue';
import { get } from 'lodash-es';
import { InputCustomSelector } from '../InputCustomSelector';
import { translate } from '@zb/i18n';

interface ShapeDevider {
	shape?: string;
	height?: { default: string; tablet: string; mobile: string; laptop: string } | 'auto';
	color?: string;
	flip?: boolean;
}

type Position = 'top' | 'bottom';
const props = withDefaults(
	defineProps<{
		modelValue?: Partial<Record<Position, ShapeDevider>>;
	}>(),
	{
		modelValue: () => {
			return {};
		},
	},
);

const emit = defineEmits<{
	(e: 'update:modelValue', value: Partial<Record<Position, ShapeDevider>> | null): void;
}>();

const maskPosOptions = ref([
	{
		id: 'top',
		name: translate('top_masks'),
	},
	{
		id: 'bottom',
		name: translate('bottom_masks'),
	},
]);

const activeMaskPosition = ref<Position>('top');

const computedTitle = computed(() => {
	return activeMaskPosition.value === 'top' ? translate('select_top_mask') : translate('select_bottom_mask');
});

const schema = computed(() => {
	return {
		shape: {
			type: 'shape_component',
			id: 'shape',
			width: '100',
			title: computedTitle.value,
			position: activeMaskPosition.value,
		},
		color: {
			type: 'colorpicker',
			id: 'color',
			width: '100',
			title: translate('select_mask_color'),
		},
		height: {
			type: 'dynamic_slider',
			id: 'height',
			title: translate('select_mask_height'),
			width: '100',
			responsive_options: true,
			options: [
				{ unit: 'px', min: 0, max: 4999, step: 1 },
				{ unit: '%', min: 0, max: 100, step: 1 },
				{ unit: 'vh', min: 0, max: 100, step: 10 },
				{ unit: 'auto' },
			],
		},
		flip: {
			type: 'checkbox_switch',
			id: 'flip',
			title: translate('flip_mask'),
			width: '100',
			layout: 'inline',
		},
	};
});

const computedValue = computed({
	get() {
		return props.modelValue?.[activeMaskPosition.value] ?? {};
	},
	set(newValue: ShapeDevider) {
		if (newValue === null) {
			emit('update:modelValue', null);
			return;
		}

		const shape: string = get(props.modelValue, `${activeMaskPosition.value}.shape`);
		if (shape !== newValue['shape'] && newValue['height']) {
			newValue['height'] = 'auto';
		}
		emit('update:modelValue', {
			...props.modelValue,
			[activeMaskPosition.value]: newValue,
		});
	},
});
</script>
<style lang="scss">
.znpb-shape-list {
	display: flex;
	flex-direction: column;
	max-height: 400px;
	padding: 20px;
	margin: 0 -20px;
	background-color: var(--zb-surface-light-color);
}

/* Enter and leave transitions for delete mask */
.slide-fade-enter-to,
.slide-fade-leave-from {
	transition: all 0.1s;
}

.slide-fade-enter-from,
.slide-fade-leave-to {
	opacity: 0;
}
</style>
