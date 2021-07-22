<template>
	<Color
		v-model="colorModel"
		@open="$emit('open')"
		@close="$emit('close')"
	>
		<template v-slot:trigger>
			<div class="znpb-style-background-color">
				<EmptyList
					class="znpb-input-background-image__empty"
					v-if="! modelValue"
					:no-margin="true"
				>
					{{$translate('select_background_color')}}
				</EmptyList>
				<ActionsOverlay v-if="modelValue">
					<div
						class="znpb-style-background-color__holder"
						:style="getColorStyle"
					></div>
					<template v-slot:actions>
						<div>
							<Icon
								:rounded="true"
								icon="delete"
								:bg-size="30"
								@click.stop="deleteColor"
							/>
						</div>
					</template>
				</ActionsOverlay>

			</div>
		</template>
	</Color>
</template>

<script>
export default {
	name: 'BackgroundColor',
	props: {
		modelValue: {
			required: false
		}
	},
	data () {
		return {
			showColorPicker: false,
			preventNextClick: false,
			isDragging: false
		}
	},
	computed: {
		colorModel: {
			get () {
				let computedValue = null
				if (this.modelValue !== undefined) {
					if (typeof (this.modelValue) === 'string') {
						computedValue = this.modelValue
					} else computedValue = this.modelValue.value
				}

				return computedValue !== null ? computedValue : ''
			},
			set (newColor) {
				this.$emit('update:modelValue', newColor)
			}
		},
		getColorStyle () {
			return {
				'background-color': this.colorModel
			}
		}
	},
	methods: {
		deleteColor () {
			this.$emit('update:modelValue', null)
		}
	}
}
</script>

<style lang="scss">
.znpb-style-background-color {
	margin-bottom: 20px;
	cursor: pointer;

	&__holder {
		display: block;
		width: 100%;
		height: 200px;
		box-shadow: inset 0 0 0 2px rgba(0, 0, 0, 0.1);
		border-radius: 3px;
	}
	.znpb-empty-list__content {
		padding: 50px 30px;
	}
}
</style>
