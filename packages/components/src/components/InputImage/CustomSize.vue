<template>
	<div
		class="znpb-forms-image-custom-size__wrapper"
	>
		<InputWrapper
			:title="$translate('custom_width')"
			align="center"
			class="znpb-forms-image-custom-size__option-wrapper"
		>
			<BaseInput
				v-model="width"
			/>
		</InputWrapper>
		<div class="znpb-forms-image-custom-size__option-separator">
			<Icon icon="close" :size="10" />
		</div>
		<InputWrapper
			:title="$translate('custom_height')"
			align="center"
			class="znpb-forms-image-custom-size__option-wrapper"
		>
			<BaseInput
				v-model="height"
			/>
		</InputWrapper>
		<div
			class="znpb-forms-image-custom-size__option-wrapper"
		>
			<button
				class="znpb-button znpb-button--line znpb-forms-image-custom-size__apply-button"
				@click="onCustomSizeClick"
			>Apply</button>
		</div>
	</div>
</template>

<script>
import { Icon } from '../Icon'
import BaseInput from '../BaseInput/BaseInput.vue'
import InputWrapper from '../inputWrapper/InputWrapper.vue'

export default {
	name: 'CustomSize',
	props: {
		modelValue: {
			type: Object
		}
	},
	data () {
		return {
			width: (this.modelValue || {}).width,
			height: (this.modelValue || {}).height
		}
	},
	components: {
		BaseInput,
		InputWrapper,
		Icon
	},
	methods: {
		onCustomSizeClick () {
			if (this.width || this.height) {
				this.$emit('update:modelValue', {
					width: this.width,
					height: this.height
				})
			}
		}
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
