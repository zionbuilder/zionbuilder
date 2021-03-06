<template>
	<Sortable
		class="znpb-option-repeater"
		v-model="sortableItems"
		handle=".znpb-horizontal-accordion > .znpb-horizontal-accordion__header"
	>
		<RepeaterOption
			v-for="(item, index) in sortableItems"
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

		<template #end>
			<Button
				v-if="showButton"
				class="znpb-option-repeater__add-button"
				type="line"
				@click="addProperty"
			>
				{{add_button_text}}
			</Button>
		</template>
	</Sortable>
</template>

<script>
import { computed } from 'vue'
import RepeaterOption from './RepeaterOption.vue'
import { translate } from '@zb/i18n'

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
				return translate('generic_add_new')
			}
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
		},
		add_template: {
			type: Object,
			required: false
		}
	},
	setup (props, { emit }) {
		const sortableItems = computed({
			get () {
				return props.modelValue
			},
			set (newValue) {
				emit('update:modelValue', newValue)
			}
		})

		return {
			sortableItems
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


			// Check to see if we need to delete the data
			if (newValues === null) {
				copiedValues.splice(index, 1)

				if (copiedValues.length === 0) {
					copiedValues = null
				}
			} else {
				copiedValues[index] = newValues
			}

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
			const newItem = this.add_template ? this.add_template : {}
			clone.push(newItem)

			this.$emit('update:modelValue', clone)

			// this.$nextTick(() => {
			// 	console.log(this.$refs.repeaterItem);
			// 	this.$refs.repeaterItem.expand()
			// })
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
