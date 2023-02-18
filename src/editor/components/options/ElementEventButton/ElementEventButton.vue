<template>
	<div class="znpb-option__elementEvent" @click="onClick">
		{{ button_text }}
	</div>
</template>

<script lang="ts" setup>
import { useElementProvide } from '/@/editor/composables';

const props = defineProps<{
	event: string;
	// eslint-disable-next-line vue/prop-name-casing
	button_text: string;
}>();

const { injectElement } = useElementProvide();
const element = <ZionElement>injectElement();

function onClick() {
	element.trigger(props.event);
}
</script>
<style lang="scss">
.znpb-option__elementEvent {
	padding: 15px 37px;
	color: var(--zb-secondary-text-color);
	font-weight: 500;
	font-size: 13px;
	line-height: 1;
	text-align: center;
	background-color: var(--zb-secondary-color);
	border-radius: 3px;
	transition: all 0.3s;
	cursor: pointer;
	&:hover {
		background: var(--zb-secondary-hover-color);
	}
}
</style>
