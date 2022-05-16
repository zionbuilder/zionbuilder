<template>
	<div class="znpb-colorpicker-inner-editor-hex">
		<InputLabel>
			<BaseInput v-model="hexValue" class="znpb-form-colorpicker__input-text" />
			HEX
		</InputLabel>
	</div>
</template>

<script lang="ts" setup>
import BaseInput from '../BaseInput/BaseInput.vue';
import { InputLabel } from '../InputLabel';
import { computed } from 'vue';

const props = defineProps<{
	modelValue: string;
}>();

const emit = defineEmits<{
	(e: 'update:modelValue', value: string): void;
}>();

const hexValue = computed({
	get() {
		return props.modelValue;
	},
	set(newValue: string) {
		emit('update:modelValue', newValue);
	},
});
</script>

<style lang="scss">
.znpb-colorpicker-inner-editor-hex {
	flex-grow: 1;
	line-height: 1;

	.znpb-form-label {
		display: flex;
		align-items: center;
		width: 100%;
		margin: 0 auto;
		margin-bottom: 0;
		color: var(--zb-surface-text-color);
		font-size: 10px;
		font-weight: 500;
	}

	.zion-input {
		margin-bottom: 5px;

		input {
			padding: 8px 0;
			font-family: var(--zb-font-stack);
			font-size: 13px;
			text-align: center;
		}
	}
}
</style>
