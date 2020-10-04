<template>
	<ElementActions
		:style="positionStyles"
		v-click-outside="close"
		@close="close"
		:show-rename="showRename"
		@changename="triggerRename"
	/>
</template>

<script>

// Utils
import { mapGetters, mapActions } from 'vuex'
import clickOutside from '@zionbuilder/click-outside-directive'

// Components
import ElementActions from '../../../editor/src/common/ElementActions.vue'
import { trigger } from '@zb/hooks'

export default {
	name: 'RightClickMenu',
	directives: {
		clickOutside
	},
	props: {
		showRename: {
			type: Boolean,
			required: false,
			default: true
		}
	},
	data () {
		return {
			height: null,
			width: null
		}
	},
	components: {
		ElementActions
	},
	computed: {
		...mapGetters([
			'getCopiedElement',
			'getCopiedClasses',
			'getRightClickMenu',
			'getElementFocus'
		]),
		position () {
			if (this.getRightClickMenu && this.getRightClickMenu.position) {
				const { previewIframeLeft, source, editorScrollTop, previewScrollTop } = this.getRightClickMenu
				const { left, top } = this.getRightClickMenu.position
				const outOfHeightBoundary = top + this.height > window.innerHeight
				const outOfWidthBoundary = left + previewIframeLeft + this.width > window.innerWidth
				const heightBoundary = outOfHeightBoundary ? this.height : 0
				const widthBoundary = outOfWidthBoundary ? this.width : 0
				return {
					widthBoundary,
					heightBoundary,
					position: this.getRightClickMenu.position,
					initialScrollTop: this.getRightClickMenu.initialScrollTop,
					scrollTop: source === 'editor' ? editorScrollTop || 0 : previewScrollTop || 0,
					previewIframeLeft: this.getRightClickMenu.previewIframeLeft || 0
				}
			}

			return {}
		},
		positionStyles () {
			if (this.position) {
				return {
					'left': this.position.position.left + this.position.previewIframeLeft - this.position.widthBoundary + 'px',
					'top': this.position.position.top + this.position.initialScrollTop - this.position.heightBoundary + 'px',
					'transform': `translateY(${-this.position.scrollTop}px)`
				}
			}
			return {}
		}
	},
	mounted () {
		this.height = this.$el.clientHeight
		this.width = this.$el.clientWidth
	},
	methods: {
		...mapActions([
			'setRightClickMenu'
		]),
		close () {
			this.setRightClickMenu({
				visibility: false
			})
		},
		triggerRename () {
			trigger('rename-element', true)
			this.close()
		}
	}
}
</script>
