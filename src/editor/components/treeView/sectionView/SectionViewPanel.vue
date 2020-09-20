<template>
	<div
		id="znpb-section-view"
		class="znpb-tree-view-container znpb-fancy-scrollbar znpb-panel-view-wrapper"
	>
		<Sortable
			class="znpb-section-view-wrapper"
			v-model="templateItems"
			tag="ul"
			group="pagebuilder-sectionview-elements"
			@start="sortableStart"
			@end="sortableEnd"
			v-if="content.length !== 0"
		>
			<ElementSectionView
				v-for="elementUid in templateItems"
				:content="content"
				:parentUid="parentUid"
				:element-uid="elementUid"
				:key="'tree-view-element-' + elementUid"
			/>
			<SortableHelper slot="helper"></SortableHelper>
			<SortablePlaceholder slot="placeholder"></SortablePlaceholder>
		</Sortable>
		<div
			class="znpb-tree-view--no_content"
			v-if="content.length === 0"
		>
			No elements added to page
		</div>
	</div>
</template>
<script>
import { mapActions, mapGetters } from 'vuex'
import ElementSectionView from './ElementSectionView.vue'
import { Sortable } from '@zb/components'
import SortableHelper from '@/editor/common/SortableHelper.vue'
import SortablePlaceholder from '@/editor/common/SortablePlaceholder.vue'

export default {
	name: 'section-view',
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
		return {}
	},
	computed: {
		...mapGetters([
			'getRightClickMenu'
		]),
		templateItems: {
			get () {
				return this.content || []
			},
			set (value) {
				this.saveElementsOrder({
					newOrder: value,
					content: this.content
				})
			}
		}
	},
	components: {
		ElementSectionView,
		Sortable,
		SortableHelper,
		SortablePlaceholder
	},
	methods: {
		...mapActions([
			'saveElementsOrder',
			'setDraggingState',
			'setRightClickMenu'
		]),
		sortableStart () {
			this.setDraggingState(true)
		},
		sortableEnd () {
			this.setDraggingState(false)
		},
		onScroll (e) {
			this.setRightClickMenu({
				editorScrollTop: parseInt(this.$el.scrollTop)
			})
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
	},
	getRightClickMenu (newValue) {
		if (newValue.visibility) {
			this.$el.addEventListener('scroll', this.onScroll)
		} else {
			this.$el.removeEventListener('scroll', this.onScroll)
		}
	}
}
</script>
<style lang="scss">
.znpb-tree-view-container {
	flex-grow: 1;
	overflow-y: auto;
	padding: 0 20px 0 20px;
}
</style>
