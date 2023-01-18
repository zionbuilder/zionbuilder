<template>
	<div class="znpb-modal-content-save-button">
		<div class="znpb-modal-content-wrapper znpb-fancy-scrollbar">
			<slot></slot>
		</div>
		<div class="znpb-modal-content-save-button__button">
			<Button :type="buttonType" @click="onButtonClick">{{ __('Save', 'zionbuilder') }} </Button>
		</div>
	</div>
</template>

<script lang="ts" setup>
import { __ } from '@wordpress/i18n';
import { Button } from '../../Button';
import { computed } from 'vue';

const props = withDefaults(
	defineProps<{
		disabled?: boolean;
	}>(),
	{
		disabled: false,
	},
);

const emit = defineEmits(['save-modal']);

const buttonType = computed(() => {
	return props.disabled ? 'gray' : 'secondary';
});

function onButtonClick() {
	if (!props.disabled) {
		emit('save-modal');
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
		border-top: 1px solid var(--zb-surface-border-color);
	}
}
</style>
