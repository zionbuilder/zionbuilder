<template>
	<BasePanel
		ref="basepanel"
		@close-panel="panel.close()"
		:panel-name="$translate('tree_view_panel')"
		panel-id="panel-tree"
		:css-class="activeTreeView.basePanelCssClass"
		:expanded="activeTreeView.expandMainPanel"
		:show-header="activeTreeView.showBasePanelHeader"
		:show-expand="false"
		:panel="panel"
	>
		<div
			class="znpb-tree-view__header"
		>
			<div class="znpb-tree-view__header-menu">
				<div
					class="znpb-tree-view__header-menu-item"
					v-for="(treeType, index) in treeViewTypes"
					@click="activateTree(treeType)"
					:class="{'znpb-tree-view__header-menu-item--active': activeTreeView.id===treeType.id}"
				>
					<Icon
						class="znpb-tree-view__header-menu-item-icon"
						:icon="treeType.icon"
						:size="16"
					/>
					<h4>{{ treeType.name }}</h4>
				</div>
			</div>
			<Icon
				v-if="activeTreeView.id === 'WireframeView'"
				class="znpb-tree-view__header-menu-close-icon"
				icon="close"
				@click="closeWireframe"
			/>
		</div>

		<Loader v-if="isPreviewLoading" />

		<div
			v-if="element && !isPreviewLoading"
			class="znpb-tree-view__type_wrapper"
		>
			<component
				:is="activeTreeView.id"
				:element="element"
			/>
		</div>
	</BasePanel>
</template>

<script>
import { ref, computed } from 'vue'
import SectionView from './sectionView/SectionViewPanel.vue'
import TreeView from './treeView/TreeViewPanel.vue'
import WireframeView from './wireFrame/WireframePanel.vue'
import BasePanel from '../BasePanel.vue'
import { useElements, usePreviewLoading } from '@data'
import { translate } from '@zb/i18n'

export default {
	name: 'panel-tree',
	components: {
		TreeView,
		SectionView,
		WireframeView,
		BasePanel
	},
	props: {
		panel: {}
	},
	setup (props) {
		const { getElement } = useElements()
		const element = computed(() => getElement('content'))
		const { isPreviewLoading } = usePreviewLoading()
		const myReactiveValue = ref(true)

		// Tree view types
		const treeViewTypes = [{
			name: translate('tree_view'),
			id: 'TreeView',
			icon: 'treeview'
		},
		{
			name: translate('section_view'),
			id: 'SectionView',
			icon: 'structure'
		},
		{
			name: translate('wireframe_view'),
			id: 'WireframeView',
			icon: 'layout',
			basePanelCssClass: ' znpb-editor-panel__container--wireframe',
			expandMainPanel: true,
			showBasePanelHeader: false
		}]

		const activeTreeView = ref(treeViewTypes[0])
		const basepanel = ref(null)
		const panelDetachedState = ref(null)

		const activateTree = (treeType) => {
			activeTreeView.value = treeType
			if (treeType.id === 'WireframeView') {
				panelDetachedState.value = basepanel.value.panel.isDetached
				props.panel.set('isDetached', false)
			} else {
				if (panelDetachedState.value) {
					props.panel.set('isDetached', panelDetachedState.value)
					panelDetachedState.value = null
				}
			}
		}

		const closeWireframe = () => {
			props.panel.close()
		}

		return {
			element,
			activeTreeView,
			treeViewTypes,
			activateTree,
			closeWireframe,
			basepanel,
			isPreviewLoading
		}
	}
}
</script>
<style lang="scss">
/* style panel */
.znpb-editor-panel__container--wireframe {
	width: 100% !important;
}
.znpb-tree-view__header {
	display: flex;
	justify-content: center;
	flex-shrink: 0;
	width: 100%;
	padding: 20px 20px 0 20px;
	margin-bottom: 20px;
	color: $font-color;
	text-align: center;
	background-color: $surface;

	&-menu {
		display: flex;
		align-items: center;
		flex-grow: 1;
		max-width: 320px;
		padding: 3px;
		margin: 0 auto;
		background-color: $surface-variant;
		border-radius: 3px;

		&-item {
			display: flex;
			flex-direction: column;
			align-items: center;
			width: calc(100% / 3);
			padding: 15px 10px;
			border-radius: 2px;
			cursor: pointer;

			&:hover {
				background-color: darken($surface-variant, 3%);
			}

			&--active {
				color: $primary-color--accent;
				background-color: $secondary;
				.zion-icon.zion-svg-inline {
					color: $primary-color--accent;
				}

				&:hover {
					background-color: $secondary;
				}
			}

			&-icon {
				margin-bottom: 8px;
				color: $surface-headings-color;
				font-size: 20px;
				font-weight: bold;
			}

			h4 {
				font-size: 13px;
				font-weight: 500;
			}
		}

		&-close-icon {
			position: absolute;
			top: 30px;
			right: 25px;
			align-self: baseline;
			padding: 15px;
			margin-top: -15px;
			color: $surface-headings-color;
			cursor: pointer;

			&:hover {
				color: $font-color;
			}
		}
	}

	&-close-button {
		@include background-icon(30px);
		align-self: center;
		margin-right: 30px;
		&:hover {
			background: darken($surface-variant, 10%);
		}
	}
}

.znpb-tree-view__type_wrapper {
	display: flex;
	flex-direction: column;
	flex-grow: 1;
	min-height: 1px;
}
</style>
