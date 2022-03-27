<template>
	<div ref="root" class="znpb-colorpicker-inner-editor__opacity-wrapper" @mousedown="actCircleDrag">
		<div class="znpb-colorpicker-inner-editor__opacity" @click="dragCircle">
			<div ref="opacityStrip" :style="barStyles" class="znpb-colorpicker-inner-editor__opacity-strip"></div>
			<span
				:style="opacityStyles"
				class="znpb-colorpicker-inner-editor__opacity-indicator"
				@mousedown="actCircleDrag"
			></span>
		</div>
	</div>
</template>

<script lang="ts" setup>
import tinycolor, { ColorFormats } from 'tinycolor2';
import rafSchd from 'raf-schd';
import { onMounted, onBeforeUnmount, ref, computed, Ref } from 'vue';

const props = defineProps<{
	modelValue: ColorFormats.HSLA;
}>();

const emit = defineEmits(['update:modelValue']);

// Refs
const root: Ref<HTMLDivElement | null> = ref(null);
const opacityStrip: Ref<HTMLDivElement | null> = ref(null);

// Temp values
const rafDragCircle = rafSchd(dragCircle);
let lastA: number;
let ownerWindow: Window;

// Computed properties
const opacityStyles = computed(() => {
	return {
		left: props.modelValue.a * 100 + '%',
	};
});

const barStyles = computed(() => {
	const color = tinycolor(props.modelValue);
	return {
		'background-image': 'linear-gradient(to right, rgba(255, 0, 0, 0),' + color.toHexString() + ')',
	};
});

// Methods
function actCircleDrag() {
	ownerWindow.addEventListener('mousemove', rafDragCircle);
	ownerWindow.addEventListener('mouseup', deactivateDragCircle);
}

function deactivateDragCircle() {
	ownerWindow.removeEventListener('mousemove', rafDragCircle);
	ownerWindow.removeEventListener('mouseup', deactivateDragCircle);

	function preventClicks(e: MouseEvent) {
		e.stopPropagation();
	}

	// Prevent closing colorpicker when clicked outside
	ownerWindow.addEventListener('click', preventClicks, true);
	setTimeout(() => {
		ownerWindow.removeEventListener('click', preventClicks, true);
	}, 100);
}

function dragCircle(event: MouseEvent) {
	// @TODO Remove following unused code
	// If the mouseup happened outside window
	if (!event.which) {
		deactivateDragCircle();
		return false;
	}

	let a;
	const mouseLeftPosition = event.clientX;

	const stripOffset = (opacityStrip.value as HTMLDivElement).getBoundingClientRect();

	// Calculate where the coordinate x of the element starts
	const startX = stripOffset.left;
	// from total width reduce the coordinate x value
	const newLeft = mouseLeftPosition - startX;

	// don't let the circle outside opacity strip
	if (newLeft > stripOffset.width) {
		a = 1;
	} else if (newLeft < 0) {
		a = 0;
	} else {
		a = newLeft / stripOffset.width;
		a = Number(a.toFixed(2));
	}

	const newColor = {
		...props.modelValue,
		a: a,
	};

	if (lastA !== a) {
		emit('update:modelValue', newColor);
	}

	lastA = a;
}

// Lifecycle
onMounted(() => {
	ownerWindow = root.value?.ownerDocument.defaultView as Window;
});

onBeforeUnmount(() => {
	deactivateDragCircle();
});
</script>
<style lang="scss">
.znpb-colorpicker-inner-editor__opacity {
	@extend %opacitybg;

	&-indicator {
		@extend %hue-indicator-style;
	}
	&-strip {
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		background-image: linear-gradient(to right, rgba(255, 0, 0, 0), rgba(255, 0, 0, 1));
	}
}
</style>
