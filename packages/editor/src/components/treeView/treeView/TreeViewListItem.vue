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

			<img
				v-if="get_element_image"
				:src="get_element_image"
				class="znpb-tree-view__itemIcon znpb-utility__cursor--move"
			/>

			<Icon
				v-else
				:icon="get_element_icon"
				:size="24"
				class="znpb-tree-view__itemIcon znpb-utility__cursor--move"
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

		const { getElementIcon, getElementImage } = useElementTypes();

		const get_element_image = getElementImage(props.element.element_type);
		const get_element_icon = getElementIcon(props.element.element_type);

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
			get_element_image,
			get_element_icon,
			listItem,
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
	&Image, &Icon {
		color: var(--zb-surface-icon-color);
	}

	&--hidden {
		.znpb-tree-view__item-header-item {
			transition: opacity .5s ease;
			opacity: .5;
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

		& > *:last-child {
			padding-right: 15x;
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

		&-expand, &-more {
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
				transition: all .2s ease;
				opacity: .7;
			}
			&:hover {
				& > span {
					opacity: 1;
				}
			}
		}
	}
}

.znpb-tree-view__itemIcon {
	padding: 10px 8px;
}
</style>
