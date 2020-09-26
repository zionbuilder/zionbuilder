<template>
	<Modal
		:show-close="false"
		:show-maximize="false"
		:show="true"
		append-to="body"
		:width="width"
	>
		<div class="znpb-modal__confirm">
			<!-- @slot Content that will be placed inside the modal body -->
			<slot></slot>
		</div>

		<div class="znpb-modal__confirm-buttons-wrapper">
			<Button
				type="danger"
				v-if="confirmText"
				@click="$emit('confirm')"
			>{{confirmText}}</Button>
			<Button
				type="gray"
				v-if="cancelText"
				@click="$emit('cancel')"
			>{{cancelText}}</Button>
		</div>
	</Modal>
</template>

<script>
import Modal from './Modal.vue'
import Button from '../BaseButton/BaseButton.vue'

export default {
	name: 'ModalConfirm',
	props: {
		/**
		 * confirmation text
		 */
		confirmText: {
			type: String,
			required: false,
			default: 'confirm'
		},
		/**
		 * cancel text
		 */
		cancelText: {
			type: String,
			required: false,
			default: 'cancel'
		},
		/**
		 * modal width
		 */
		width: {
			type: Number,
			required: false,
			default: 470
		}
	},
	data () {
		return {}
	},
	components: {
		Modal,
		Button
	}
}
</script>

<style lang="scss">
.znpb-modal__confirm {
	padding: 30px 30px 0;
	margin-bottom: 20px;
	color: $font-color;
	text-align: center;

	p {
		margin-top: 0;

		&:last-of-type {
			margin: 0;
		}
	}

	&-buttons-wrapper {
		display: flex;
		justify-content: center;
		align-items: center;
		padding: 0 30px 30px;

		.znpb-button {
			margin-right: 5px;
			text-align: center;

			&:last-child {
				margin-right: 0;
			}
		}
	}
}
</style>
