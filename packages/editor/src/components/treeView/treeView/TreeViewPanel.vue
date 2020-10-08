<template>
	<div
		class="znpb-tree-view znpb-fancy-scrollbar znpb-panel-view-wrapper"
		ref="treeViewPanel"
		id="znpb-tree-view-panel"
	>
		<div
			class="znpb-tree-view--no_content"
			v-if="getPageContent.length === 0"
		>
			No elements added to page
		</div>

		<TreeViewList
			v-else
			:content="getPageContent"
		/>
	</div>
</template>
<script>
import TreeViewList from './TreeViewList.vue'
import { mapGetters, mapActions } from 'vuex'
export default {
	name: 'TreeViewPanel',
	props: {
		content: {
			type: Array,
			required: true
		},
		parentUid: {
			type: String
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
			'getElementFocus',
			'getParents'
		]),
		getPageContent () {
			return this.$zb.data.pageAreas.activeAreaContent
		}
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
