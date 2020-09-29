<template>
	<OptionsForm
		:schema="getTypographyOptionSchema"
		v-model="valueModel"
		class="znpb-option__typography-wrapper"
	/>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
	name: 'Typography',
	props: {
		modelValue: {
			type: Object,
			default () {
				return {}
			}
		}
	},
	computed: {
		...mapGetters([
			'getTypographyOptionSchema'
		]),
		valueModel: {
			get () {
				return this.modelValue || {}
			},
			set (newValue) {
				this.$emit('update:modelValue', newValue)
			}
		}
	}
}
</script>

<style lang="scss">
.znpb-options-form-wrapper.znpb-option__typography-wrapper {
	flex: 1;
	padding: 0;
}
</style>
