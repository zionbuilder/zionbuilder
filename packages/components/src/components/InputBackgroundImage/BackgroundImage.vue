<template>
	<div class="znpb-input-background-image">
		<InputImage
			:modelValue="computedValue['background-image']"
			:shouldDragImage="true"
			:position-top="backgroundPositionYModel"
			:position-left="backgroundPositionXModel"
			@update:modelValue="onOptionUpdated('background-image', $event)"
			@background-position-change="changeBackgroundPosition"
		/>

		<OptionsForm
			:schema="backgroundImageSchema"
			v-model="computedValue"
		/>
	</div>
</template>

<script>
import InputImage from '../InputImage/InputImage.vue'
import { useOptionsSchemas } from '@data/useOptionsSchemas'

export default {
	name: 'InputBackgroundImage',
	props: {
		modelValue: {}
	},
	components: {
		InputImage
	},
	setup () {
		const { getSchema } = useOptionsSchemas()
		const backgroundImageSchema = getSchema('backgroundImageSchema')
		return {
			getSchema,
			backgroundImageSchema
		}
	},
	computed: {
		computedValue: {
			get () {
				return this.modelValue || {}
			},
			set (newValue) {
				this.$emit('update:modelValue', newValue)
			}
		},
		backgroundPositionXModel () {
			return this.computedValue['background-position-x']
		},
		backgroundPositionYModel () {
			return this.computedValue['background-position-y']
		}
	},
	methods: {
		changeBackgroundPosition (event) {
			this.$emit('update:modelValue', {
				...this.computedValue,
				'background-position-x': `${event.x}%`,
				'background-position-y': `${event.y}%`
			})
		},
		onOptionUpdated (optionId, newValue) {
			const newValues = {
				...this.modelValue
			}

			newValues[optionId] = newValue
			this.computedValue = newValues
		}
	}
}
</script>

<style lang="scss">
.znpb-input-background-image {
	&-position-wrapper {
		display: flex;

		label {
			&:nth-child(1) {
				margin-right: 15px;
			}
		}
	}
}
</style>
