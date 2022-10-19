<template>
	<div ref="root" class="znpb-form-colorPicker-saturation">
		<div
			ref="boardContent"
			:style="{ background: bgColor }"
			class="znpb-form-colorPicker-saturation__color"
			@mousedown="initiateDrag"
			@mouseup="deactivateDragCircle"
		>
			<div class="znpb-form-colorPicker-saturation__white">
				<div class="znpb-form-colorPicker-saturation__black">
					<div :style="pointStyles" class="znpb-color-picker-pointer"></div>
				</div>
			</div>
		</div>
	</div>
</template>

<script lang="ts" setup>
import rafSchd from 'raf-schd';
import { ref, computed, Ref, onMounted, onBeforeUnmount } from 'vue';
import type { ColorFormats } from 'tinycolor2';

const props = defineProps<{
	colorObject: {
		hsva: ColorFormats.HSVA;
	};
}>();

const emit = defineEmits<{
	(e: 'update:color-object', value: ColorFormats.HSVA): void;
}>();

const isDragging = ref(false);

const root: Ref<HTMLDivElement | null> = ref(null);
const boardContent: Ref<HTMLDivElement | null> = ref(null);

let ownerWindow: Window;

const computedColorObject = computed({
	get() {
		return props.colorObject;
	},
	set(newValue) {
		emit('update:color-object', newValue);
	},
});

/**
 * Returns the position of the pointer
 */
const pointStyles = computed(() => {
	const { v, s } = props.colorObject.hsva;
	const cssStyles = {
		top: 100 - v * 100 + '%',
		left: s * 100 + '%',
	};
	return cssStyles;
});

/**
 * Returns the background of the colorBoard
 */
const bgColor = computed(() => {
	const { h } = props.colorObject.hsva;
	return `hsl(${h}, 100%, 50%)`;
});

const boardRect = computed(() => {
	return (boardContent.value as HTMLDivElement).getBoundingClientRect();
});

const rafDragCircle = rafSchd(dragCircle);

function initiateDrag(event: MouseEvent) {
	isDragging.value = true;
	let { clientX, clientY } = event;

	ownerWindow.addEventListener('mousemove', rafDragCircle);
	ownerWindow.addEventListener('mouseup', deactivateDragCircle, true);

	// Emit click value
	const newTop = clientY - boardRect.value.top;
	const newLeft = clientX - boardRect.value.left;
	let bright = 100 - (newTop / boardRect.value.height) * 100;
	let saturation = (newLeft * 100) / boardRect.value.width;

	let newColor = {
		...props.colorObject.hsva,
		v: bright / 100,
		s: saturation / 100,
	};

	computedColorObject.value = newColor;
}

function deactivateDragCircle() {
	ownerWindow.removeEventListener('mousemove', rafDragCircle);
	ownerWindow.removeEventListener('mouseup', deactivateDragCircle, true);

	function preventClicks(e: MouseEvent) {
		e.stopPropagation();
	}

	// Prevent closing colorPicker when clicked outside
	ownerWindow.addEventListener('click', preventClicks, true);
	setTimeout(() => {
		ownerWindow.removeEventListener('click', preventClicks, true);
	}, 100);
}

function dragCircle(event: MouseEvent) {
	// If the mouseup happened outside window
	if (!event.which) {
		deactivateDragCircle();
		return false;
	}
	let { clientX, clientY } = event;

	// don't let the circle outside hue area
	let newLeft = clientX - boardRect.value.left;
	if (newLeft > boardRect.value.width) {
		newLeft = boardRect.value.width;
	} else if (newLeft < 0) {
		newLeft = 0;
	}

	let newTop = clientY - boardRect.value.top;

	// don't let the circle outside hue area
	if (newTop >= boardRect.value.height) {
		newTop = boardRect.value.height;
	} else if (newTop < 0) {
		newTop = 0;
	}

	const bright = 100 - (newTop / boardRect.value.height) * 100;
	const saturation = (newLeft * 100) / boardRect.value.width;

	let newColor = {
		...props.colorObject.hsva,
		v: bright / 100,
		s: saturation / 100,
	};

	computedColorObject.value = newColor;
}

onMounted(() => {
	ownerWindow = (root.value as HTMLDivElement).ownerDocument.defaultView as Window;

	(root.value as HTMLDivElement).ownerDocument.body.classList.add('znpb-color-picker--backdrop');
});

onBeforeUnmount(() => {
	(root.value as HTMLDivElement).ownerDocument.body.classList.remove('znpb-color-picker--backdrop');
	deactivateDragCircle();
});
</script>

<style lang="scss">
.znpb-color-picker--backdrop,
.znpb-color-gradient--backdrop {
	&:before {
		content: '';
		position: absolute;
		width: 100%;
		height: 100%;
	}
}

.znpb-form-colorPicker-saturation {
	position: relative;
	overflow: hidden;
	height: 180px;
	border-top-right-radius: 3px;
	border-top-left-radius: 3px;

	&__color,
	&__white,
	&__black {
		position: absolute;
		top: 0;
		right: 0;
		bottom: 0;
		left: 0;
	}
	&__white {
		background: linear-gradient(to right, #fff, rgba(255, 255, 255, 0));
	}
	&__black {
		background: linear-gradient(to top, #000, rgba(0, 0, 0, 0));
	}
}
.znpb-color-picker-pointer {
	position: absolute;
	top: 50%;
	z-index: 1;
	width: 12px;
	height: 12px;
	margin: 0 auto;
	background: #fff;
	border-radius: 50%;
	transform: translate(-50%, -50%);
	cursor: move;
}
</style>
