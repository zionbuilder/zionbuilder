<template>
	<component :is="options.tag || 'h1'">
		<slot name="start" />

		<a
			v-if="options.link && options.link.link"
			:href="options.link.link"
			:title="options.link.title"
			:target="options.link.target"
			@click.prevent="
				e => {
					e.preventDefault();
				}
			"
		>
			<RenderValue option="content" :forced-root-node="false" />
		</a>

		<RenderValue v-else option="content" :forced-root-node="false" />

		<slot name="end" />
	</component>
</template>

<script lang="ts" setup>
defineProps<{
	options: {
		content: string;
		tag?: string;
		link?: {
			link: string;
			target: string;
			title: string;
		};
	};
	element: ZionElement;
	api: ZionElementRenderApi;
}>();
</script>
