<template>
	<div class="znpb-button"
		:class="{['znpb-button--' + type]: type}"
	>
		<!-- @slot Content that will be added inside button -->
		<slot></slot>
	</div>
</template>

<script>
export default {
	name: 'BaseButton',
	props: {
		/**
		 * Button type
		 */
		type: {
			type: String,
			required: false
		}
	}
}
</script>

<style lang="scss">
.znpb-button {
	display: inline-flex;
	justify-content: center;
	align-items: center;
	padding: 13.5px 20px;
	color: $primary-color--accent;
	font-family: $font-stack;
	font-size: 13px;
	font-weight: 500;
	line-height: 1;
	background: $primary-color;
	border-radius: 3px;
	transition: all .3s;
	cursor: pointer;

	&:hover {
		background: darken( $primary-color, 3 );
	}

	b {
		font-weight: 700;
	}

	&:last-child {
		margin-right: 0;
	}

	&--minimal {
		padding: 0 40px;
		color: $surface-active-color;
		font-size: 14px;
		font-weight: 400;
		line-height: 44px;
		background: transparent;

		&:hover {
			background: transparent;
		}
	}

	&--secondary {
		color: $secondary-color--accent;
		background: $secondary;

		&:hover, &:focus, &:active {
			color: $surface;
		}

		&:hover {
			background: lighten( $secondary, 2 );
		}
	}

	&--line {
		padding: 11.5px 20px;
		color: $surface-active-color;
		background: transparent;
		border: 2px solid $border-color;

		& .znpb-editor-icon {
			color: $font-color;
		}

		&:hover {
			background: none;
			opacity: .6;
		}
	}

	&--color {
		padding: 18px 70px;
		background: $pro-color;
		&:hover {
			background: darken( $pro-color, 3 );
		}
	}

	&--danger {
		background: #e35364;

		&:hover {
			background: darken( #e35364, 3 );
		}
	}

	&--gray {
		color: #858585;
		background: #f1f1f1;

		&:hover {
			background: darken( #f1f1f1, 3 );
		}
	}
	&--disabled {
		background: rgba( $secondary, .7);
		pointer-events: none;
	}
}
</style>
