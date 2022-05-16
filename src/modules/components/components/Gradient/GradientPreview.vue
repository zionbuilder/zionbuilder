<template>
	<div class="znpb-gradient-preview-transparent" :class="{ 'gradient-type-rounded': round }">
		<div
			class="znpb-gradient-preview"
			:style="getGradientPreviewStyle"
			:class="{ 'gradient-type-rounded': round }"
		></div>
	</div>
</template>

<script lang="ts">
export default {
	name: 'GradientPreview',
};
</script>

<script lang="ts" setup>
import { computed, CSSProperties } from 'vue';
import { applyFilters } from '@zb/hooks';
import type { Gradient } from './GradientBar.vue';

const props = defineProps<{
	config?: Gradient[];
	round?: boolean;
}>();

const filteredConfig = computed<Gradient[]>(() => {
	return applyFilters('zionbuilder/options/model', JSON.parse(JSON.stringify(props.config)));
});

const getGradientPreviewStyle = computed<CSSProperties>(() => {
	const style: CSSProperties = {};
	const gradient: string[] = [];

	filteredConfig.value.forEach(element => {
		const colors: string[] = [];
		let position = '90deg';

		const colorsCopy = [...element.colors].sort((a, b) => {
			return a.position > b.position ? 1 : -1;
		});

		colorsCopy.forEach(color => {
			colors.push(`${color.color} ${color.position}%`);
		});

		// Set position
		if (element.type === 'radial') {
			const { x, y } = element.position || { x: 50, y: 50 };
			position = `circle at ${x}% ${y}%`;
		} else {
			position = `${element.angle}deg`;
		}

		gradient.push(`${element.type}-gradient(${position}, ${colors.join(', ')})`);
	});
	gradient.reverse();

	style['background-image'] = gradient.join(', ');

	return style;
});
</script>

<style lang="scss">
.znpb-gradient-preview-transparent {
	@extend %opacitybg;
	box-shadow: 0 0 0 2px var(--zb-surface-color) inset, 0 0 0 1px var(--zb-surface-color),
		0 0 2px var(--zb-surface-color);

	&.gradient-type-rounded {
		width: 46px;
		height: 46px;
		border-radius: 3px;
	}
}
.znpb-gradient-preview {
	width: 100%;
	height: 200px;
	border-radius: 0;
	&.gradient-type-rounded {
		width: 46px;
		height: 46px;
		margin-bottom: 18px;
		border-radius: 3px;
		cursor: pointer;
	}
}
</style>
