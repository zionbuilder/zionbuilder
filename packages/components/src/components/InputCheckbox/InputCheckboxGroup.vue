<template>
	<div
		class="znpb-checkbox-list"
		:class="wrapperClasses"
	>
		<!-- @slot content for checkbox if no checkbox -->
		<slot></slot>
		<template v-if="!hasSlots">
			<InputCheckbox
				v-for="(option,i) in options"
				:key="i"
				v-model="model"
				:option-value="option.id"
				:label="option.name"
				:disabled="disabled"
				:title="option.icon ? option.name : false"
			>
				<Icon
					v-if="option.icon"
					:icon="option.icon"
				></Icon>
			</InputCheckbox>
		</template>
	</div>
</template>
<script>
import { Comment, computed } from 'vue'
import { Icon } from '../Icon'
import InputCheckbox from './InputCheckbox.vue'

export default {
	name: 'InputCheckboxGroup',
	props: {
		/**
		 * v-model/modelValue for checkbox
		 */
		modelValue: {
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
	setup (props, { slots }) {
		const hasSlots = computed(() => {
			if (!slots.default) {
				return false
			}

			const defaultSlot = slots.default()
			const normalNodes = []
			if (Array.isArray(defaultSlot)) {
				defaultSlot.forEach(vNode => {
					if (vNode.type !== Comment) {
						normalNodes.push(vNode)
					}
				})
			}

			return normalNodes.length > 0
		})

		return {
			hasSlots
		}
	},
	computed: {
		model: {
			get () {
				return this.modelValue ? this.modelValue : []
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
		Icon
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

	// Styles
	&-style--buttons {
		.znpb-checkbox-wrapper {
			padding: 0 2px;

			.znpb-checkmark-option {
				padding: 13px;
				margin-left: 0;
			}
		}

		& .znpb-form__input-checkbox,
		& .znpb-checkmark {
			display: none;
		}

		& .znpb-checkmark-option {
			display: flex;
			justify-content: center;
			align-items: center;
			padding: 10px;
			background-color: var(--zb-surface-lighter-color);
			border-radius: 2px;

			&:hover {
				background-color: var(--zb-surface-lightest-color);
			}
		}

		& input:checked ~ .znpb-checkmark-option {
			color: var(--zb-secondary-text-color);
			background-color: var(--zb-secondary-color);
		}
	}
}
</style>
