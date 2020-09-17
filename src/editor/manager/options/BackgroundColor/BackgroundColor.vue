<template>
	<Color
		v-model="colorModel"
		@open="$emit('open')"
		@close="$emit('close')"
	>
		<div
			class="znpb-style-background-color"
			slot="trigger"
		>
			<EmptyList
				class="znpb-input-background-image__empty"
				v-if="! value"
				:no-margin="true"
			>
				{{$translate('select_background_color')}}
			</EmptyList>
			<ActionsOverlay v-if="value">
				<div
					class="znpb-style-background-color__holder"
					:style="getColorStyle"
				></div>

				<div slot="actions">
					<BaseIcon
						:rounded="true"
						icon="delete"
						:bg-size="30"
						bg-color="#fff"
						@click.stop.native="deleteColor"
					/>
				</div>
			</ActionsOverlay>

		</div>
	</Color>
</template>

<script>
import { EmptyList } from '@zb/components/forms'
import {
	ActionsOverlay,
	ColorPicker,
	Color
} from '@zb/components'

export default {
	name: 'BackgroundColor',
	props: {
		value: {
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
				if (this.value !== undefined) {
					if (typeof (this.value) === 'string') {
						computedValue = this.value
					} else computedValue = this.value.value
				}

				return computedValue !== null ? computedValue : ''
			},
			set (newColor) {
				this.$emit('input', newColor)
			}
		},
		getColorStyle () {
			return {
				'background-color': this.colorModel
			}
		}
	},
	components: {
		Color,
		EmptyList,
		ActionsOverlay
	},
	methods: {
		deleteColor () {
			this.$emit('input', null)
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
		box-shadow: inset 0 0 0 2px rgba(0, 0, 0, .1);
		border-radius: 3px;
	}
	.znpb-empty-list__content {
		padding: 50px 30px;
	}
}
</style>
