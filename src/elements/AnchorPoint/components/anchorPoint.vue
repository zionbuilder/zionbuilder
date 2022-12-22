<template>
	<div class="zb-anchorPoint">
		<slot name="start" />

		<!-- eslint-disable-next-line vue/no-v-html -->
		<span v-html="`#${getCssID}`"></span>
		<Icon icon="element-anchor-point" :size="30" color="#B2B2B2" />
		<slot name="end" />
	</div>
</template>

<script lang="ts" setup>
import { computed } from 'vue';

const props = defineProps<{
	options: {
		_advanced_options: {
			_element_id: string;
		};
	};
	element: ZionElement;
	api: ZionElementRenderApi;
}>();

const getCssID = computed(() => {
	return (props.options._advanced_options || {})._element_id || props.element.uid;
});
</script>
<style lang="scss">
.zb-anchorPoint {
	display: flex;
	justify-content: space-between;
	align-items: center;
	padding: 15px 20px;
	background-color: #f1f1f1;

	& > span {
		color: #858585;
		font-family: Roboto;
		font-size: 15px;
		font-style: normal;
		font-weight: 500;
		line-height: 18px;
	}
}
</style>
