<template>
	<div
		class="znpb-shape-divider-icon zb-mask"
		:class="[position === 'top' ? 'zb-mask-pos--top' : 'zb-mask-pos--bottom', flip ? 'zb-mask-pos--flip' : '']"
		:style="{ color: color }"
		v-html="getSvgIcon"
	></div>
</template>

<script lang="ts">
export default {
	name: 'SvgMask',
};
</script>

<script lang="ts" setup>
import { computed, ref, watch, inject, onMounted } from 'vue';

const props = defineProps<{
	shapePath: string;
	position?: string;
	color?: string;
	flip?: boolean;
}>();

const masks = inject('masks') as Record<string, { path: string; url: string }>;

const svgData = ref('');

const getSvgIcon = computed(() => svgData.value);

watch(
	() => props.shapePath,
	newValue => {
		getFile(newValue);
	},
);

function getFile(shapePath: string) {
	let url;
	if (shapePath.includes('.svg')) {
		url = shapePath;
	} else {
		const shapeConfig = masks[shapePath];
		url = shapeConfig.url;
	}

	fetch(url)
		.then(response => response.text())
		.then(svgFile => {
			svgData.value = svgFile;
		})
		.catch(error => {
			console.error(error);
		});
}

onMounted(() => {
	if (props.shapePath !== undefined && props.shapePath.length) {
		getFile(props.shapePath);
	}
});
</script>
