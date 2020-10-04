<template>
	<div class="znpb-colorpicker-inner-editor-rgba">
		<InputLabel >
			<InputNumber
				:modelValue="modelValue.r"
				:min="0"
				:max="255"
				:step="1"
				@update:modelValue="updateValue('r', $event)"
			/>
			R
		</InputLabel>
		<InputLabel >
			<InputNumber
				:modelValue="modelValue.g"
				:min="0"
				:max="255"
				:step="1"
				@update:modelValue="updateValue('g', $event)"
			/>
			G
		</InputLabel>
		<InputLabel >
			<InputNumber
				:modelValue="modelValue.b"
				:min="0"
				:max="255"
				:step="1"
				@update:modelValue="updateValue('b', $event)"
			/>
			B
		</InputLabel>
		<InputLabel >
			<InputNumber
				:modelValue="modelValue.a"
				:min="0"
				:max="1"
				:step="0.01"
				@update:modelValue="updateValue('a', $event)"
			/>
			A
		</InputLabel>
	</div>
</template>
<script>
import { InputNumber, InputLabel } from '../forms'

export default {
	name: 'RgbaElement',
	props: {
		modelValue: {
			type: Object,
			required: false
		}
	},
	components: {
		InputLabel,
		InputNumber
	},
	data () {
		return {
			localrgba: this.rgba
		}
	},
	computed: {

	},
	methods: {
		updateValue (property, newValue) {
			this.$emit('update:modelValue', {
				...this.modelValue,
				[property]: newValue
			})
		}
	}
}
</script>
<style lang="scss">
.znpb-colorpicker-inner-editor-rgba, .znpb-colorpicker-inner-editor-hsla {
	display: flex;
	justify-content: space-between;
	width: 100%;

	.znpb-form-label {
		align-items: center;
		flex: 1;
		margin-right: 5px;
		margin-bottom: 0;
		color: $font-color;
		font-size: 10px;
		font-weight: 500;
		line-height: 1;

		&:last-child {
			margin-right: 0;
		}

		.znpb-input-number {
			position: relative;
			width: 100%;
			margin-bottom: 5px;
			font-size: 13px;
		}

		input {
			padding: 8px 0;
			font-size: 13px;
			text-align: center;
			box-shadow: none;
		}

		.zion-input__suffix {
			margin-right: 0;
		}
	}
}
</style>
