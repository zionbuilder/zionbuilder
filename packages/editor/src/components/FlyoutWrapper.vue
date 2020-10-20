<template>
	<div
		class="znpb-editor-header-flyout"
		@mouseover.stop="showflyout=true"
		@mouseleave.stop="showflyout=false"
	>
		<div class="znpb-editor-header__menu_button">
			<slot name="panel-icon"></slot>
		</div>

		<ul
			class="znpb-editor-header-flyout-hidden-items znpb-editor-header__menu-list"
			v-if="showflyout"
		>
			<slot></slot>
		</ul>

	</div>
</template>
<script>
import { ref } from 'vue'

export default {
	name: 'FlyoutWrapper',
	setup (props) {
		const showflyout = ref(false)

		return {
			showflyout,
			items: props.items
		}
	}
}
</script>
<style lang="scss">
ul.znpb-editor-header-flyout-hidden-items {
	@extend %tooltip;
	padding: 8px 0;
	font-weight: 500;
}
.znpb-editor-header-flyout {
	position: relative;
	display: flex;
	justify-content: center;
	align-items: center;
	width: 60px;
	height: 60px;
	transition: background-color .15s ease;
	cursor: pointer;

	.znpb-editor-header__menu_button {
		width: auto;
		height: auto;
	}

	/* .znpb-editor-header-flyout-hidden-items */
	&-hidden-items {
		position: absolute;
		top: 0;
		left: 100%;
		min-width: 180px;
		white-space: nowrap;
		list-style: none;
		transform-origin: left top;
		li {
			a {
				display: block;
				padding: 8px 16px;
				color: $font-color;
				font-size: 13px;
				line-height: 1;
				text-decoration: none;
				text-transform: capitalize;
				.znpb-device-active__content {
					display: flex;
					align-items: center;
					margin-right: 10px;
					font-size: 16px;
				}
			}
		}
	}

	&:hover &-hidden-items {
		z-index: 9000;

		li {
			a:hover {
				color: $surface-active-color;
				background-color: lighten($surface-variant, 2%);
			}
		}
	}

	&:hover, &:active, &:focus {
		background: $primary-variant;
	}
}

/* flyout for save on the left position of the bar*/
.znpb-editor-header {
	&__page-save-wrapper {
		&.znpb-editor-header-flyout {
			.znpb-editor-header-flyout-hidden-items {
				top: auto;
				bottom: 0;
				transform-origin: left bottom;
			}
		}
	}
}

/* flyout css for different positions of the bar */

.znpb-top-area {
	.znpb-editor-header {
		.znpb-editor-header-flyout {
			.znpb-editor-header-flyout-hidden-items {
				top: 60px;
				right: 0;
				left: auto;
				width: auto;
				min-height: 150px;
				transform-origin: top right;
			}
		}
	}
}

.znpb-bottom-area {
	.znpb-editor-header {
		.znpb-editor-header-flyout {
			.znpb-editor-header-flyout-hidden-items {
				top: auto;
				right: 0;
				bottom: 60px;
				left: auto;
				width: auto;
				min-height: 150px;
				transform-origin: bottom right;
			}
		}
	}
}

.znpb-editor-header--right {
	.znpb-editor-header-flyout {
		.znpb-editor-header-flyout-hidden-items {
			right: 60px;
			left: auto;
			width: auto;
			height: auto;
			transform-origin: bottom right;
		}
	}
}
</style>
