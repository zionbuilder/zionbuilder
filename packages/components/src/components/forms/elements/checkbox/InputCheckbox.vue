<template>
	<label
		class="znpb-checkbox-wrapper"
		:aria-disabled="disabled"
	>
		<input
			type="checkbox"
			aria-hidden="true"
			:disabled="disabled"
			v-model="model"
			:value="optionValue"
			class="znpb-form__input-checkbox"
			@change="onChange"
		>
		<span
			class="znpb-checkmark"
			:class="{ 'znpb-checkmark--rounded' : rounded }"
		></span>
		<span
			v-if="$slots.default() || label"
			class="znpb-checkmark-option"
		>
			{{$slots.default()}}
			<!-- @slot content for checkbox or label -->
			<slot></slot>
			<template
				v-if="showLabel && !$slots.default()"
			>
				{{label}}
			</template>
		</span>
	</label>
</template>

<script>
export default {
	name: 'InputCheckbox',
	props: {
		/**
		 * label for checkbox
		 */
		label: {
			type: String,
			required: false
		},
		showLabel: {
			type: Boolean,
			required: false,
			default: true
		},
		/**
		 * v-model/value for checkbox
		 */
		value: {
			type: [String, Array, Boolean],
			required: false
		},
		/**
		 * value for checkbox
		 */
		optionValue: {
			type: [String, Boolean],
			required: false
		},
		/**
		 * if disabled
		 */
		disabled: {
			type: Boolean,
			required: false
		},
		/**
		 * if checkbox checked
		 */
		checked: {
			type: Boolean,
			required: false
		},
		rounded: {
			type: Boolean,
			required: false
		}
	},
	data () {
		return {
			isLimitExceeded: false
		}
	},
	mounted () {
		console.log(this.$slots.default())
	},
	created () {
		console.log(this.$slots.default())
	},
	computed: {
		model: {
			get () {
				return this.value !== undefined ? this.value : false
			},
			set (newValue) {
				this.isLimitExceeded = false
				const allowUnselect = this.parentGroup.allowUnselect

				if (this.isInGroup) {
					this.isLimitExceeded = false
					// Check if minimum limit is meet
					if (this.parentGroup.min !== undefined && newValue.length < this.parentGroup.min) {
						this.isLimitExceeded = true
					}

					// Check if maximum limit is meet
					if (this.parentGroup.max !== undefined && newValue.length > this.parentGroup.max) {
						this.isLimitExceeded = true
					}

					if (this.isLimitExceeded === false) {
						this.$emit('update:modelValue', newValue)
					} else if (allowUnselect && this.isLimitExceeded === true) {
						const clonedValues = [...newValue]
						clonedValues.shift()
						// Allows to change the option check state on nextThick
						this.isLimitExceeded = false
						this.$emit('update:modelValue', clonedValues)
					}
				} else {
					/**
					 * when input model changed, it emits new value
					 */
					this.$emit('update:modelValue', newValue)
				}
			}
		},
		isInGroup () {
			return this.$parent.$options.name === 'InputCheckboxGroup'
		},
		parentGroup () {
			return this.isInGroup ? this.$parent : false
		}
	},
	created () {
		this.checked && this.setInitialValue()
	},
	methods: {
		setInitialValue () {
			this.model = this.value || true
		},
		onChange (event) {
			let checked = event.target.checked
			if (this.isLimitExceeded) {
				this.$nextTick(() => {
					event.target.checked = !checked
				})

				return
			}

			let value
			if (event.target.checked) {
				value = true
			} else {
				value = false
			}
			/**
			 * when input changed, it emits new value
			 */
			this.$emit('change', value)
		}
	}
}
</script>
<style lang="scss">
.znpb-checkbox-wrapper {
	position: relative;
	display: flex;
	cursor: pointer;

	input[type="checkbox"].znpb-form__input-checkbox {
		width: 0;
		height: 0;
		margin: 0;
		border: none;

		&:focus {
			box-shadow: none;
		}
	}

	&:hover .znpb-checkmark:after, input:checked ~ .znpb-checkmark:after {
		display: block;
	}
}

input:checked ~ .znpb-checkmark {
	background-color: $secondary;
}
input:checked ~ .znpb-checkmark:after {
	display: block;
}
input[type="checkbox"]:disabled ~ .znpb-checkmark {
	background-color: $surface-variant;
}
.znpb-checkmark:after {
	top: 4px;
	left: 7px;
	width: 4px;
	height: 8px;
	border: solid $primary-color--accent;
	border-width: 0 2px 2px 0;
	transform: rotate(45deg);
}

.znpb-checkmark {
	position: absolute;
	top: 0;
	left: 0;
	width: 24px;
	height: 24px;
	background-color: $surface;
	border: 2px solid $border-color;
	border-radius: 3px;
	transition: all .2s;
	&--rounded {
		border-radius: 50%;
	}

	&-option {
		white-space: nowrap;
	}

	&:after {
		content: "";
		position: absolute;
		display: none;
	}
}
</style>
