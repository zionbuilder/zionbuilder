<template>
	<div>
		<CustomSelector
			v-model="textAlignModel"
			:options="textAlignOptions"
			:columns="4"
		>
		</CustomSelector>
	</div>
</template>
<script>
import CustomSelector from '../custom-selector/CustomSelector.vue'

export default {
	name: 'InputTextAlign',
	props: {
		/**
		* Value of text align
		*/
		modelValue: {
			type: String,
			required: false
		}
	},
	components: {
		CustomSelector
	},
	data () {
		return {
			textAlignOptions: [
				{
					icon: 'align--left',
					id: 'left',
					name: this.$translate('align_left')
				},
				{
					icon: 'align--center',
					id: 'center',
					name: this.$translate('align_center')
				},
				{
					icon: 'align--right',
					id: 'right',
					name: this.$translate('align_right')
				},
				{
					icon: 'align--justify',
					id: 'justify',
					name: this.$translate('justify')
				}
			],
			localModel: []
		}
	},
	computed: {
		textAlignModel: {
			get () {
				return this.modelValue
			},
			set (newValue) {
				this.$emit('update:modelValue', newValue)
			}
		}
	},
	methods: {
		changeValue: function (newValue) {
			this.radioSavedValue = newValue
		}
	}
}
</script>
