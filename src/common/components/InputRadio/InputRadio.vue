<template>
	<label
		class="znpb-radio-item"
		:class="{
			'znpb-radio-item--active': isSelected,
			'znpb-radio-item--hidden-input': hideInput
		}"
	>
		<input
			v-model="radioButtonValue"
			:modelValue="optionValue"
			type="radio"
			class="znpb-form__input-toggle"
		>
		<span class="znpb-radio-item-input"></span>
		<!-- @slot Content such as Icon -->
		<slot></slot>
		<span
			class="znpb-radio-item-label"
			v-if="label"
		>{{label}}</span>
	</label>
</template>

<script>
export default {
	name: 'InputRadio',
	props: {
		/**
		 * modelValue
		 */
		modelValue: {
			type: String,
			required: false
		},
		/**
		 * Label
		 */
		label: {
			type: String,
			required: false
		},
		/**
		 * Initial option
		 */
		optionValue: {
			type: String,
			required: true
		},
		/**
		 * If input should be hidden
		 */
		hideInput: {
			type: Boolean,
			required: false,
			default: false
		}
	},
	data () {
		return {
			checked: ''
		}
	},
	computed: {
		radioButtonValue: {
			get: function () {
				return this.modelValue
			},
			set: function () {
				/**
				 * Emits new radio option
				 */
				this.$emit('update:modelValue', this.optionValue)
			}
		},
		isSelected () {
			return this.modelValue === this.optionValue
		}
	},
	methods: {

	}
}
</script>
<style lang="scss">
.znpb-radio-item {
	input {
		display: none;
		padding: 0;
		margin: 0;

		-webkit-appearance: none;
	}

	&--hidden-input &-input {
		display: none;
		padding: 0;
		margin: 0;

		-webkit-appearance: none;
	}

	&-input {
		position: relative;
		display: inline-block;
		width: 14px;
		height: 14px;
		margin-right: 14px;
		vertical-align: bottom;

		&:before {
			content: "";
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background: var(--zb-surface-lighter-color);
			border-radius: 50%;
		}
	}

	&--active &-input,
	&:hover &-input {
		&:before {
			content: "";
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background: var(--zb-secondary-color);
			animation-duration: 0.3s;
			animation-name: colorGrow;
		}
	}

	@keyframes colorGrow {
		0% {
			background-color: var(--zb-surface-lighter-color);
			transform: scale(1);
		}
		50% {
			background-color: var(--zb-surface-lighter-color);
			transform: scale(0);
		}
		100% {
			background-color: var(--zb-secondary-color);
			transform: scale(1);
		}
	}
}
</style>
