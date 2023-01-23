<template>
	<div ref="root" class="znpb-gradient-bar-colors-wrapper">
		<div ref="gradientbar" class="znpb-gradient-bar-wrapper">
			<GradientBarPreview :config="computedValue" @click="addColor" />
			<GradientDragger
				v-for="(colorConfig, i) in computedValue.colors"
				:key="i"
				:modelValue="colorConfig"
				@update:modelValue="onColorConfigUpdate(colorConfig, $event)"
				@color-picker-open="colorPickerOpen = $event"
				@mousedown="enableDragging(i)"
			/>
		</div>
		<div class="znpb-gradient-colors-legend">
			<span class="znpb-form__input-title znpb-gradient-colors-legend-item"> {{ i18n.__('Color', 'zionbuilder') }} </span>
			<span class="znpb-form__input-title znpb-gradient-colors-legend-item"> {{ i18n.__('Location', 'zionbuilder') }} </span>
		</div>
		<GradientColorConfig
			v-for="(colorConfig, i) in sortedColors"
			:key="i"
			:config="colorConfig"
			:show-delete="sortedColors.length > 2"
			@update:modelValue="onColorConfigUpdate(colorConfig, $event)"
			@delete-color="onDeleteColor(colorConfig)"
		/>
	</div>
</template>

<script lang="ts">
export default {
	name: 'GradientBar',
};
</script>

<script lang="ts" setup>
import * as i18n from '@wordpress/i18n';
import { ref, computed, nextTick, onMounted, onBeforeUnmount } from 'vue';
import GradientBarPreview from './GradientBarPreview.vue';
import GradientDragger from './GradientDragger.vue';
import GradientColorConfig from './GradientColorConfig.vue';
import rafSchd from 'raf-schd';

export type Color = { color: string; position: number; __dynamic_content__?: any };
export type Position = { x: number; y: number };

export interface Gradient {
	angle: number;
	colors: Color[];
	position: Position;
	type: string;
}

const props = defineProps<{
	modelValue: Gradient;
}>();

const emit = defineEmits<{
	(e: 'update:modelValue', value: Gradient): void;
}>();

const root = ref<HTMLDivElement | null>(null);
const gradientbar = ref<HTMLDivElement | null>(null);

const gradref = ref<DOMRect | null>(null);
const colorPickerOpen = ref(false);
const deletedColorConfig = ref<Color | null>(null);

const rafMovePosition = rafSchd(onCircleDrag);
const rafEndDragging = rafSchd(disableDragging);

let draggedCircleIndex: number | null;
let draggedItem: Color;

const computedValue = computed({
	get() {
		return props.modelValue;
	},
	set(newValue: Gradient) {
		emit('update:modelValue', newValue);
	},
});

const sortedColors = computed(() => {
	let colorsCopy = [...computedValue.value.colors].sort((a, b) => {
		return a.position > b.position ? 1 : -1;
	});

	return colorsCopy;
});

const activeDraggedItem = computed(() => {
	return computedValue.value.colors[draggedCircleIndex as number];
});

function onColorConfigUpdate(colorConfig: Color, newValues: Color) {
	const index = computedValue.value.colors.indexOf(colorConfig);
	const updatedValues = computedValue.value.colors.slice(0);
	updatedValues.splice(index, 1, newValues);

	computedValue.value = {
		...computedValue.value,
		colors: updatedValues,
	};
}

function onDeleteColor(colorConfig: Color) {
	const index = computedValue.value.colors.indexOf(colorConfig);
	const colorsClone = computedValue.value.colors.slice(0);

	deletedColorConfig.value = colorConfig;
	colorsClone.splice(index, 1);

	computedValue.value = {
		...computedValue.value,
		colors: colorsClone,
	};
}

function reAddColor() {
	computedValue.value = {
		...computedValue.value,
		colors: [...computedValue.value.colors, deletedColorConfig.value as Color],
	};

	deletedColorConfig.value = null;
}

function addColor(event: MouseEvent) {
	const defaultColor = {
		color: 'white',
		position: 0,
	};

	const mouseLeftPosition = event.clientX;
	const barOffset = (root.value as HTMLDivElement).getBoundingClientRect();

	// Calculate where the coordinate x of the element starts
	const startx = barOffset.left;
	// from total width reduce the coordinate x value
	const newLeft = mouseLeftPosition - startx;

	defaultColor.position = Math.round((newLeft / barOffset.width) * 100);

	const updatedValues = {
		...computedValue.value,
		colors: [...computedValue.value.colors, defaultColor],
	};

	computedValue.value = updatedValues;
}

function enableDragging(colorConfigIndex: number) {
	if (colorPickerOpen.value === false) {
		document.body.classList.add('znpb-color-gradient--backdrop');
		document.addEventListener('mousemove', rafMovePosition);
		document.addEventListener('mouseup', rafEndDragging);
		document.body.style.userSelect = 'none';
		draggedCircleIndex = colorConfigIndex;
		draggedItem = computedValue.value.colors[colorConfigIndex];
		deletedColorConfig.value = null;
	}
}

function disableDragging() {
	document.body.classList.remove('znpb-color-gradient--backdrop');
	document.removeEventListener('mousemove', rafMovePosition);
	document.removeEventListener('mouseup', rafEndDragging);
	document.body.style.userSelect = '';
	deletedColorConfig.value = null;
	draggedCircleIndex = null;
}

function updateActiveConfigPosition(newPosition: number) {
	const newConfig = {
		...activeDraggedItem.value,
		position: newPosition,
	};

	onColorConfigUpdate(activeDraggedItem.value, newConfig);
}

function onCircleDrag(event: MouseEvent) {
	// calculate the dragger left position %
	let newLeft = ((event.clientX - (gradref.value as DOMRect).left) * 100) / (gradref.value as DOMRect).width;
	const position = Math.min(Math.max(newLeft, 0), 100);

	// check if the user wants to delete the color as in photoshop
	if (newLeft > 100 || newLeft < 0) {
		// Check to see if we need to delete the color
		if (sortedColors.value.length > 2 && deletedColorConfig.value === null) {
			onDeleteColor(draggedItem);
		}
	} else {
		if (deletedColorConfig.value !== null) {
			reAddColor();
		}

		nextTick(() => {
			// Update position
			updateActiveConfigPosition(Math.round(position));
		});
	}
}

onMounted(() => {
	nextTick(() => {
		gradref.value = (gradientbar.value as HTMLDivElement).getBoundingClientRect();
	});
});

onBeforeUnmount(() => {
	document.body.classList.remove('znpb-color-gradient--backdrop');
	disableDragging();
});
</script>

<style lang="scss">
.znpb-gradient-bar-wrapper {
	position: relative;
	height: 100%;
	.znpb-gradient-preview {
		max-height: 25px;
		margin-bottom: 0;
		border-radius: 3px;
	}
}
.znpb-gradient-bar-colors-wrapper {
	display: flex;
	flex-direction: column;
}

.znpb-gradient-colors-legend {
	display: flex;

	&-item {
		flex: 1;

		&:last-child {
			margin-left: 20px;
		}
	}
}
</style>
