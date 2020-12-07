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
	color: $surface;
	line-height: 1;
	text-align: center;
	background-color: $secondary;
	border-radius: 3px;
	transition: all .3s;
	cursor: pointer;
	&:hover {
		background: lighten($secondary, 2);
	}
}
</style>
