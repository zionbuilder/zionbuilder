<template>
	<div
		class="znpb-checkbox-list"
		:class="wrapperClasses"
	>
		<!-- @slot content for checkbox if no checkbox -->
		<slot></slot>
		<template v-if="!$slots.default">
			<InputCheckbox
				v-for="(option,i) in options"
				:key="i"
				v-model="model"
				:option-value="option.id"
				:label="option.name"
				:disabled="disabled"
				:title="option.icon ? option.name : false"
			>
				<BaseIcon
					v-if="option.icon"
					:icon="option.icon"
				></BaseIcon>
			</InputCheckbox>
		</template>
	</div>
</template>
<script>
import BaseIcon from '../../../BaseIcon.vue'
import InputCheckbox from './InputCheckbox.vue'

export default {
	name: 'InputCheckboxGroup',
	props: {
		/**
		 * v-model/value for checkbox
		 */
		value: {
			type: Array,
			required: false,
			default () {
				return []
			}
		},
		/**
		 * minimum checked values
		 */
		min: {
			type: Number,
			required: false
		},
		/**
		 * maximum checked values
		 */
		max: {
			type: Number,
			required: false
		},
		/**
		 * Allows to unselect the last value in case the limit is exceeded
		 */
		allowUnselect: {
			type: Boolean,
			required: false,
			default: false
		},
		/**
		 * values direction
		 */
		direction: {
			type: String,
			required: false,
			default: 'vertical'
		},
		/**
		 * option values
		 */
		options: {
			type: Array,
			required: false
		},
		/**
		 * cheked disabled
		 */
		disabled: {
			type: Boolean,
			required: false
		},
		displayStyle: {
			type: String,
			required: false
		}
	},
	computed: {
		model: {
			get () {
				return this.value ? this.value : []
			},
			set (newValue) {
				this.$emit('update:modelValue', newValue)
			}
		},
		wrapperClasses () {
			return {
				[`znpb-checkbox-list--${this.direction}`]: this.direction,
				[`znpb-checkbox-list-style--${this.displayStyle}`]: this.displayStyle
			}
		}
	},
	components: {
		InputCheckbox,
		BaseIcon
	}
}
</script>

<style lang="scss">
.znpb-checkbox-list {
	overflow: hidden;
	border-radius: 3px;
	&--vertical .znpb-checkbox-wrapper {
		margin-bottom: 16px;

		&:last-child {
			margin-bottom: 0;
		}
	}
	&--horizontal {
		display: flex;
	}
	.znpb-checkbox-wrapper {
		flex: 1;
		.znpb-checkmark-option {
			width: 100%;
			padding: 6px 5px 6px 16px;
			font-weight: 500;
			text-align: left;
		}
	}
	// Styles
	&-style--buttons {
		.znpb-checkbox-wrapper {
			padding: 0 2px;

			.znpb-checkmark-option {
				padding: 13px;
			}
		}

		& .znpb-form__input-checkbox, & .znpb-checkmark {
			display: none;
		}

		& .znpb-checkmark-option {
			display: flex;
			justify-content: center;
			align-items: center;
			padding: 10px;
			background-color: $surface-variant;
			border-radius: 2px;

			&:hover {
				background-color: darken($surface-variant, 3%);
			}
		}

		& input:checked ~ .znpb-checkmark-option {
			color: $primary-color--accent;
			background-color: $secondary;
		}
	}
}
</style>
