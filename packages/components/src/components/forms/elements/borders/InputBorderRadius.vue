<template>
	<div class="znpb-input-border-radius-wrapper">
		<InputLabel
			v-if="title"
			:label="title"
			class="znpb-typography-group-item znpb-typography-group-item-font-weight"
		>
			<InputNumberUnit
				v-model="widthValue"
				:min="0"
				:max="999"
				:units="['px', 'rem', 'pt', 'vh', '%']"
				:step="1"
				default-unit="px"
			/>
		</InputLabel>
	</div>
</template>
<script>
import InputNumberUnit from '../InputNumber/InputNumberUnit.vue'

export default {
	name: 'InputBorderRadius',
	props: {
		/**
		 * v-model/value for border radius
		 */
		modelValue: {
			default () {
				return null
			},
			type: String,
			required: false
		}

	},
	components: {
		InputNumberUnit
	},
	data () {
		return {

		}
	},
	computed: {
		widthValue: {
			get () {
				return this.modelValue
			},
			set (newValue) {
				/**
				 * emits new value for border radius input
				 */
				this.$emit('modelValue', newValue)
			}
		}
	}
}
</script>
<style lang="scss">
.znpb-input-border-radius-wrapper {
	margin-bottom: 15px;
	.znpb-form-label {
		display: flex;
		flex-direction: row-reverse;
		justify-content: space-between;
	}
}
</style>
