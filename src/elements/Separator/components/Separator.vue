<template>
	<div>
		<slot name="start" />

		<div
			v-if="!options.use_icon"
			class="zb-el-zionSeparator-item zb-el-zionSeparator-item--size"
			v-bind="api.getAttributesForTag('separator_item')"
		/>

		<div v-else class="zb-el-zionSeparator-item-icon zb-el-zionSeparator-item--size">
			<span class="zb-el-zionSeparator-item zb-el-zionSeparator-icon-line zb-el-zionSeparator-icon-line-one"></span>
			<ElementIcon class="zb-el-zionSeparator-icon" :icon-config="iconConfig" />
			<span class="zb-el-zionSeparator-item zb-el-zionSeparator-icon-line zb-el-zionSeparator-icon-line-two"></span>
		</div>

		<slot name="end" />
	</div>
</template>

<script lang="ts" setup>
import { computed } from 'vue';

const props = defineProps<{
	options: {
		use_icon: boolean;
		icon: {
			family: string;
			name: string;
			unicode: string;
		};
	};
	element: ZionElement;
	api: ZionElementRenderApi;
}>();

const iconConfig = computed(() => {
	return (
		props.options.icon || {
			family: 'Font Awesome 5 Free Regular',
			name: 'star',
			unicode: 'uf005',
		}
	);
});
</script>
