<template>
	<div class="znpb-tree-viewWrapper">
		<div class="znpb-tree-viewExpandContainer">
			<ModalConfirm
				v-if="showModalConfirm"
				:width="530"
				:confirm-text="translate('yes_delete_elements')"
				:cancel-text="translate('cancel')"
				@confirm="removeAllElements"
				@cancel="showModalConfirm = false"
			>
				{{ translate('are_you_sure_delete_elements') }}
			</ModalConfirm>

			<a
				href="#"
				class="znpb-tree-viewRemoveButton"
				:class="{
					'znpb-tree-viewRemoveButton--disabled': canRemove,
				}"
				@click="showModalConfirm = true"
			>
				{{ translate('remove_all') }}
				<Icon icon="delete" :size="10" />
			</a>

			<a href="#" @click="treeViewExpanded = !treeViewExpanded">
				<template v-if="!treeViewExpanded">
					{{ translate('expand_all') }}
					<Icon icon="long-arrow-down" :size="10" />
				</template>
				<template v-else>
					{{ translate('collapse_all') }}
					<Icon icon="long-arrow-up" :size="10" />
				</template>
			</a>
		</div>

		<div class="znpb-tree-view znpb-fancy-scrollbar znpb-panel-view-wrapper">
			<TreeViewList :element="element" />
		</div>
	</div>
</template>
<script lang="ts" setup>
import { ref, computed, provide } from 'vue';
import { useHistory } from '/@/editor/composables';
import { translate } from '/@/common/modules/i18n';
import { useContentStore, useUIStore } from '/@/editor/store';

// components
import TreeViewList from './TreeViewList.vue';

const props = defineProps<{
	element: ZionElement;
}>();

const UIStore = useUIStore();
const contentStore = useContentStore();
const treeViewExpanded = ref(false);
const showModalConfirm = ref(false);
const { addToHistory } = useHistory();

const canRemove = computed(() => {
	return props.element.content.length === 0;
});

provide('treeViewExpandStatus', treeViewExpanded);

function removeAllElements() {
	// clear area content
	contentStore.clearAreaContent(props.element.uid);

	// Close edit element panel
	UIStore.unEditElement();

	// Add to history
	addToHistory(translate('removed_all_elements'));

	// Close modal
	showModalConfirm.value = false;
}
</script>
<style lang="scss">
.znpb-tree-viewExpandContainer {
	display: flex;
	justify-content: flex-end;
	padding: 0 20px;
	margin-bottom: 12px;

	a {
		display: flex;
		align-items: center;
		margin-left: 12px;
		color: var(--zb-surface-text-color);
		font-size: 11px;
		font-weight: 700;
		transition: color 0.15s;

		&:hover {
			color: var(--zb-surface-text-hover-color);
		}
	}

	.znpb-editor-icon-wrapper {
		position: relative;
		top: -1px;
		margin-left: 3px;
	}
}

.znpb-tree-viewWrapper {
	display: flex;
	flex-direction: column;
	max-height: 100%;
}

.znpb-tree-view--no_content {
	padding: 0 30px;
	text-align: center;
}
.znpb-tree-view {
	flex-grow: 1;
	overflow-x: hidden;
	overflow-y: auto;
}

.znpb-tree-viewRemoveButton {
	&--disabled {
		display: none;
	}
}
</style>
