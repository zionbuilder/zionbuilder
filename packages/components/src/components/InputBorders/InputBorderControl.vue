<template>
	<OptionsForm
		:schema="schema"
		v-model="computedValue"
		class="znpb-border-control-group"
	/>
</template>

<script>
export default {
	name: 'InputBorderControl',
	props: {
		/**
		 * v-model/modelValue for border
		 */
		modelValue: {
			default () {
				return {}
			},
			type: Object,
			required: false
		},
		/**
		 * title border
		 */
		title: {
			type: String,
			required: false
		}
	},
	data () {
		return {
			borderStyle: [
				{
					id: 'solid',
					name: 'solid'
				},
				{
					id: 'dashed',
					name: 'dashed'
				},
				{
					id: 'dotted',
					name: 'dotted'
				},
				{
					id: 'double',
					name: 'double'
				},
				{
					id: 'groove',
					name: 'groove'
				},
				{
					id: 'ridge',
					name: 'ridge'
				},
				{
					id: 'inset',
					name: 'inset'
				},
				{
					id: 'outset',
					name: 'outset'
				}
			]
		}
	},
	computed: {
		schema () {
			const schema = {
				color: {
					type: 'colorpicker',
					css_class: 'znpb-border-control-group-item',
					title: 'Color',
					width: 33.3
				},
				width: {
					type: 'number_unit',
					title: 'Width',
					min: 0,
					max: 999,
					units: ['px', 'rem', 'pt', 'vh', '%'],
					step: 1,
					css_class: 'znpb-border-control-group-item',
					width: 33.3
				},
				style: {
					type: 'select',
					title: 'Style',
					default: 'solid',
					options: this.borderStyle,
					css_class: 'znpb-border-control-group-item',
					width: 33.3
				}
			}

			return schema
		},

		computedValue: {
			get () {
				return this.modelValue || {}
			},
			set (newValue) {
				this.$emit('update:modelValue', newValue)
			}
		}
	}
}
</script>

<style lang="scss">
.znpb-input-wrapper.znpb-border-control-group-item {
	padding-bottom: 0;
}
</style>
