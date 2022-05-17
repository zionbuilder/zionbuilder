<template>
	<div ref="gradboard" class="znpb-gradient-wrapper__board">
		<GradientPreview :config="config"></GradientPreview>
		<div v-if="radialArr != null" class="znpb-gradient-radial-wrapper">
			<GradientRadialDragger
				v-for="(gradient, index) in radialArr"
				:key="gradient.type + index"
				:position="gradient.position"
				:active="activegrad === gradient"
				@mousedown="enableDragging(gradient)"
			/>
		</div>
	</div>
</template>

<script lang="ts">
export default {
	name: 'GradientBoard',
};
</script>

<script lang="ts" setup>
import { ref, computed, onBeforeUnmount } from 'vue';
import GradientPreview from './GradientPreview.vue';
import GradientRadialDragger from './GradientRadialDragger.vue';
import rafSchd from 'raf-schd';
import type { Gradient } from './GradientBar.vue';
import { clamp } from 'lodash-es';

const props = defineProps<{
	config: Gradient[];
	activegrad?: Gradient;
}>();

const emit = defineEmits<{
	(e: 'change-active-gradient', value: number): void;
	(e: 'position-changed', value: { x: number; y: number }): void;
}>();

const gradboard = ref<HTMLDivElement | null>(null);

const rafMovePosition = rafSchd(onCircleDrag);
const rafEndDragging = rafSchd(disableDragging);

const radialArr = computed({
	get() {
		return props.config.filter(gradient => gradient.type === 'radial');
	},
	set(newArr: Gradient[]) {
		radialArr.value = newArr;
	},
});

function enableDragging(gradient: Gradient) {
	document.addEventListener('mousemove', rafMovePosition);
	document.addEventListener('mouseup', rafEndDragging);

	document.body.style.userSelect = 'none';

	const activeGradientIndex = props.config.indexOf(gradient);
	// Set new active gradient config
	emit('change-active-gradient', activeGradientIndex);
}

function disableDragging() {
	document.removeEventListener('mousemove', rafMovePosition);
	document.removeEventListener('mouseup', rafEndDragging);

	document.body.style.userSelect = '';
}

function onCircleDrag(event: MouseEvent) {
	const gradBoard = (gradboard.value as HTMLDivElement).getBoundingClientRect();

	// calculate position and check if the dragger goes outside the board
	const newLeft = clamp(((event.clientX - gradBoard.left) * 100) / gradBoard.width, 0, 100);
	const newTop = clamp(((event.clientY - gradBoard.top) * 100) / gradBoard.height, 0, 100);

	emit('position-changed', {
		x: Math.round(newLeft),
		y: Math.round(newTop),
	});
}

onBeforeUnmount(() => {
	disableDragging();
});
</script>

<style lang="scss">
.znpb-gradient-wrapper__board {
	position: relative;
	overflow: hidden;
	margin-bottom: 9px;
	.znpb-gradient-preview-transparent {
		box-shadow: none;
	}
}
</style>
