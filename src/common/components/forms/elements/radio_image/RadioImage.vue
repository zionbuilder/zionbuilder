<template>
	<div class="znpb-radio-image-wrapper znpb-fancy-scrollbar">
		<ul
			class="znpb-radio-image-list "
			:class="[`znpb-radio-image-list--columns-${columns}`]"
		>
			<li
				v-for="(option, index) in options"
				:key="index"
				class="znpb-radio-image-list__item-wrapper"

				@click="changeValue(option.value)"
			>

				<div class="znpb-radio-image-list__item"
					:class="{['znpb-radio-image-list__item--active']: value === option.value}"
				>
					<img
						v-if="option.image"
						:src="option.image"
						class="znpb-image-wrapper"
					/>
					<span
						v-if="option.class"
						class="znpb-radio-image-list__preview-element animated" :class="option.value">
					</span>
					<BaseIcon
						class="znpb-radio-image-list__icon"
						v-if="option.icon"
						:icon="option.icon"
					/>
				</div>
				<span
					class="znpb-radio-image-list__item-name"
					v-if="option.name"
				>
					{{option.name}}
				</span>
			</li>
		</ul>
	</div>
</template>

<script>
export default {
	name: 'RadioImage',
	props: {
		/**
		 * Value or v-model
		 */
		value: {
			type: String
		},
		/**
		 * Options
		 */
		options: {
			type: Array,
			required: false
		},
		/**
		 * Number of columns
		 */
		columns: {
			type: Number,
			required: false
		}
	},
	data () {
		return {

		}
	},
	methods: {
		changeValue (newValue) {
			this.$emit('input', newValue)
		}
	}
}
</script>
<style lang="scss">
@function list-grid($col) {
	$fr: "1fr ";
	$grid: "";

	@for $i from 1 through $col {
		$grid: str-insert($grid,"#{$fr}",(str-length($grid)+1));
	}

	@return(unquote($grid));
}
.znpb-input-wrapper.znpb-input-type--radio_image {
	margin-bottom: 20px;

	> .znpb-input-content {
		width: auto;
		margin: 0 -20px;
	}
}
.znpb-radio-image-wrapper {
	padding: 20px 20px;
	color: $font-color;
	background: #fafafa;
	box-shadow: 0 5px 10px 0 rgba(164, 164, 164, .1);
	border-radius: 3px;
}
.znpb-radio-image-list__item-wrapper {
	display: flex;
	flex-direction: column;
	width: 88px;
}
.znpb-radio-image-list__preview-element {
	display: block;
	width: 20px;
	height: 20px;
	background: #e8e8e8;
	border-radius: 6px;
	animation-duration: 1s;
	animation-iteration-count: infinite;
	animation-fill-mode: both;
}
ul.znpb-radio-image-list {
	display: grid;
	max-height: 300px;

	grid-gap: 20px;
}
.znpb-radio-image-list {
	&__icon {
		animation-duration: 1s;
		animation-iteration-count: infinite;
		animation-fill-mode: both;
	}
	&__item {
		position: relative;
		display: flex;
		flex-direction: column;
		flex-wrap: wrap;
		justify-content: space-around;
		align-items: center;
		overflow: hidden;
		height: 100px;
		padding: 15px;
		margin-bottom: 8px;
		background-color: $surface;
		box-shadow: 0 5px 10px 0 rgba(164, 164, 164, .08);
		border: 1px solid $surface-variant;
		border-radius: 3px;
		transition: all .2s ease;
		cursor: pointer;
		height: 88px;

		&:hover {
			box-shadow: 0 5px 10px 0 rgba(164, 164, 164, .22);
		}
		&--active {
			border: 2px solid $secondary;
		}

		&:hover &__item-name, &--active &__item-name {
			color: $secondary-color--accent;
		}
	}

	&__item-name {
		font-size: 12px;
		font-weight: 500;
		text-transform: capitalize;
	}

	&--columns-1 {
		width: 100%;
	}
	&--columns-2 {
		grid-template-columns: list-grid(2);
	}
	&--columns-3 {
		grid-template-columns: list-grid(3);
	}
	&--columns-4 {
		grid-template-columns: list-grid(4);
	}

	&__has-image {
		padding: 0;
		border: 0;
		.znpb-radio-image-list__item-name {
			text-align: center;
		}
		.znpb-image-wrapper {
			background-repeat: no-repeat;
			background-position: center center;
			background-size: cover;
		}
		&:hover, &.znpb-radio-image-list__item--active {
			.znpb-image-wrapper {
				background-color: $secondary;

				background-blend-mode: multiply;
			}
		}
	}
	&__icon-text-content {
		display: flex;
		flex-direction: column;
	}
	.znpb-radio-image-list__item-name, .znpb-editor-icon-wrapper {
		text-align: center;
		transition: all .2s ease;
	}
}

.znpb-radio-image-list__item {
	&:hover {
		.znpb-radio-image-list__item-name, .znpb-editor-icon-wrapper {
			color: $surface;
		}
	}
}
.znpb-radio-image-list__item--active {
	.znpb-radio-image-list__item-name, .znpb-editor-icon-wrapper {
		color: $surface;
	}
}
</style>
