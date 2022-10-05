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
				v-if="!userStore.permissions.only_content"
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

			<a href="#" @click="(treeViewExpanded = !treeViewExpanded), (expandedItems = [])">
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
import { ref, Ref, computed, provide, watch } from 'vue';
import { translate } from '/@/common/modules/i18n';

// components
import TreeViewList from './TreeViewList.vue';
import { useUIStore, useUserStore } from '/@/editor/store';

const UIStore = useUIStore();
const userStore = useUserStore();
const props = defineProps<{
	element: ZionElement;
}>();

const treeViewExpanded = ref(false);
const showModalConfirm = ref(false);
const expandedItems: Ref<string[]> = ref([]);

const canRemove = computed(() => {
	return props.element.content.length === 0;
});

provide('treeViewExpandStatus', treeViewExpanded);

watch(
	() => UIStore.editedElement,
	(newElement, oldElement) => {
		if (newElement && newElement !== oldElement) {
			let parentUIDS = [newElement.uid];
			while (newElement.parent && newElement.parent.element_type !== 'contentRoot') {
				parentUIDS.push(newElement.parent.uid);

				newElement = newElement.parent;
			}

			expandedItems.value = parentUIDS;
		}
	},
);

function removeAllElements() {
	window.zb.run('editor/elements/remove_all', {
		areaID: props.element.uid,
	});

	// Close modal
	showModalConfirm.value = false;
}

provide('treeViewExpandedItems', expandedItems);
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
