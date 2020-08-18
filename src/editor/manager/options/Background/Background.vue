<template>
	<Tabs
		tab-style="group"
		class="znpb-background-option-tabs"
		title-position="center"
	>
		<Tab name="background-color">
			<BaseIcon
				icon="drop"
				slot="title"
			/>

			<InputWrapper
				:schema="bgColorSchema"
				:option-id="bgColorSchema.id"
				:value="valueModel['background-color']"
				:delete-value="onDeleteOption"
				@input="onOptionUpdate(...$event)"
			/>

		</Tab>
		<Tab name="background-gradient">
			<BaseIcon
				icon="gradient"
				slot="title"
			/>
			<InputWrapper
				:schema="bgGradientSchema"
				:option-id="bgGradientSchema.id"
				:value="valueModel['background-gradient']"
				:delete-value="onDeleteOption"
				@input="onOptionUpdate(...$event)"
			/>
		</Tab>
		<Tab name="background-image">
			<BaseIcon
				slot="title"
				icon="picture"
			/>
			<InputBackgroundImage
				class="znpb-input__background-image"
				:value="valueModel"
				@input="onOptionUpdate(false, $event)"
			/>
		</Tab>
		<Tab
			name="background-video"
			v-if="canShowBackground"
		>
			<BaseIcon
				slot="title"
				icon="video"
			/>
			<InputBackgroundVideo
				class="znpb-input__background-video"
				:value="valueModel['background-video']"
				@input="onOptionUpdate('background-video', $event)"
			/>
		</Tab>
	</Tabs>
</template>

<script>
import { mapGetters } from 'vuex'
import { InputBackgroundImage, InputBackgroundVideo } from '@/common/components/forms'

export default {
	name: 'Background',
	components: {
		InputBackgroundImage,
		InputBackgroundVideo,
		InputWrapper: () => import('@/editor/components/elementOptions/forms/InputWrapper')
	},
	inject: {
		panel: {
			default: null
		}
	},
	props: {
		value: {}
	},
	data () {
		return {
			bgColorSchema: {
				id: 'background-color',
				type: 'background_color'
			},
			bgGradientSchema: {
				id: 'background-gradient',
				type: 'background_gradient'
			}
		}
	},
	computed: {
		...mapGetters([
			'getActiveDevice',
			'getActivePseudoSelector'
		]),
		valueModel: {
			get () {
				return this.value || {}
			},
			set (newValue) {
				this.$emit('input', newValue)
			}
		},
		// only show bg video on desktop
		canShowBackground () {
			return this.getActiveDevice.id === 'default' && this.getActivePseudoSelector.id === 'default'
		}
	},
	methods: {
		onDeleteOption (optionId) {
			const newValues = {
				...this.value
			}

			delete newValues[optionId]
			this.valueModel = newValues
		},
		onOptionUpdate (optionId, newValue) {
			const clonedValue = { ...this.value }
			if (optionId) {
				if (newValue === null) {
					// If this is used as layout, we need to delete the active pseudo selector
					delete clonedValue[optionId]
				} else {
					clonedValue[optionId] = newValue
				}

				this.valueModel = clonedValue
			} else {
				if (newValue === null) {
					this.$emit('input', null)
				} else {
					this.valueModel = newValue
				}
			}
		}
	}
}
</script>

<style lang="scss">
.znpb-background-option-tabs {
	width: 100%;

	.znpb-tabs__header {
		&-item {
			display: flex;
			justify-content: center;
			align-items: center;
			flex-grow: 1;

			&--active {
				color: #fff;
				background: $secondary;
			}
		}
	}
	.znpb-input__background-image {
		.znpb-forms-input-wrapper {
			padding: 0;
		}
	}
	.znpb-input__background-video {
		.znpb-forms-input-wrapper {
			padding: 0;
		}
	}
}
</style>
