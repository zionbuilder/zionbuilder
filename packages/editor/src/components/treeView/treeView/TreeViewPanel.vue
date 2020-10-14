<template>
	<div
		class="znpb-tree-view znpb-fancy-scrollbar znpb-panel-view-wrapper"
		ref="treeViewPanel"
		id="znpb-tree-view-panel"
	>
		<TreeViewList
			:element="element"
		/>
	</div>
</template>
<script>
import TreeViewList from './TreeViewList.vue'
import { mapGetters, mapActions } from 'vuex'
import { Element } from '@data'

export default {
	name: 'TreeViewPanel',
	props: {
		element: {
			type: Element,
			required: true
		}
	},
	data: function () {
		return {
			parentsToExpand: []
		}
	},
	components: {
		TreeViewList
	},
	computed: {
		...mapGetters([
			'getRightClickMenu',
			'getParents'
		])
	},
	methods: {
		...mapActions([
			'setRightClickMenu'
		]),
		onScroll (e) {
			this.setRightClickMenu({
				editorScrollTop: parseInt(this.$el.scrollTop)
			})
		},
		getParentsArray (parents) {
			if (parents.uid !== 'contentRoot') {
				this.parentsToExpand.push(parents.uid)
			}
			for (let element in parents) {
				if (element === 'children' && parents[element].length) {
					this.getParentsArray(parents[element][0])
				}
			}
		},
		onScrollToItem (event) {
			if (!this.getRightClickMenu.visibility) {
				this.parentsToExpand = []
				this.getParentsArray(this.getParents)
				const activeItem = event
				const parentId = activeItem.closest('.znpb-tree-view__item').id
				activeItem.scrollIntoView({
					behavior: 'smooth'
				})
			}
		},
		getParentById (id) {
			return document.getElementById(id)
		}
	},
	watch: {
		getRightClickMenu (newValue) {
			if (newValue.visibility) {
				this.$el.addEventListener('scroll', this.onScroll)
			} else {
				this.$el.removeEventListener('scroll', this.onScroll)
			}
		}
	}
}
</script>
<style lang="scss">
.znpb-tree-view--no_content {
	padding: 0 30px;
	text-align: center;
}
.znpb-tree-view {
	flex-grow: 1;
	overflow-x: hidden;
	overflow-y: auto;
}
</style>
