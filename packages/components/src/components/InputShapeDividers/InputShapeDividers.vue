<template>

	<div>
		<InputCustomSelector
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
import { get } from 'lodash-es'
import { InputCustomSelector } from '../InputCustomSelector'

export default {
	name: 'InputShapeDividers',
	components: {
		InputCustomSelector
	},
	props: {
		/**
		 * Value for input
		 */
		modelValue: {
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
					responsive_options: true,
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
					width: '100',
					layout: 'inline'
				}
			}
		},
		computedValue: {
			get () {
				return this.modelValue ? this.modelValue[this.activeMaskPosition] ? this.modelValue[this.activeMaskPosition] : {} : {}
			},
			set (newValue) {
				if (newValue === null) {
					this.$emit('update:modelValue', null)
					return
				}

				const shape = get(this.modelValue, `${this.activeMaskPosition}.shape`)
				if (shape !== newValue['shape']) {
					if (newValue.hasOwnProperty('height')) {
						newValue['height'] = 'auto'
					}
				}
				this.$emit('update:modelValue', {
					...this.modelValue,
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
	background-color: var(--zb-surface-light-color);
}

/* Enter and leave transitions for delete mask */
.slide-fade-enter-to, .slide-fade-leave-from {
	transition: all .1s;
}

.slide-fade-enter-from, .slide-fade-leave-to {
	opacity: 0;
}
</style>
