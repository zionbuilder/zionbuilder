<template>
	<div class="znpb-colorpicker-inner-editor-hex">
		<InputLabel>
			<BaseInput
				v-model="hexValue"
				class="znpb-form-colorpicker__input-text"
			/>
			HEX
		</InputLabel>
	</div>
</template>
<script>
import BaseInput from '../BaseInput/BaseInput.vue'
import { InputLabel } from '../InputLabel'

/**
 * this type of element supports
 */
export default {
	name: 'HexElement',
	components: {
		BaseInput,
		InputLabel
	},
	props: {
		modelValue: {
			type: String,
			required: false
		}
	},
	data () {
		return {}
	},
	computed: {
		hexValue: {
			get () {
				return this.modelValue
			},
			set (newValue) {
				this.$emit('update:modelValue', newValue)
			}
		}
	}
}
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
		color: $font-color;
		font-size: 10px;
		font-weight: 500;
	}

	.zion-input {
		margin-bottom: 5px;

		input {
			padding: 8px 0;
			font-family: var(--font-stack);
			font-size: 13px;
			text-align: center;
		}
	}
}
</style>
