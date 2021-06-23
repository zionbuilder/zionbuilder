<template>
	<div class="znpb-checkbox-switch-wrapper">

		<label
			class="znpb-checkbox-switch-wrapper__label"
			:class="{[`znpb-checkbox-switch--${model? 'checked' : 'unchecked'}`]: true}"
			:content="model? $translate('yes') : $translate('no')"
		>
			<input
				type="checkbox"
				:disabled="disabled"
				class="znpb-checkbox-switch-wrapper__checkbox"
				:modelValue="optionValue"
				v-model="model"
			>

			<span class="znpb-checkbox-switch-wrapper__button"></span>
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
		 * v-model/modelValue for checkbox
		 */
		modelValue: {
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
				return this.modelValue !== undefined ? this.modelValue : false
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
			this.model = this.modelValue || true
		},
		onChange (event) {
			let checked = event.target.checked
			if (this.isLimitExceeded) {
				this.$nextTick(() => {
					event.target.checked = !checked
				})

				return
			}

			/**
			 * when input changed, it emits new value
			 */
			this.$emit('change', !!event.target.checked)
		}
	}
}
</script>
<style lang="scss">
.znpb-checkbox-switch-wrapper {
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

	&:hover .znpb-checkmark:after,
	input:checked ~ .znpb-checkmark:after {
		display: block !important;
	}

	&__checkbox {
		display: none !important;
	}
	&__label {
		position: relative;
		display: block;
		box-sizing: content-box;
		width: 74px;
		height: 40px;
		background: var(--zb-input-bg-color);
		border: 2px solid var(--zb-input-border-color);
		border-radius: 3px;
		cursor: pointer;

		&:before,
		&:after {
			position: absolute;
			display: flex;
			justify-content: center;
			align-items: center;
			width: 50%;
			height: 100%;
			color: var(--zb-input-text-color);
			font-family: var(--zb-font-stack);
			font-size: 13px;
			font-weight: 500;
			text-align: center;
		}

		&:before {
			content: attr(content);
		}
		&:after {
			content: "" attr(content) "";
			right: 0;
			color: var(--zb-surface-text-active-color);
		}
	}

	&__button {
		position: absolute;
		top: 2px;
		left: 2px;
		z-index: 1;
		width: calc(50% - 2.5px);
		height: calc(100% - 4px);
		background: #006dd2;
		border-radius: 2px;
		transition: transform 0.15s, background-color 0.1s;
	}
}

.znpb-checkbox-switch--checked {
	&::before {
		content: "";
	}
}
.znpb-checkbox-switch--unchecked {
	&::after {
		content: "";
	}
	.znpb-checkbox-switch-wrapper__button {
		background: var(--zb-surface-lightest-color);
		transform: translateX(33px);
	}
}
</style>
