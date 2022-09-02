<template>
	<div ref="root" class="znpb-colorpicker-inner-editor__hue-wrapper" @click="dragHueCircle">
		<div class="znpb-colorpicker-inner-editor__hue">
			<span :style="hueStyles" class="znpb-colorpicker-inner-editor__hue-indicator" @mousedown="actHueCircleDrag" />
		</div>
	</div>
</template>

<script lang="ts" setup>
import { ref, computed, watch, Ref, onMounted, onUnmounted, onBeforeUnmount } from 'vue';
import type { ColorFormats } from 'tinycolor2';

const props = defineProps<{
	modelValue: ColorFormats.HSLA;
}>();

const emit = defineEmits<{
	(e: 'update:modelValue', value: ColorFormats.HSLA): void;
}>();

const direction = ref('right');
const oldHue = ref(props.modelValue?.h);
const lastHue: Ref<number | null> = ref(null);
const root: Ref<HTMLDivElement | null> = ref(null);

let ownerWindow: Window;

const hueStyles = computed(() => {
	const { h } = props.modelValue;
	let positionValue = (props.modelValue.h / 360) * 100;

	if (h === 0 && direction.value === 'right') {
		positionValue = 100;
	}

	return {
		left: positionValue + '%',
	};
});

watch(
	() => props.modelValue,
	() => {
		const { h } = props.modelValue;
		if (h !== 0 && h > oldHue.value) {
			direction.value = 'right';
		}
		if (h !== 0 && h < oldHue.value) {
			direction.value = 'left';
		}

		oldHue.value = h;
	},
);

const rafDragCircle = rafSchd(dragHueCircle);

function actHueCircleDrag() {
	ownerWindow.addEventListener('mousemove', rafDragCircle);
	ownerWindow.addEventListener('mouseup', deactivatedragHueCircle);
}

function deactivatedragHueCircle() {
	ownerWindow.removeEventListener('mousemove', rafDragCircle);
	ownerWindow.removeEventListener('mouseup', deactivatedragHueCircle);

	function preventClicks(e: MouseEvent) {
		e.stopPropagation();
	}

	// Prevent closing colorpicker when clicked outside
	ownerWindow.addEventListener('click', preventClicks, true);
	setTimeout(() => {
		ownerWindow.removeEventListener('click', preventClicks, true);
	}, 100);
}

function dragHueCircle(event: MouseEvent) {
	// If the mouseup happened outside window

	// @TODO Remove following unused code
	if (!event.which) {
		deactivatedragHueCircle();
		return false;
	}

	let h: number;
	const mouseLeftPosition = event.clientX;
	const stripOffset = (root.value as HTMLDivElement).getBoundingClientRect();

	// Calculate where the coordinate x of the element starts
	const startX = stripOffset.left;
	// from total width reduce the coordinate x value
	const newLeft = mouseLeftPosition - startX;
	// don't let the circle outside opacity strip
	if (newLeft > stripOffset.width) {
		h = 360;
	} else if (newLeft < 0) {
		h = 0;
	} else {
		const percent = (newLeft * 100) / stripOffset.width;
		h = (360 * percent) / 100;
	}

	let newColor = {
		...props.modelValue,
		h,
	};

	if (lastHue.value !== h) {
		emit('update:modelValue', newColor);
	}

	lastHue.value = h;
}

onMounted(() => {
	ownerWindow = (root.value as HTMLDivElement).ownerDocument.defaultView as Window;
});

onBeforeUnmount(() => {
	deactivatedragHueCircle();
});

onUnmounted(() => {
	ownerWindow.removeEventListener('mousemove', dragHueCircle);
});
</script>

<script lang="ts">
import rafSchd from 'raf-schd';

export default {
	name: 'HueStrip',
};
</script>
<style lang="scss">
@import '../../scss/_mixins.scss';
.znpb-colorpicker-inner-editor__hue {
	background-image: linear-gradient(to right, #f00, #ff0, #0f0, #0ff, #00f, #f0f, #f00);

	&-wrapper {
		margin-bottom: 14px;
	}

	&-indicator {
		@extend %hue-indicator-style;
	}
}
</style>
