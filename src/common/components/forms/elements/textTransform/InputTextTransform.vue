<template>
	<div class="znpb-input-text-transform__wrapper"
		:class="`znpb-input-text-transform__wrapper--${layout}`"
	>

		<RadioGroup
			layout="row"
		>
			<RadioIconItem
				class="zion-form-text-align_element"
				v-model="radioSavedValue"
				v-for="(option, index) in radioOptions"
				:key="index"
				:option-value="option.value"
				:icon="option.icon"
			>
			</RadioIconItem>
		</RadioGroup>
	</div>
</template>

<script>
import RadioIconItem from '../radio/RadioIconItem'
import RadioGroup from '../radio/RadioGroup'
export default {
	name: 'InputTextTransform',
	components: {
		RadioIconItem,
		RadioGroup
	},
	props: {
		/**
		* Value of text transform
		*/
		value: {
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
				return this.value
			},
			set (newValue) {
				/**
				 * Emits newValue
				*/
				this.$emit('input', newValue)
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
