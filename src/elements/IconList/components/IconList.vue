<template>
	<div>
		<slot name="start" />

		<component
			:is="item.link && item.link.link ? 'a' : 'span'"
			v-for="(item, index) in iconListConfig"
			:key="index"
			class="zb-el-iconList__item"
			:class="[`zb-el-iconList__item--${index} `, api.getStyleClasses('item_styles')]"
			v-bind="api.getAttributesForTag('item_styles')"
		>
			<ElementIcon
				class="zb-el-iconList__itemIcon"
				:class="api.getStyleClasses('icon_styles')"
				v-bind="api.getAttributesForTag('icon_styles')"
				:icon-config="item.icon"
			/>

			<span
				v-if="item.text"
				class="zb-el-iconList__itemText"
				:class="api.getStyleClasses('text_styles')"
				v-bind="api.getAttributesForTag('text_styles')"
				v-html="item.text"
			/>
		</component>

		<slot name="end" />
	</div>
</template>

<script lang="ts" setup>
import { computed } from 'vue';

const props = defineProps<{
	options: {
		icons: {
			icon: {
				type: string;
				value: string;
			};
			text: string;
			link: {
				link: string;
				target: string;
			};
		}[];
	};
	element: ZionElement;
	api: ZionElementRenderApi;
}>();

const iconListConfig = computed(() => {
	return props.options.icons ? props.options.icons : [];
});
</script>
