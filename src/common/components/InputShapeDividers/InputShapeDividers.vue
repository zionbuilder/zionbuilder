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
import * as i18n from '@wordpress/i18n';
import { computed, ref } from 'vue';
import { get } from 'lodash-es';
import { InputCustomSelector } from '../InputCustomSelector';

interface ShapeDivider {
	shape?: string;
	height?: { default: string; tablet: string; mobile: string; laptop: string } | 'auto';
	color?: string;
	flip?: boolean;
}

type Position = 'top' | 'bottom';
const props = withDefaults(
	defineProps<{
		modelValue?: Partial<Record<Position, ShapeDivider>>;
	}>(),
	{
		modelValue: () => {
			return {};
		},
	},
);

const emit = defineEmits<{
	(e: 'update:modelValue', value: Partial<Record<Position, ShapeDivider>> | null): void;
}>();

const maskPosOptions = ref([
	{
		id: 'top',
		name: i18n.__('Top masks', 'zionbuilder'),
	},
	{
		id: 'bottom',
		name: i18n.__('Bottom masks', 'zionbuilder'),
	},
]);

const activeMaskPosition = ref<Position>('top');

const computedTitle = computed(() => {
	return activeMaskPosition.value === 'top'
		? i18n.__('Selected top mask', 'zionbuilder')
		: i18n.__('Selected bottom mask', 'zionbuilder');
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
			title: i18n.__('Add a color to mask', 'zionbuilder'),
		},
		height: {
			type: 'dynamic_slider',
			id: 'height',
			title: i18n.__('Add mask height', 'zionbuilder'),
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
			title: i18n.__('Flip mask horizontally', 'zionbuilder'),
			width: '100',
			layout: 'inline',
		},
	};
});

const computedValue = computed({
	get() {
		return props.modelValue?.[activeMaskPosition.value] ?? {};
	},
	set(newValue: ShapeDivider) {
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
