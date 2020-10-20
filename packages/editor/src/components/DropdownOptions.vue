<template>
	<div
		class="znpb-element-options__container"
		@click.stop="toggleOptions"
		v-click-outside="hideOptions"
	>
		<Icon
			class="znpb-element-options__dropdown-icon znpb-utility__cursor--pointer"
			icon="more"
		/>
		<transition name="list">
			<ElementActions
				class="znpb-element-options__element-actions"
				v-if="showOptions && getElementFocus"
				@close="close"
				@changename="$emit('changename',true), showOptions=false"
			/>
		</transition>

	</div>
</template>
<script>
import { mapGetters, mapActions } from 'vuex'
import templateElementMixin from '../mixins/templateElement.js'
import clickOutside from '@zionbuilder/click-outside-directive'
import ElementActions from '../common/ElementActions.vue'

export default {
	name: 'DropdownOptions',
	mixins: [
		templateElementMixin
	],
	components: {
		ElementActions
	},
	props: {
		elementUid: String,
		parentUid: String,
		isActive: {
			type: Boolean,
			required: false
		}
	},
	data: function () {
		return {
			showOptions: false
		}
	},
	directives: {
		clickOutside
	},

	computed: {
		...mapGetters([
			'isDragging',
			'getRightClickMenu',
			'getElementFocus',
			'getElementData'
		]),
		elementVisibilityValue: function () {
			let elVisibility = this.getElementData(this.elementUid)['options']['_isVisible']
			return elVisibility
		},
		rightClickOpen () {
			if (this.getRightClickMenu) {
				return this.getRightClickMenu.visibility
			}

			return null
		}
	},
	methods: {
		...mapActions([
			'copyElement',
			'deleteElement',
			'updateElementOptionValue',
			'setElementFocus',
			'setRightClickMenu'
		]),
		close () {
			this.showOptions = false
		},
		updateElementVisibility (payload) {
			this.updateElementOptionValue({
				elementUid: this.elementUid,
				path: '_isVisible',
				newValue: payload,
				type: 'visibility'
			})
		},
		toggleElement () {
			if (this.elementVisibilityValue === true || this.elementVisibilityValue === undefined) {
				this.updateElementVisibility(false)
			} else {
				this.updateElementVisibility(true)
			}
		},
		toggleOptions () {
			this.showOptions = !this.showOptions && !this.isDragging

			this.setElementFocus({
				uid: this.elementUid,
				parentUid: this.parentUid,
				insertParent: this.elementModel.wrapper ? this.parentUid : this.elementUid,
				scrollIntoView: false
			})
			this.setRightClickMenu({
				visibility: false
			})
		},
		hideOptions () {
			this.showOptions = false
		}
	},
	watch: {
		rightClickOpen (newValue) {
			if (!newValue) {
				this.hideOptions()
			} else {
				this.setRightClickMenu({
					editorScrollTop: document.getElementsByClassName('znpb-panel-view-wrapper')[0].scrollTop
				})
			}
		},
		isActive (newValue) {
			if (!newValue) {
				this.hideOptions()
			}
		}
	}
}
</script>
<style lang="scss">
.znpb-element-options {
	&__dropdown-icon {
		padding: 15px;
	}

	&__options-container {
		position: relative;
		position: absolute;
		top: 100%;
		right: 0;
		z-index: 9999;
		width: 172px;
		padding: 12px 0;
		margin-top: -1px;
		text-align: left;
		list-style-type: none;
		background: $surface;
		box-shadow: 0 0 16px 0 rgba(0, 0, 0, .08);
		border-radius: 3px;
		transition: all .5s;
		user-select: none;

		li {
			min-width: 172px;
			padding: 12px 29px;
			color: $font-color;
			font-size: 12px;
			line-height: 14px;
			transition: color .2s ease;
			&:hover {
				color: $surface-active-color;
				cursor: pointer;
			}
		}
	}
}

.list-enter-to, .list-leave-from {
	transition: all .2s;
}

.list-enter-from, .list-leave-to {
	transform: translateY(10%);
	opacity: 0;
}
</style>
