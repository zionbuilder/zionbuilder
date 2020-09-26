<template>
	<div class="znpb-checkbox-wrapper">
		<label
			class="znpb-checkbox-wrapper__label"
			:class="{[`znpb-checkbox--${this.model? 'checked' : 'unchecked'}`]: true}"
			:content="this.model? $translate('yes') : $translate('no')"
		>
			<input
				type="checkbox"
				:disabled="disabled"
				class="znpb-checkbox-wrapper__checkbox"
				:value="optionValue"
				v-model="model"
			>
			<!-- @change="onChange" -->

			<span class="znpb-checkbox-wrapper__button"></span>
		</label>
	</div>
</template>

<script>
export default {
	name: 'InputCheckboxSwitch',
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
.znpb-checkbox-wrapper__checkbox {
	display: none;
}

.znpb-checkbox-wrapper__label {
	position: relative;
	display: block;
	box-sizing: content-box;
	width: 74px;
	height: 40px;
	border: 2px solid #e5e5e5;
	border-radius: 3px;
	cursor: pointer;
}

.znpb-checkbox-wrapper__label::before, .znpb-checkbox-wrapper__label::after {
	position: absolute;
	display: flex;
	justify-content: center;
	align-items: center;
	width: 50%;
	height: 100%;
	color: #787878;
	font-family: Roboto;
	font-size: 13px;
	font-weight: 500;
	text-align: center;
}

.znpb-checkbox-wrapper__label::before {
	content: ""attr(content)"" ;
}

.znpb-checkbox-wrapper__label::after {
	content: ""attr(content)"" ;
	right: 0;
}

.znpb-checkbox-wrapper__button {
	position: absolute;
	top: 2px;
	left: 2px;
	z-index: 1;
	width: calc(50% - 2.5px);
	height: calc(100% - 4px);
	background: #006dd2;
	border-radius: 2px;
	transition: transform .15s, background-color .1s;
}
.znpb-checkbox--checked {
	&::before {
		content: "";
	}
}
.znpb-checkbox--unchecked {
	&::after {
		content: "";
	}
	.znpb-checkbox-wrapper__button {
		background: #ebebeb;
		transform: translateX(33px);
	}
}

.has-changes {
	position: relative;
	display: inline-block;
	width: 10px;
	height: 10px;
	padding: 3px;
	cursor: pointer;
}

.has-changes::after {
	content: "";
	position: absolute;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	background: #31d783;
	border-radius: 50%;
	transition: transform .15s, background-color .1s;
}

.has-changes:hover::after {
	background-color: #ebebeb;
	transform: scale(2);
}

/* .has-changes__icon {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  color: #959595;
  display: inline-flex;
  align: items: center;
  justify-content: center;
  font-size: 10px;
  z-index: 1;
  opacity: 0;
  transition: opacity 0.1s;
} */

.has-changes > span {
	position: absolute;
	top: 0;
	left: 0;
	display: block;
	width: 9px;
	height: 9px;
	margin: .5px 0 0 .5px;
	transition: opacity .1s;
	opacity: 0;
}

.has-changes > span::before, .has-changes > span::after {
	content: "";
	position: absolute;
	top: 0;
	left: 0;
	z-index: 1;
	width: 100%;
	height: 2px;
	margin-top: 4px;
	background: #959595;
}

.has-changes > span::before {
	transform: rotate(45deg);
}

.has-changes > span::after {
	transform: rotate(-45deg);
}

.has-changes:hover > span {
	opacity: 1;
}
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

.znpb-input-type--checkbox_switch .znpb-checkbox-wrapper {
	cursor: default;
}

input:checked ~ .znpb-checkmark {
	background-color: $secondary;
}
input:checked ~ .znpb-checkmark:after {
	display: block;
}
input[type="checkbox"]:disabled ~ .znpb-checkmark {
	background-color: $surface-variant;
	pointer-events: none;

	&::after {
		display: none;
	}
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
	width: 24px;
	height: 24px;
	background-color: $surface;
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
