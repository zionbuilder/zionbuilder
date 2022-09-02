<template>
	<div class="znpb-gradient-preview-transparent">
		<div
			class="znpb-gradient-preview"
			:style="getGradientPreviewStyle"
			:class="{ 'gradient-type-rounded': round }"
		></div>
	</div>
</template>

<script lang="ts">
export default {
	name: 'OneGradient',
};
</script>

<script lang="ts" setup>
import { computed, CSSProperties } from 'vue';
import type { Gradient } from './GradientBar.vue';

const props = defineProps<{
	config: Gradient;
	round?: boolean;
}>();

const getGradientPreviewStyle = computed(() => {
	const style: CSSProperties = {};
	const gradient = [];
	const colors: string[] = [];
	let position = '90deg';

	const colorsCopy = [...props.config.colors].sort((a, b) => {
		return a.position > b.position ? 1 : -1;
	});

	colorsCopy.forEach(color => {
		colors.push(`${color.color} ${color.position}%`);
	});

	// Set position
	if (props.config.type === 'radial') {
		const { x, y } = props.config.position || { x: 50, y: 50 };
		position = `circle at ${x}% ${y}%`;
	} else {
		position = `${props.config.angle}deg`;
	}

	gradient.push(`${props.config.type}-gradient(${position}, ${colors.join(', ')})`);
	style['background'] = gradient.join(', ');

	return style;
});
</script>

<style lang="scss" scoped>
@import '../../scss/_mixins.scss';
.znpb-gradient-preview-transparent {
	@extend %opacitybg;
	width: 100%;
	height: 100%;
}
</style>
