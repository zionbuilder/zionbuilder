<template>
	<div class="znpb-forms-image-custom-size__wrapper">
		<InputWrapper
			:title="i18n.__('Custom Width', 'zionbuilder')"
			align="center"
			class="znpb-forms-image-custom-size__option-wrapper"
		>
			<BaseInput v-model="width" />
		</InputWrapper>
		<div class="znpb-forms-image-custom-size__option-separator">
			<Icon icon="close" :size="10" />
		</div>
		<InputWrapper
			:title="i18n.__('Custom Height', 'zionbuilder')"
			align="center"
			class="znpb-forms-image-custom-size__option-wrapper"
		>
			<BaseInput v-model="height" />
		</InputWrapper>
		<div class="znpb-forms-image-custom-size__option-wrapper">
			<button
				class="znpb-button znpb-button--line znpb-forms-image-custom-size__apply-button"
				@click="onCustomSizeClick"
			>
				{{ i18n.__('Apply', 'zionbuilder') }}
			</button>
		</div>
	</div>
</template>

<script lang="ts">
export default {
	name: 'CustomSize',
	data() {
		return {};
	},
	methods: {},
};
</script>

<script lang="ts" setup>
import * as i18n from '@wordpress/i18n';
import { ref } from 'vue';
import { Icon } from '../Icon';
import BaseInput from '../BaseInput/BaseInput.vue';
import InputWrapper from '../InputWrapper/InputWrapper.vue';

const props = defineProps<{
	modelValue?: {
		width?: string;
		height?: string;
	};
}>();

const emit = defineEmits<{
	(e: 'update:modelValue', value: { width: string; height: string }): void;
}>();

const width = ref(props.modelValue?.width);
const height = ref(props.modelValue?.height);

function onCustomSizeClick() {
	if (width.value || height.value) {
		emit('update:modelValue', {
			width: width.value || '',
			height: height.value || '',
		});
	}
}
</script>

<style lang="scss">
.znpb-forms-image-custom-size {
	&__wrapper {
		display: flex;
		align-items: flex-end;
	}

	&__option-wrapper {
		padding-right: 5px;

		&:last-child {
			padding-right: 0;
		}
	}

	&__option-separator {
		display: flex;
		justify-content: center;
		align-items: center;
		height: 40px;
		margin: 0 5px;
	}

	&__apply-button {
		margin-left: 10px;
	}
}
</style>
