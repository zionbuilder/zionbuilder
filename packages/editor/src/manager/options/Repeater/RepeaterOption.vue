<template>
	<HorizontalAccordion
		:title="title"
		:combine-breadcrumbs="true"
		:show-back-button="true"
	>
		<BaseIcon
			v-if="clonable"
			class="znpb-option-repeater-selector__clone-icon"
			@click.stop="cloneOption"
			icon="copy"
			slot="actions"
		></BaseIcon>
		<BaseIcon
			v-if="deletable"
			class="znpb-option-repeater-selector__delete-icon"
			@click.stop="deleteOption(propertyIndex)"
			icon="delete"
			slot="actions"
		></BaseIcon>

		<OptionsForm
			:schema="schema"
			:modelValue="selectedOptionModel"
			@update:modelValue="onItemChange($event, propertyIndex)"
			class="znpb-option-repeater-form"
		/>
	</HorizontalAccordion>

</template>

<script>
export default {
	name: 'RepeaterOption',
	data () {
		return {
			folded: true
		}
	},
	props: {
		modelValue: {
			default () {
				return {}
			}
		},
		schema: {
			type: Object,
			required: true,
			default () {
				return []
			}
		},
		propertyIndex: {
			type: Number
		},
		item_title: {
			type: String,
			required: false
		},
		default_item_title: {
			type: String,
			required: true
		},
		deletable: {
			type: Boolean,
			required: false,
			default: true
		},
		clonable: {
			type: Boolean,
			required: false,
			default: true
		}
	},
	computed: {
		selectedOptionModel: {
			get () {
				return this.modelValue
			},
			set (newValue) {
				this.$emit('update:modelValue', newValue)
			}
		},
		title () {
			if (this.item_title && this.selectedOptionModel && this.selectedOptionModel[this.item_title]) {
				return this.selectedOptionModel[this.item_title]
			}

			return this.default_item_title.replace('%s', this.propertyIndex)
		}
	},
	methods: {
		cloneOption () {
			const clone = JSON.parse(JSON.stringify(this.modelValue))
			this.$emit('clone-option', clone)
		},
		deleteOption (propertyIndex) {
			this.$emit('delete-option', propertyIndex)
		},
		toggleOptions () {
			this.folded = !this.folded
		},
		onItemChange (newValues, index) {
			this.$emit('update:modelValue', { newValues, index })
		},
		expand () {
			this.folded = false
		},
		collapse () {
			this.folded = true
		}
	}
}
</script>

<style lang="scss">
.znpb-options-form-wrapper.znpb-option-repeater-form {
	padding-top: 0;
}
</style>
