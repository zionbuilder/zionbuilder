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
			<BaseIcon icon="close" :size="10" />
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
import BaseIcon from '@/common/components/BaseIcon.vue'
import BaseInput from '@/common/components/forms/elements/input/BaseInput.vue'
import InputWrapper from '@/common/components/forms/elements/inputWrapper/InputWrapper.vue'

export default {
	name: 'CustomSize',
	props: {
		value: {
			type: Object
		}
	},
	data () {
		return {
			width: (this.value || {}).width,
			height: (this.value || {}).height
		}
	},
	components: {
		BaseInput,
		InputWrapper,
		BaseIcon
	},
	methods: {
		onCustomSizeClick () {
			if (this.width || this.height) {
				this.$emit('input', {
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
