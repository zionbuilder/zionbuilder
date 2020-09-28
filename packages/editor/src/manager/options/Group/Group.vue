<template>
	<OptionsForm
		v-if="child_options"
		class="znpb-option__type-option-group"
		:class="{
			[`znpb-option__type-option-group-layout--${optionsLayout}`]: optionsLayout
		}"
		:schema="child_options"
		v-model="valueModel"
	/>
</template>

<script>
export default {
	name: 'Group',
	props: {
		value: {},
		child_options: {
			type: Object,
			required: false
		},
		optionsLayout: {
			type: String,
			required: false
		}
	},
	data () {
		return {}
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

<style lang="scss">
.znpb-options-form-wrapper.znpb-option__type-option-group {
	flex-wrap: nowrap;
	padding: 0;
	margin: 0 -5px;
}
.znpb-option__type-option-group {
	&-layout {
		&--inline {
			display: flex;
			justify-content: space-between;
			align-items: flex-start;
		}

		&--full {
			display: flex;
			flex-direction: column;
			justify-content: space-between;
			align-items: flex-start;
		}
	}
}
</style>
