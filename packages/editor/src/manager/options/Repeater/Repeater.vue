<template>
	<div class="znpb-option-repeater">
		<RepeaterOption
			v-for="(item, index) in modelValue"
			:key="index"
			:schema="child_options"
			:modelValue="item"
			:propertyIndex="index"
			:item_title="item_title"
			:default_item_title="default_item_title"
			:deletable="!addable ? false : deletable"
			:clonable="checkClonable"
			@clone-option="cloneOption($event, index)"
			@delete-option="deleteOption"
			@update:modelValue="onItemChange($event)"
			ref="repeaterItem"
		>
		</RepeaterOption>
		<BaseButton
			v-if="showButton"
			class="znpb-option-repeater__add-button"
			type="line"
			@click="addProperty"
		>
			{{add_button_text}}
		</BaseButton>
	</div>
</template>

<script>
import RepeaterOption from './RepeaterOption.vue'

export default {
	name: 'Repeater',
	data () {
		return {
		}
	},
	props: {
		modelValue: {
			type: Array,
			required: false,
			default () {
				return []
			}
		},
		addable: {
			type: Boolean,
			required: false,
			default: true
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
		},
		maxItems: {
			type: Number,
			required: false
		},
		add_button_text: {
			type: String,
			required: false,
			default () {
				return this.$translate('generic_add_new')
			}
		},
		default_unit: {
			type: String,
			required: false
		},
		child_options: {
			type: Object,
			required: true
		},
		item_title: {
			type: String,
			required: false
		},
		default_item_title: {
			type: String,
			required: true
		},
		reset_repeater: {
			type: Object,
			required: false
		}
	},
	components: {
		RepeaterOption
	},
	computed: {
		valueModel () {
			return this.modelValue || []
		},
		showButton () {
			return this.maxItems ? this.addable && (this.valueModel.length < this.maxItems) : this.addable
		},
		checkClonable () {
			return !this.addable ? false : !this.maxItems ? this.clonable : this.valueModel.length < this.maxItems
		}
	},
	methods: {
		onItemChange (payload) {
			const { index, newValues } = payload
			let copiedValues = [...this.valueModel]
			copiedValues[index] = newValues

			if (this.reset_repeater && this.reset_repeater.option) {
				const resetOption = this.reset_repeater.option
				const oldValue = this.valueModel[index][resetOption]
				const newValue = newValues[resetOption]

				if (newValue && oldValue !== newValue) {
					const newValues = {}
					newValues[resetOption] = newValue
					copiedValues[index] = newValues
				}
			}

			this.$emit('update:modelValue', copiedValues)
		},
		addProperty () {
			const clone = [...this.valueModel]
			clone.push({})
			this.$emit('update:modelValue', clone)

			this.$nextTick(() => {
				const itemsLength = this.$refs.repeaterItem.length
				this.$refs.repeaterItem[itemsLength - 1].expand()
			})
		},
		cloneOption (event, index) {
			if ((this.maxItems && this.addable && (this.valueModel.length < this.maxItems)) || (this.maxItems === undefined)) {
				const repeaterClone = [...this.modelValue]
				repeaterClone.splice(index, 0, event)

				this.$emit('update:modelValue', repeaterClone)
			}
		},
		deleteOption (optionIndex) {
			let copiedValues = [...this.valueModel]
			copiedValues.splice(optionIndex, 1)
			this.$emit('update:modelValue', copiedValues)
		}
	}
}
</script>

<style lang="scss">
.znpb-option-repeater__add-button {
	width: 100%;
	margin-top: 5px;
	text-align: center;
}
</style>
