<template>
	<div class="znpb-colorpicker-inner-editor-rgba">
		<InputLabel >
			<InputNumber
				:value="value.r"
				:min="0"
				:max="255"
				:step="1"
				@input="updateValue('r', $event)"
			/>
			R
		</InputLabel>
		<InputLabel >
			<InputNumber
				:value="value.g"
				:min="0"
				:max="255"
				:step="1"
				@input="updateValue('g', $event)"
			/>
			G
		</InputLabel>
		<InputLabel >
			<InputNumber
				:value="value.b"
				:min="0"
				:max="255"
				:step="1"
				@input="updateValue('b', $event)"
			/>
			B
		</InputLabel>
		<InputLabel >
			<InputNumber
				:value="value.a"
				:min="0"
				:max="1"
				:step="0.01"
				@input="updateValue('a', $event)"
			/>
			A
		</InputLabel>
	</div>
</template>
<script>
/**
 * this type of element supports

 */
import { InputNumber } from '@/common/components/forms/elements/InputNumber'
import InputLabel from '@/common/components/forms/elements/label/Label.vue'
export default {
	name: 'RgbaElement',
	props: {
		value: {
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
			this.$emit('input', {
				...this.value,
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
			font-size: 13px;
			margin-bottom: 5px;
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
