<template>
	<div ref="root" class="zb-el-counter">
		<slot name="start" />

		<div
			v-if="options.before"
			class="zb-el-counter__before"
			v-bind="api.getAttributesForTag('before_text_styles')"
			:class="api.getStyleClasses('before_text_styles')"
		>
			{{ options.before }}
		</div>

		<div class="zb-el-counter__number">{{ options.start }}</div>

		<div
			v-if="options.after"
			class="zb-el-counter__after"
			v-bind="api.getAttributesForTag('after_text_styles')"
			:class="api.getStyleClasses('after_text_styles')"
		>
			{{ options.after }}
		</div>

		<slot name="end" />
	</div>
</template>

<script lang="ts" setup>
import { ref, watch, onMounted } from 'vue';

const props = defineProps<{
	options: {
		start: number;
		end: number;
		duration: number;
		before: string;
		after: string;
	};
	element: ZionElement;
	api: ZionElementRenderApi;
}>();

const root = ref(null);

onMounted(() => {
	runScript();
});

watch(
	() => [props.options.start, props.options.end, props.options.duration].toString(),
	() => {
		runScript();
	},
);

function runScript() {
	if (root.value) {
		new window.zbScripts.counter(root.value);
	}
}
</script>
