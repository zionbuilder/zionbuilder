<template>

	<div>
		<CustomSelector
			v-model="activeMaskPosition"
			:options="maskPosOptions"
			:columns="2"
		/>

		<OptionsForm
			:schema="schema"
			v-model="computedValue"
		/>
	</div>

</template>

<script>
import CustomSelector from '../custom-selector/CustomSelector'
import { mapGetters } from 'vuex'

export default {
	name: 'ShapeDividers',
	components: {
		CustomSelector
	},
	props: {
		/**
		 * Value for input
		 */
		value: {
			type: Object,
			default () {
				return {}
			}
		}
	},
	data () {
		return {
			maskPosOptions: [
				{
					id: 'top',
					name: this.$translate('top_masks')
				},
				{
					id: 'bottom',
					name: this.$translate('bottom_masks')
				}
			],

			activeMaskPosition: 'top'
		}
	},
	computed: {
		computedTitle () {
			return this.activeMaskPosition === 'top' ? this.$translate('select_top_mask') : this.$translate('select_bottom_mask')
		},
		schema () {
			return {
				shape: {
					type: 'shape_component',
					id: 'shape',
					width: '100',
					title: this.computedTitle,
					position: this.activeMaskPosition
				},
				color: {
					type: 'colorpicker',
					id: 'color',
					width: '100',
					title: this.$translate('select_mask_color')
				},
				height: {
					type: 'dynamic_slider',
					id: 'height',
					title: this.$translate('select_mask_height'),
					width: '100',
					options: [
						{ 'unit': 'px', 'min': 0, 'max': 4999, 'step': 1 },
						{ 'unit': '%', 'min': 0, 'max': 100, 'step': 1 },
						{ 'unit': 'vh', 'min': 0, 'max': 100, 'step': 10 },
						{ 'unit': 'auto' }
					]
				},
				flip: {
					type: 'checkbox_switch',
					id: 'flip',
					title: this.$translate('flip_mask'),
					width: '100'
				}
			}
		},
		computedValue: {
			get () {
				return this.value ? this.value[this.activeMaskPosition] ? this.value[this.activeMaskPosition] : {} : {}
			},
			set (newValue) {
				if (this.value[this.activeMaskPosition] !== undefined && this.value[this.activeMaskPosition]['shape'] !== newValue['shape']) {
					if (newValue.hasOwnProperty('height')) {
						newValue['height'] = 'auto'
					}
				}
				this.$emit('input', {
					...this.value,
					[this.activeMaskPosition]: newValue
				})
			}
		}
	}
}
</script>
<style lang="scss">
.znpb-shape-list {
	display: flex;
	flex-direction: column;
	max-height: 400px;
	padding: 20px;
	margin: 0 -20px;
	background-color: #f1f1f1;
}

/* Enter and leave transitions for delete mask */
.slide-fade-enter-active, .slide-fade-leave-active {
	transition: all .1s;
}

.slide-fade-enter, .slide-fade-leave-to {
	opacity: 0;
}
</style>
