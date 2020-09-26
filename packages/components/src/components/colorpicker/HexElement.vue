<template>
	<div class="znpb-colorpicker-inner-editor-hex">
		<InputLabel >
			<BaseInput
				v-model="hexValue"
				class="znpb-form-colorpicker__input-text"
			/>
			HEX
		</InputLabel>
	</div>
</template>
<script>
import BaseInput from '../forms/elements/input/BaseInput.vue'
import InputLabel from '../forms/elements/label/Label.vue'

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
		value: {
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
				return this.value
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
			font-family: $font-stack;
			font-size: 13px;
			text-align: center;
		}
	}
}
</style>
