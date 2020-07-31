<template>

	<OptionsForm
		:schema="schema"
		v-model="valueModel"
	/>

</template>

<script>

import ShapeDividerComponent from './ShapeDividerComponent.vue'
import CustomSelector from '../custom-selector/CustomSelector'
import { InputWrapper } from '../inputWrapper'
import { mapGetters } from 'vuex'
export default {
	name: 'ShapeConfig',
	inject: ['inputWrapper', 'optionsForm'],

	props: {
		/**
		 * Value for input
		 */
		config: {
			type: Object,
			required: true
		},
		position: {
			type: String,
			required: false
		}

	},
	data () {
		return {
		}
	},
	computed: {
		valueModel: {
			get () {
				return this.config
			},
			set (newValue) {
				this.$emit('input', newValue)
			}
		},
		schema () {
			return {
				shape: {
					type: 'shape_component',
					id: 'shape',
					width: '100',
					title: this.$translate('select_mask'),
					position: this.position
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
				}
			}
		}
	},
	methods: {
	}
}
</script>
