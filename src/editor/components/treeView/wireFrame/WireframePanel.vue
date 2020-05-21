<template>
	<div
		id="znpb-wireframe-panel"
		class="znpb-tree-view-bar znpb-wireframe-container znpb-fancy-scrollbar znpb-panel-view-wrapper"
	>
		<!-- content -->
		<Sortable
			tag="ul"
			class="znpb-wireframe-view-wrapper"
			v-model="templateItems"
			group="pagebuilder-wireframe-elements"
			v-if="content.length !== 0"
		>
			<ElementWireframeView
				v-for="(elementUid, i) in templateItems"
				:parent="content"
				:parentUid="parentUid"
				:element-uid="elementUid"
				v-bind:key="i"
			/>
			<SortableHelper slot="helper" />
			<SortablePlaceholder slot="placeholder" />
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
import { mapGetters, mapActions } from 'vuex'
import { Sortable } from '@/common/vue-beautifull-dnd'
import SortablePlaceholder from '@/editor/common/SortablePlaceholder.vue'
import SortableHelper from '@/editor/common/SortableHelper.vue'

export default {
	name: 'wireframe-view',
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
		}
	},
	components: {
		Sortable,
		SortablePlaceholder,
		SortableHelper
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
	methods: {
		...mapActions([
			'saveElementsOrder',
			'setRightClickMenu'
		]),
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
	}
}
</script>
<style lang="scss">
// style panel when wireframe active
.znpb-editor-panel__container.znpb-editor-panel__container--wireframe {
	.znpb-tree-view__type_wrapper {
		overflow-y: auto;
	}
}

.znpb-wireframe-container {
	width: 100%;
	height: 100%;
	padding: 0 30px;
	margin: 0 auto;

	.znpb-wireframe__item {
		margin-right: 0;

		.znpb-wireframe__item {
			margin-right: 30px;
		}
	}
}
</style>
