<template>
	<div
		class="znpb-shadow-option-wrapper__outer"
		:class="`znpb-shadow-option--${shadow_type}`"
	>
		<OptionsForm
			:schema="schema"
			v-model="valueModel"
			class="znpb-shadow-option"
		/>
	</div>
</template>
<script>
import { getSchema } from '@zb/schemas'

export default {
	name: 'InputTextShadow',
	props: {
		/**
		* Value of text shadow
		*/
		value: {
			type: Object,
			required: false,
			default () {
				return {}
			}
		},
		/**
		* Boolean if the shadow is inset or not
		*/
		inset: {
			type: Boolean,
			required: false,
			default: false
		},
		/**
		* String represanting the shadow type
		*/
		shadow_type: {
			type: String,
			required: false,
			default: 'text-shadow'
		}
	},
	computed: {
		schema () {
			if (this.shadow_type === 'text-shadow') {
				const { inset, spread, ...shadowSchema } = getSchema('shadow')
				return shadowSchema
			}

			return getSchema('shadow')
		},
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
.znpb-shadow-option {
	display: flex;
	justify-content: space-between;
	align-items: flex-start;

	.znpb-input-wrapper {
		padding-bottom: 10px;
	}

	.znpb-options-form-wrapper & {
		padding: 0;
		margin: 0 -5px;
	}

	&--text-shadow {
		.znpb-shadow-option__colorpicker.znpb-input-wrapper {
			width: 100% !important;

			 > * {
				width: calc(50% - 5px);
			}
		}
	}

	&--ptop.znpb-input-wrapper {
		padding-top: 10px;
	}
}
</style>
