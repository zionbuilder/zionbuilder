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
import { computed } from 'vue'
import { useOptionsSchemas } from '@composables/useOptionsSchemas'

export default {
	name: 'InputTextShadow',
	props: {
		/**
		* Value of text shadow
		*/
		modelValue: {
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
	setup (props, { emit }) {
		const { getSchema } = useOptionsSchemas()
		const schema = computed(() => {
			if (props.shadow_type === 'text-shadow') {
				const { inset, spread, ...shadowSchema } = getSchema('shadowSchema')
				return shadowSchema
			}

			return getSchema('shadowSchema')
		})

		const valueModel = computed({
			get () {
				return props.modelValue || {}
			},
			set (newValue) {
				emit('update:modelValue', newValue)
			}
		})
		return {
			getSchema,
			schema,
			valueModel
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
