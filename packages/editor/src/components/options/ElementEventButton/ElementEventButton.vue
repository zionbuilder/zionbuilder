<template>
	<div
		class="znpb-option__elementEvent"
		@click="onClick"
	>
		{{buttonText}}
	</div>
</template>

<script>
import { inject } from 'vue'
import { useElementProvide } from '@composables'
export default {
	name: 'ElementEventButton',
	props: {
		event: {
			type: String,
			required: true
		},
		button_text: {
			type: String,
			required: true
		}
	},
	setup (props) {
		const { injectElement } = useElementProvide()
		const element = injectElement()

		function onClick () {
			element.value.trigger(props.event)
		}

		return {
			buttonText: props.button_text,
			onClick
		}
	}
}
</script>
<style lang="scss">
.znpb-option__elementEvent {
	padding: 15px 37px;
	color: var(--zb-secondary-text-color);
	font-weight: 500;
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
