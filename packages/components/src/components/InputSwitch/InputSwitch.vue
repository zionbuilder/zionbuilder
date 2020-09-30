<template>
	<div
		class="znpb-toggle-switch"
		:class="switchStyles"
	>
		<input
			v-for="(option,i) in options"
			v-bind:key="i"
			type="radio"
			v-model="valueModel"
			:modelValue="option.id"
			:data-name="option.name"
			class="znpb-form__input-switch"
			name="options"
		>
		<span class="znpb-switch-background"></span>
	</div>
</template>

<script>
/**
 * this type of element supports
 * on/off - the off option appears disabled
 * 2 options both colored when active
 * multiple colored switch till 5 options
 * light class for all of the above mentioned
 *
 * required properties received:
 * 	 multiple
 * 	 onoff
 *   options - array of objects
 *   model - string
 */
export default {
	name: 'InputSwitch',
	props: {
		/**
		 * Value for input
		 */
		modelValue: {
			type: String,
			required: true
		},
		/**
		 * Options to switch from
		 */
		options: {
			type: Array,
			required: true
		},
		/**
		 * If it is multiple or not
		 */
		multiple: {
			type: Boolean,
			required: false,
			default: true
		},
		/**
		 * If it is only on/off only 2 values
		 */
		onoff: {
			type: Boolean,
			required: false,
			default: false
		}
	},
	data () {
		return {}
	},
	computed: {
		valueModel: {
			get () {
				return this.modelValue
			},
			set (newValue) {
				/**
					 *It emits the new value
					 */
				this.$emit('update:modelValue', newValue)
			}
		},
		switchStyles () {
			return {
				['znpb-toggle-switch--multiple-' + this.options.length]: this.multiple,
				'znpb-toggle-switch--onoff': this.onoff
			}
		}
	},
	methods: {
		// check what model receives
		sanitizeLocalModel () {
			if (Array.isArray(this.model)) {
				return this.model.toString()
			} else return String(this.model)
		}
	}
}
</script>
<style lang="scss">
@mixin list-switch($max) {
	$inc: calc( 100% / #{$max});

	@for $i from 0 through $max {
		$i: $i + 1;
		&:nth-child(#{$i}) {
			width: #{$inc};
			&:checked ~ .znpb-switch-background {
				left: calc( #{$inc} * #{$i - 1} );
				width: #{$inc};
				border-radius: 0;
				transform: none;
			}
			&:checked:after {
				color: $primary-color--accent;
			}
			@if($i == 1) {
				&:checked ~ .znpb-switch-background {
					@include border(3px,0,0,3px);
				}
			}

			@if($i == $max) {
				&:checked ~ .znpb-switch-background {
					@include border(0,3px,3px,0);
				}
			}
		}
	}
}

.znpb-switch-background {
	content: "";
	position: absolute;
	top: 0;
	left: 0;
	width: 50%;
	height: 100%;
	background-color: $secondary;
	border-radius: 0;
	transition: all .1s ease;
}

input.znpb-form__input-switch:after {
	content: attr(data-name);
	position: absolute;
	top: 50%;
	right: 0;
	left: 0;
	z-index: 1;
	padding: 12px 10px;
	font-size: 11px;
	text-align: center;
	transform: translateY(-50%);
}

// hide default input
.znpb-form__input-switch {
	position: relative;
	min-width: 50px;
	height: 0;
	padding: 19px 18px!important;
	margin: 0 auto;
	font-family: $font-stack;
	background-color: transparent;
	box-shadow: none;
	border: 0;
	border-radius: 0;
	cursor: pointer;

	-webkit-appearance: none;
}

.znpb-toggle-switch--light {
	.znpb-form__input-switch {
		color: $surface-active-color;
		background-color: $surface-headings-color;
		&:checked {
			color: #fff;
		}
	}
	&.znpb-toggle-switch--onoff {
		.znpb-form__input-switch {
			&:nth-child(2) {
				&:checked {
					color: $font-color;
				}
			}
		}
	}
}

.znpb-toggle-switch {
	position: relative;
	display: inline-flex;
	overflow: hidden;
	margin-right: 12px;
	background: $surface-variant;
	border-radius: 3px;

	&--multiple-2 {
		input.znpb-form__input-switch {
			@include list-switch(2);
		}
	}

	&--multiple-3 {
		input.znpb-form__input-switch {
			@include list-switch(3);
		}
	}

	&--multiple-4 {
		input.znpb-form__input-switch {
			@include list-switch(4);
		}
	}

	&--multiple-5 {
		input.znpb-form__input-switch {
			@include list-switch(5);
		}
	}

	&--onoff {
		&.znpb-toggle-switch--light {
			background: $surface-headings-color;
			.znpb-form__input-switch {
				color: $surface-active-color;
				background-color: $surface-headings-color;
				&:nth-child(2) {
					&:checked ~ .znpb-switch-background {
						background-color: $surface-headings-color;
						box-shadow: -3px 0 3px 0 rgba(0, 0, 0, .1);
					}
				}
			}
		}

		input.znpb-form__input-switch {
			width: 50%;
			color: $font-color;
			background-color: $surface-variant;

			&:first-child {
				&:checked {
					color: #fff;
				}
				&:checked ~ .znpb-switch-background {
					@include border(3px,0,0,3px);
					left: 0;
				}
			}
			&:nth-child(2) {
				&:checked ~ .znpb-switch-background {
					@include border(0,3px,3px,0);
					background-color: $surface-active-color;
					transform: translateX(100%);
					transition: linear .2s;
				}
			}
		}
	}
}
</style>
