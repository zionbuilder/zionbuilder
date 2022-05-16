<template>
	<div class="znpb-gradient-preview-transparent-container" :title="$translate('click_to_add_gradient_point')">
		<div class="znpb-gradient-preview-transparent">
			<div class="znpb-gradient-preview" :style="getGradientPreviewStyle"></div>
		</div>
	</div>
</template>

<script lang="ts">
export default {
	name: 'GradientBarPreview',
};
</script>

<script lang="ts" setup>
import { computed, CSSProperties } from 'vue';
import type { Gradient } from './GradientBar.vue';

const props = defineProps<{
	config: Gradient;
}>();

const getGradientPreviewStyle = computed(() => {
	const style: CSSProperties = {};
	const gradient = [];
	const colors: string[] = [];

	const colorsCopy = [...props.config.colors];

	colorsCopy.sort((a, b) => {
		return a.position > b.position ? 1 : -1;
	});

	colorsCopy.forEach(color => {
		colors.push(`${color.color} ${color.position}%`);
	});

	gradient.push(`linear-gradient(90deg, ${colors.join(', ')})`);

	style['background'] = gradient.join(', ');

	return style;
});
</script>
<style lang="scss">
.znpb-gradient-preview-transparent-container {
	margin-bottom: 25px;
	.znpb-gradient-preview-transparent {
		@extend %opacitybg;
		box-shadow: none;
	}
}
</style>
