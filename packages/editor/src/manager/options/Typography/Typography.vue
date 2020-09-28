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
		value: {
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
