<template>
	<div class="znpb-gradient-dragger-wrapper">
		<Tooltip :show="showPicker" :trigger="null" placement="top">
			<span ref="gradientCircle" class="znpb-gradient-dragger" :style="colorPosition" @dblclick="openColorPicker" />

			<template #content>
				<ColorPicker
					v-if="showPicker"
					ref="colorpickerHolder"
					:parent-position="parentPosition"
					:model="computedValue.color"
					:show-library="false"
					@color-changed="colorValue = $event"
				/>
			</template>
		</Tooltip>
	</div>
</template>

<script lang="ts">
export default {
	name: 'GradientDragger',
};
</script>

<script lang="ts" setup>
import { ref, computed, nextTick, onMounted, onUnmounted } from 'vue';
import { ColorPicker } from '../Colorpicker';
import { Tooltip } from '@zionbuilder/tooltip';
import type { Color } from './GradientBar.vue';

const props = defineProps<{
	modelValue: Color;
}>();

const emit = defineEmits<{
	(e: 'update:modelValue', value: Color): void;
	(e: 'color-picker-open', value: boolean): void;
}>();

const gradientCircle = ref<HTMLSpanElement | null>(null);
const colorpickerHolder = ref<InstanceType<typeof ColorPicker> | null>(null);

const showPicker = ref(false);
const circlePos = ref<DOMRect | null>(null);

const computedValue = computed({
	get() {
		return props.modelValue;
	},
	set(newValue: Color) {
		emit('update:modelValue', newValue);
	},
});

const colorValue = computed({
	get() {
		return computedValue.value.color;
	},
	set(newValue: string) {
		computedValue.value = {
			...computedValue.value,
			color: newValue,
		};
	},
});

const colorPosition = computed(() => {
	const cssStyles = {
		left: computedValue.value.position + '%',
		background: computedValue.value.color,
	};
	return cssStyles;
});

const parentPosition = computed(() => {
	return {
		left: (circlePos.value as DOMRect).left,
		top: (circlePos.value as DOMRect).top,
	};
});

function openColorPicker() {
	showPicker.value = true;
	emit('color-picker-open', true);
	document.addEventListener('mousedown', closePanelOnOutsideClick);
}

function closePanelOnOutsideClick(event: MouseEvent) {
	if (!(colorpickerHolder.value as InstanceType<typeof ColorPicker>).$el.contains(event.target)) {
		showPicker.value = false;

		document.removeEventListener('mousedown', closePanelOnOutsideClick);
		emit('color-picker-open', false);
	}
}

onMounted(() => {
	nextTick(() => {
		circlePos.value = (gradientCircle.value as HTMLSpanElement).getBoundingClientRect();
	});
});

onUnmounted(() => {
	document.removeEventListener('mousedown', closePanelOnOutsideClick);
});
</script>

<style lang="scss" scoped>
.znpb-gradient-dragger {
	@include circlesimple(12px);
	position: absolute;
	top: 0;
	left: 0;
	box-shadow: 0 0 10px 0 rgba(0, 0, 0, 0.2);
	border: 2px solid #fff;
	transform: translate(-50%, 150%);
}
</style>
