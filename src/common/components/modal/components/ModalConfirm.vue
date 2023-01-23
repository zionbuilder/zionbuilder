<template>
	<Modal :show-close="false" :show-maximize="false" :show="true" append-to="body" :width="width">
		<div class="znpb-modal__confirm">
			<!-- @slot Content that will be placed inside the modal body -->
			<slot></slot>
		</div>

		<div class="znpb-modal__confirm-buttons-wrapper">
			<Button v-if="confirmText" type="danger" @click="emit('confirm')">{{ confirmText }}</Button>
			<Button v-if="cancelText" type="gray" @click="emit('cancel')">{{ cancelText }}</Button>
		</div>
	</Modal>
</template>

<script lang="ts" setup>
import Modal from './Modal.vue';
import { Button } from '../../Button';

withDefaults(
	defineProps<{
		confirmText?: string;
		cancelText?: string;
		width?: number;
	}>(),
	{
		confirmText: 'confirm',
		cancelText: 'cancel',
		width: 470,
	},
);

const emit = defineEmits(['confirm', 'cancel']);
</script>

<style lang="scss">
.znpb-modal__confirm {
	padding: 30px 30px 0;
	margin-bottom: 20px;
	color: var(--zb-surface-text-color);
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
