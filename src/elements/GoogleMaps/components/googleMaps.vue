<template>
	<div>
		<slot name="start" />

		<iframe
			:src="`https://www.google.com/maps?api=1&q=${location}&z=${zoom}&output=embed&t=${mapType}`"
			frameborder="0"
			style="border: 0; margin-bottom: 0"
			allowfullscreen="true"
		></iframe>

		<slot name="end" />
	</div>
</template>

<script lang="ts" setup>
import { computed } from 'vue';

const props = defineProps<{
	options: {
		location?: string;
		zoom?: number;
		map_type?: 'roadmap' | 'terrain';
	};
	element: ZionElement;
	api: ZionElementRenderApi;
}>();

const location = computed(() => {
	return encodeURIComponent(props.options.location || 'Chicago');
});

const zoom = computed(() => {
	return props.options.zoom || 15;
});

const mapType = computed(() => {
	return props.options.map_type === 'terrain' ? 'k' : '';
});
</script>
