<template>
	<div class="znpb-input-text-transform__wrapper"
		:class="`znpb-input-text-transform__wrapper--${layout}`"
	>

		<InputRadioGroup
			layout="row"
		>
			<InputRadio
				class="zion-form-text-align_element"
				v-model="radioSavedValue"
				v-for="(option, index) in radioOptions"
				:key="index"
				:option-value="option.value"
				:icon="option.icon"
			>
			</InputRadio>
		</InputRadioGroup>
	</div>
</template>

<script>
import { InputRadio, InputRadioGroup } from '../InputRadio'

export default {
	name: 'InputTextTransform',
	components: {
		InputRadio,
		InputRadioGroup
	},
	props: {
		/**
		* Value of text transform
		*/
		modelValue: {
			type: String,
			required: false
		},
		/**
		* Layout of text transform
		*/
		layout: {
			type: String
		}
	},
	computed: {
		radioSavedValue: {
			get () {
				return this.modelValue
			},
			set (newValue) {
				/**
				 * Emits newValue
				*/
				this.$emit('update:modelValue', newValue)
			}
		}
	},
	data () {
		return {
			radioOptions: [
				{
					value: 'uppercase',
					icon: 'uppercase'
				},
				{
					value: 'lowercase',
					icon: 'lowercase'
				},
				{
					value: 'capitalize',
					icon: 'capitalize'
				}
			]
		}
	}
}
</script>
<style lang="scss">
.znpb-input-text-transform {
	&__wrapper {
		&--inline {
			.zion-radio-group {
				display: flex;
			}
		}
	}
}
</style>
