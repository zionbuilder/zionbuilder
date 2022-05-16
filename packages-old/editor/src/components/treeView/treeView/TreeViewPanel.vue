<template>
	<div class="znpb-tree-viewWrapper">
		<div class="znpb-tree-viewExpandContainer">

			<ModalConfirm
				v-if="showModalConfirm"
				:width="530"
				:confirm-text="$translate('yes_delete_elements')"
				:cancel-text="$translate('cancel')"
				@confirm="removeAllElements"
				@cancel="showModalConfirm = false"
			>
				{{$translate('are_you_sure_delete_elements')}}
			</ModalConfirm>

			<a
				href="#"
				@click="showModalConfirm = true"
				class="znpb-tree-viewRemoveButton"
				:class="{
					'znpb-tree-viewRemoveButton--disabled': canRemove
				}"
			>

				{{$translate('remove_all')}}
				<Icon
					icon="delete"
					:size="10"
				/>
			</a>

			<a
				href="#"
				@click="expandOrCollapse(element), showExpand = !showExpand"
			>
				<template v-if="showExpand">
					{{$translate('expand_all')}}
					<Icon
						icon="long-arrow-down"
						:size="10"
					/>
				</template>
				<template v-else>
					{{$translate('collapse_all')}}
					<Icon
						icon="long-arrow-up"
						:size="10"
					/>
				</template>

			</a>

		</div>

		<div class="znpb-tree-view znpb-fancy-scrollbar znpb-panel-view-wrapper">
			<TreeViewList :element="element" />
		</div>
	</div>

</template>
<script lang="ts">
import { ref, computed } from "vue";
import {
	useElements,
	useEditorData,
	useHistory,
	useEditElement,
} from "@composables";
import { translate } from "@zb/i18n";

export default {
	name: "TreeViewPanel",
	props: {
		element: {
			type: Object,
			required: true,
		},
	},
	setup(props) {
		const showExpand = ref(true);
		const showModalConfirm = ref(false);
		const canRemove = computed(() => {
			return props.element.content.length === 0;
		});

		function expandOrCollapse(element) {
			if (element.content.length > 0) {
				element.content.forEach((child) => {
					child.treeViewItemExpanded = showExpand.value;
					expandOrCollapse(child);
				});
			}
		}

		function removeAllElements() {
			const { unEditElement } = useEditElement();
			const { getElement } = useElements();
			const { editorData } = useEditorData();
			const { addToHistory } = useHistory();

			const rootElement = computed(() =>
				getElement(editorData.value.page_id)
			);

			// Delete all elements
			rootElement.value.content = [];

			// Close edit element panel
			unEditElement();

			// Add to history
			addToHistory(translate("removed_all_elements"));

			// Close modal
			showModalConfirm.value = false;
		}

		return {
			canRemove,
			showExpand,
			showModalConfirm,
			expandOrCollapse,
			removeAllElements,
		};
	},
};
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
		transition: color .15s;

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
