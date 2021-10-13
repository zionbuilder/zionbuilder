<template>
	<li
		class="znpb-tree-view__item"
		:class="{'znpb-tree-view__item--hidden': !element.isVisible}"
		:id="element.uid"
		@mouseover.stop="element.highlight"
		@mouseout.stop="element.unHighlight"
		@click.stop.left="onItemClick"
		@contextmenu.stop.prevent="showElementMenu"
		ref="listItem"
	>
		<div
			class="znpb-tree-view__item-header"
			:class="{'znpb-panel-item--active': isActiveItem}"
		>
			<Icon
				icon="select"
				class="znpb-tree-view__item-header-item znpb-tree-view__item-header-expand"
				:class="{
					'znpb-tree-view__item-header-expand--expanded': element.treeViewItemExpanded
				}"
				@click.stop="element.treeViewItemExpanded = !element.treeViewItemExpanded"
				v-if="element.isWrapper"
			/>

			<UIElementIcon
				:element="elementModel"
				class="znpb-tree-view__itemIcon znpb-utility__cursor--move"
				:size="24"
			/>

			<div
				class="znpb-tree-view__item-header-item znpb-tree-view__item-header-rename"
				@input="element.name = $event.target.value"
				:contenteditable="true"
			>
				{{element.name}}
			</div>

			<Tooltip
				:content="$translate('enable_hidden_element')"
				placement="top"
				v-if="!element.isVisible"
				class="znpb-tree-view__item-enable-visible"
			>

				<transition name="fade">
					<Icon
						icon="visibility-hidden"
						class="znpb-editor-icon-wrapper--show-element"
						@click="element.toggleVisibility()"
					/>
				</transition>

			</Tooltip>

			<div
				class="znpb-element-options__container"
				@click.stop="showElementMenu"
				ref="elementOptionsRef"
			>
				<Icon
					class="znpb-element-options__dropdown-icon znpb-utility__cursor--pointer"
					icon="more"
				/>
			</div>

			<AddElementIcon
				:element="element"
				class="znpb-tree-view__itemAddButton"
				position="centered-bottom"
			/>
		</div>

		<TreeViewList
			v-if="element.treeViewItemExpanded"
			:element="element"
		/>
	</li>
</template>

<script lang="ts">
import { ref, Ref, PropType, defineComponent, watch, onMounted } from "vue";
import {
	Element,
	useElementTypes,
	useElementActions,
	useEditElement,
} from "@composables";
import { useTreeViewItem } from "../useTreeViewItem";

export default defineComponent({
	props: {
		element: Object as PropType<Element>,
	},
	setup(props) {
		const listItem: Ref = ref(null);

		const { showElementMenu, elementOptionsRef, isActiveItem } =
			useTreeViewItem(props);

		const { getElementType } = useElementTypes();

		const elementModel = getElementType(props.element.element_type);

		const onItemClick = () => {
			const { focusElement } = useElementActions();
			const { editElement } = useEditElement();

			focusElement(props.element);
			editElement(props.element);
			props.element.focus;
			props.element.scrollTo = true;
		};

		watch(
			() => isActiveItem.value,
			(newValue) => {
				if (newValue) {
					scrollToItem();
				}
			}
		);

		function scrollToItem() {
			if (listItem.value) {
				listItem.value.scrollIntoView({
					behavior: "smooth",
					inline: "nearest",
					block: "nearest",
				});
			}
		}

		onMounted(() => {
			if (isActiveItem.value) {
				scrollToItem();
			}
		});

		return {
			showElementMenu,
			elementOptionsRef,
			isActiveItem,
			onItemClick,
			listItem,
			elementModel,
		};
	},
});
</script>
<style lang="scss">
.znpb-tree-view__item {
	.znpb-element-options__dropdown-icon {
		cursor: pointer;

		&:hover {
			color: var(--zb-surface-text-hover-color);
		}
	}

	&Image {
		height: 24px;
	}
	&Image,
	&Icon {
		color: var(--zb-surface-icon-color);
	}

	&--hidden {
		.znpb-tree-view__item-header-item {
			transition: opacity 0.5s ease;
			opacity: 0.5;
		}
	}
	&-header {
		position: relative;
		display: flex;
		justify-content: space-between;
		align-items: center;
		margin-bottom: 5px;
		color: var(--zb-surface-text-active-color);
		background-color: var(--zb-surface-lighter-color);
		border-radius: 3px;

		& > *:first-child {
			padding-left: 15px;
		}

		&:hover {
			background-color: var(--zb-surface-lightest-color);
		}

		&.znpb-panel-item--active {
			color: var(--zb-secondary-text-color);
			background-color: var(--zb-secondary-color);

			.znpb-editor-icon-wrapper {
				color: var(--zb-secondary-text-color);
			}
		}

		.znpb-editor-icon-wrapper {
			color: var(--zb-surface-icon-color);
		}

		&-item {
			padding-left: 15px;
			font-weight: 500;

			&:hover {
				.znpb-editor-icon-wrapper {
					color: var(--zb-surface-icon-color);
				}
			}
		}

		&-rename {
			position: relative;
			flex-grow: 1;
			padding: 10px 8px;
			cursor: text;

			&:focus {
				outline: 0;
			}
		}

		&-expand,
		&-more {
			padding: 10px 8px;
		}

		&-expand {
			cursor: pointer;

			& > .zion-icon {
				transition: none;
			}

			&.-rotated > .zion-icon {
				transform: rotate(180deg);
			}

			&--expanded svg {
				transform: rotate(180deg);
			}
		}

		&-options-container {
			& > span {
				color: var(--zb-surface-icon-color);
				transition: all 0.2s ease;
				opacity: 0.7;
			}
			&:hover {
				& > span {
					opacity: 1;
				}
			}
		}

		.znpb-element-toolbox__add-element-button {
			--button-size: 24px;
			--font-size: 12px;
			right: 0;
			left: auto;
			margin: -16px 30px 0 0;
		}
	}
}

.znpb-tree-view__itemIcon {
	padding: 10px 8px;
}

.znpb-tree-view__itemAddButton {
	z-index: 1;
	opacity: 0;
	visibility: hidden;
}

.znpb-tree-view__item-header:hover > .znpb-tree-view__itemAddButton {
	opacity: 1;
	visibility: visible;
}

// Set the font size for the svg icons
.znpb-tree-view__itemIcon {
	font-size: 24px;
}
</style>
