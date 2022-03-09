<template>
	<div
		class="znpb-editor-header-flyout"
		@mouseover.stop="onMouseOver"
		@mouseleave.stop="onMouseOut"
		ref="root"
	>
		<div class="znpb-editor-header__menu_button">
			<slot name="panel-icon"></slot>
		</div>

		<ul
			class="znpb-editor-header-flyout-hidden-items znpb-editor-header__menu-list"
			v-if="showflyout"
			:style="computedStyles"
			ref="listContainer"
		>
			<slot></slot>
		</ul>

	</div>
</template>
<script setup>
import { ref, computed, watch, nextTick, onBeforeUnmount } from 'vue'

const props = defineProps({
	items: {
		type: Array,
		required: false,
		default() {
			return []
		}
	},
	preventClose: {
		type: Boolean,
		required: false,
		default: false
	}
})

const showflyout = ref(false)
const listContainer = ref(null)
const negativeMargin = ref(0)
const root = ref(null)

const computedStyles = computed(() => {
	let styles = {}

	if (negativeMargin.value !== 0) {
		styles.transform = `translateY(${negativeMargin.value}px)`
	}

	return styles
})

function onMouseOver() {
	showflyout.value = true
}

const emit = defineEmits(['show', 'hide'])

function onMouseOut () {
	if (!props.preventClose) {
		showflyout.value = false
	}
}


watch(showflyout, (newValue) => {
	if (newValue) {
		nextTick(() => {
			positionDropdown()
			resizeObserver.observe(listContainer.value)
		})

		emit('show')
	} else {
		negativeMargin.value = 0
		resizeObserver.unobserve(listContainer.value)
		emit('hide')
	}
})

watch(() => props.preventClose, (newValue) => {
	if (newValue) {
		window.addEventListener('click', onOutsideClick)
	}
})

onBeforeUnmount(() => {
	window.removeEventListener('click', onOutsideClick)
})

/**
 * Closes the flyout if clicked outside of it
 */
function onOutsideClick(event) {
	if (!root.value.contains(event.target)) {
		showflyout.value = false
	}
}

function positionDropdown() {
	negativeMargin.value = 0

	nextTick(() => {
		const { bottom } = listContainer.value.getBoundingClientRect()


		if (bottom > window.innerHeight) {
			negativeMargin.value = (bottom - window.innerHeight) * -1
		}
	})
}

const resizeObserver = new ResizeObserver(entries => {
	for (let entry of entries) {
		nextTick(() => {
			positionDropdown()
		})
	}
})
</script>

<style lang="scss">
ul.znpb-editor-header-flyout-hidden-items {
	@extend %tooltip;
	padding: 8px 0;
	font-weight: 500;
	box-shadow: 0 2px 15px 0 var(--zb-dropdown-shadow);
	border-bottom-left-radius: 0;
	border-top-left-radius: 0;
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
				color: var(--zb-surface-text-color);
				font-size: 13px;
				line-height: 1;
				text-decoration: none;
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
				color: var(--zb-surface-text-active-color);
				background-color: var(--zb-surface-lighter-color);
			}
		}
	}

	&:hover, &:active, &:focus {
		background: var(--zb-primary-hover-color);
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

.znpb-editorHeaderPosition--top {
	.znpb-editor-header {
		.znpb-editor-header-flyout {
			.znpb-editor-header-flyout-hidden-items {
				top: 60px;
				right: 0;
				bottom: auto;
				left: auto;
				width: auto;
				transform-origin: top right;
			}
		}
	}
}

.znpb-editorHeaderPosition--bottom {
	.znpb-editor-header {
		.znpb-editor-header-flyout {
			.znpb-editor-header-flyout-hidden-items {
				top: auto;
				right: 0;
				bottom: 60px;
				left: auto;
				width: auto;
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
