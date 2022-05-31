<template>
	<BasePanel
		ref="basePanel"
		:panel-name="translate('tree_view_panel')"
		panel-id="panel-tree"
		:css-class="activeTreeViewPanel.basePanelCssClass"
		:expanded="activeTreeViewPanel.expandMainPanel"
		:show-header="activeTreeViewPanel.showBasePanelHeader"
		:show-expand="false"
		:panel="panel"
	>
		<div class="znpb-tree-view__header">
			<div class="znpb-tree-view__header-menu">
				<div
					v-for="(treeType, index) in treeViewTypes"
					:key="index"
					class="znpb-tree-view__header-menu-item"
					:class="{ 'znpb-tree-view__header-menu-item--active': activeTreeViewPanel.id === treeType.id }"
					@click="activateTree(treeType)"
				>
					<Icon class="znpb-tree-view__header-menu-item-icon" :icon="treeType.icon" :size="16" />
					<h4>{{ treeType.name }}</h4>
				</div>
			</div>
			<Icon
				v-if="activeTreeViewPanel.id === 'WireframeView'"
				class="znpb-tree-view__header-menu-close-icon"
				icon="close"
				@click="closeWireframe"
			/>
		</div>

		<Loader v-if="UIStore.isPreviewLoading" />

		<div v-else class="znpb-tree-view__type_wrapper">
			<component :is="activeTreeViewPanel.component" :element="element" />
		</div>
	</BasePanel>
</template>

<script lang="ts" setup>
import { type Component, ref, computed } from 'vue';
import { translate } from '@/common/modules/i18n';
import { useUIStore, useContentStore } from '@/editor/store';
import SectionView from './sectionView/SectionViewPanel.vue';
import TreeView from './treeView/TreeViewPanel.vue';
import WireframeView from './wireFrame/WireframePanel.vue';
import BasePanel from '../BasePanel.vue';

const props = defineProps<{
	panel: ZionPanel;
}>();

const UIStore = useUIStore();

const contentStore = useContentStore();
const element = computed(() => contentStore.getElement(window.ZnPbInitialData.page_id));

type TreeViewPanel = {
	name: string;
	id: string;
	component: Component;
	icon: string;
	basePanelCssClass?: string;
	expandMainPanel?: boolean;
	showBasePanelHeader?: boolean;
};

// Tree view types
const treeViewTypes: TreeViewPanel[] = [
	{
		name: translate('tree_view'),
		id: 'TreeView',
		component: TreeView,
		icon: 'treeview',
	},
	{
		name: translate('section_view'),
		id: 'SectionView',
		component: SectionView,
		icon: 'structure',
	},
	{
		name: translate('wireframe_view'),
		id: 'WireframeView',
		component: WireframeView,
		icon: 'layout',
		basePanelCssClass: ' znpb-editor-panel__container--wireframe',
		expandMainPanel: true,
		showBasePanelHeader: false,
	},
];

const activeTreeViewId = ref(treeViewTypes[0].id);
const activeTreeViewPanel = computed(
	() => treeViewTypes.find(treeType => treeType.id === activeTreeViewId.value) || treeViewTypes[0],
);
const basePanel = ref(null);
const panelDetachedState = ref(null);

const activateTree = (treeType: TreeViewPanel) => {
	activeTreeViewId.value = treeType.id;
	if (treeType.id === 'WireframeView') {
		if (basePanel.value) {
			panelDetachedState.value = basePanel.value.panel.isDetached;
			UIStore.updatePanel(props.panel.id, 'isDetached', false);
		}
	} else {
		if (panelDetachedState.value) {
			UIStore.updatePanel(props.panel.id, 'isDetached', panelDetachedState.value);
			panelDetachedState.value = null;
		}
	}
};

const closeWireframe = () => {
	UIStore.closePanel(props.panel.id);
};
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
	color: var(--zb-surface-text-color);
	text-align: center;
	background-color: var(--zb-surface-color);

	&-menu {
		display: flex;
		align-items: center;
		flex-grow: 1;
		max-width: 320px;
		padding: 3px;
		margin: 0 auto;
		background-color: var(--zb-surface-lighter-color);
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
				background-color: var(--zb-surface-lightest-color);

				.znpb-editor-icon-wrapper {
					color: var(--zb-surface-text-hover-color);
				}

				.zion-icon.zion-svg-inline {
					color: var(--zb-surface-text-hover-color);
				}
			}

			&--active {
				color: var(--zb-secondary-text-color);
				background-color: var(--zb-secondary-color);

				.znpb-editor-icon-wrapper {
					color: var(--zb-secondary-text-color);
				}

				.zion-icon.zion-svg-inline {
					color: var(--zb-secondary-text-color);
				}

				&:hover {
					background-color: var(--zb-secondary-color);
				}
			}

			&-icon {
				margin-bottom: 8px;
				color: var(--zb-surface-icon-color);
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
			color: var(--zb-surface-icon-color);
			cursor: pointer;

			&:hover {
				color: var(--zb-surface-text-color);
			}
		}
	}

	&-close-button {
		@include background-icon(30px);
		align-self: center;
		margin-right: 30px;
		&:hover {
			background: var(--zb-surface-text-hover-color);
		}
	}
}

.znpb-tree-view__type_wrapper {
	display: flex;
	flex-direction: column;
	flex-grow: 1;
	min-height: 1px;

	.znpb-editor-icon path {
		opacity: 1 !important;
	}
}
</style>
