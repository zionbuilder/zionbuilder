<template>
	<div class="zb-el-iconBox">
		<slot name="start" />

		<div v-if="options.icon" class="zb-el-iconBox-iconWrapper">
			<ElementIcon
				class="zb-el-iconBox-icon"
				:class="api.getStyleClasses('icon_styles')"
				:icon-config="options.icon"
				v-bind="api.getAttributesForTag('icon_styles')"
			/>
		</div>
		<span class="zb-el-iconBox-spacer" v-bind="api.getAttributesForTag('spacer')"></span>
		<div class="zb-el-iconBox-text">
			<component
				:is="titleTag"
				v-if="options.title"
				class="zb-el-iconBox-title"
				:class="api.getStyleClasses('title_styles')"
				v-bind="api.getAttributesForTag('title_styles')"
				v-html="options.title"
			/>

			<div
				v-if="options.description"
				class="zb-el-iconBox-description"
				:class="api.getStyleClasses('description_styles')"
				v-bind="api.getAttributesForTag('description_styles')"
			>
				<RenderValue option="description" />
			</div>
		</div>

		<slot name="end" />
	</div>
</template>

<script lang="ts" setup>
import { computed } from 'vue';

const props = defineProps<{
	options: {
		icon: {
			type: string;
			value: string;
		};
		title: string;
		title_tag: string;
		description: string;
	};
	element: ZionElement;
	api: ZionElementRenderApi;
}>();

const titleTag = computed(() => {
	return props.options.title_tag || 'h3';
});
</script>
