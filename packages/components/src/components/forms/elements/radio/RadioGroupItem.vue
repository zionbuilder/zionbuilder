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
			:value="optionValue"
			type="radio"
			class="znpb-form__input-toggle"
		>
		<span
			class="znpb-radio-item-input"
		></span>
		<!-- @slot Content such as BaseIcon -->
		<slot></slot>
		<span class="znpb-radio-item-label" v-if="label">{{label}}</span>
	</label>
</template>

<script>
export default {
	name: 'RadioGroupItem',
	props: {
		/**
		 * value/model
		 */
		value: {
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
				return this.value
			},
			set: function () {
				/**
				 * Emits new radio option
				 */
				this.$emit('input', this.optionValue)
			}
		},
		isSelected () {
			return this.value === this.optionValue
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
			background: $surface-variant;
			border-radius: 50%;
		}
	}

	&--active &-input, &:hover &-input {
		&:before {
			content: "";
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background: $secondary;
			animation-duration: .3s;
			animation-name: colorGrow;
		}
	}

	@keyframes colorGrow {
		0% {
			background-color: $surface-variant;
			transform: scale(1);
		}
		50% {
			background-color: $surface-variant;
			transform: scale(0);
		}
		100% {
			background-color: $secondary;
			transform: scale(1);
		}
	}
}
</style>
