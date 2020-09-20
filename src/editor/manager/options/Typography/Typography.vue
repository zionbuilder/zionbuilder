<template>
	<OptionsForm
		:schema="typographySchema"
		v-model="valueModel"
		class="znpb-option__typography-wrapper"
	/>
</template>

<script>
import { getSchema } from '@zb/schemas'

export default {
	name: 'Typography',
	props: {
		value: {
			type: Object,
			default () {
				return {
					typographySchema: getSchema('typography')
				}
			}
		}
	},
	computed: {
		valueModel: {
			get () {
				return this.value || {}
			},
			set (newValue) {
				this.$emit('input', newValue)
			}
		}
	}
}
</script>

<style>
.znpb-options-form-wrapper.znpb-option__typography-wrapper {
	flex: 1;
	padding: 0;
}
</style>
