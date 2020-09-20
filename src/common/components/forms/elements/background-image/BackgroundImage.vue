<template>
	<div class="znpb-input-background-image">
		<InputImage
			:value="valueModel['background-image']"
			:shouldDragImage="true"
			:position-top="backgroundPositionYModel"
			:position-left="backgroundPositionXModel"
			@input="onOptionUpdated('background-image', $event)"
			@background-position-change="changeBackgroundPosition"
		/>

		<OptionsForm
			:schema="backgroundImageSchema"
			v-model="valueModel"
		/>
	</div>
</template>

<script>
import InputImage from '../featured-image/InputImage'
import { getSchema } from '@zb/schemas'

export default {
	name: 'InputBackgroundImage',
	props: {
		value: {}
	},
	components: {
		InputImage
	},
	data () {
		return {
			backgroundImageSchema: getSchema('background_image')
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
		},
		backgroundPositionXModel () {
			return this.valueModel['background-position-x']
		},
		backgroundPositionYModel () {
			return this.valueModel['background-position-y']
		}
	},
	methods: {
		changeBackgroundPosition (event) {
			this.$emit('input', {
				...this.valueModel,
				'background-position-x': `${event.x}%`,
				'background-position-y': `${event.y}%`
			})
		},
		onOptionUpdated (optionId, newValue) {
			const newValues = {
				...this.value
			}

			newValues[optionId] = newValue
			this.valueModel = newValues
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
