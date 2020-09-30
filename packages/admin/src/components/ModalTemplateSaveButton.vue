<template>
	<div class="znpb-modal-content-save-button">
		<div class="znpb-modal-content-wrapper znpb-fancy-scrollbar">
			<slot></slot>
		</div>
		<div class="znpb-modal-content-save-button__button">
			<Button
				@click="onButtonClick"
				:type="buttonType"
			>{{$translate('save')}}
			</Button>
		</div>
	</div>

</template>
<script>
import { Button } from '@zionbuilder/components'

export default {
	name: 'ModalTemplateSaveButton',
	components: {
		Button
	},
	props: ['disabled'],
	data () {
		return {}
	},
	computed: {
		buttonType () {
			return this.disabled ? 'gray' : 'secondary'
		}
	},
	methods: {
		onButtonClick () {
			if (!this.disabled) {
				this.$emit('save-modal')
			}
		}
	}
}
</script>
<style lang="scss">
.znpb-modal-content-save-button {
	display: flex;
	flex-direction: column;
	min-height: 0;

	&__button {
		padding: 15px 0;
		text-align: center;
		border-top: 1px solid $surface-variant;
	}
}
</style>
