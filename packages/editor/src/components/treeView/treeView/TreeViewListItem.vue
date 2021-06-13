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
				class="znpb-tree-view__item-header-item znpb-tree-view__item-header-expand znpb-utility__cursor--pointer"
				:class="{
					'znpb-tree-view__item-header-expand--expanded': element.treeViewItemExpanded
				}"
				@click.stop="element.treeViewItemExpanded = !element.treeViewItemExpanded"
				v-if="element.isWrapper"
			/>

			<img
				v-if="get_element_image"
				:src="get_element_image"
				class="znpb-tree-view__itemImage"
			/>

			<Icon
				v-else
				:icon="get_element_icon"
				:size="24"
				class="znpb-tree-view__itemIcon"
			/>

			<InlineEdit
				class="znpb-tree-view__item-header-item znpb-tree-view__item-header-rename"
				v-model="element.name"
				v-model:active="element.activeElementRename"
			/>

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
import { Element, useElementTypes } from "@composables";
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
			color: darken($font-color, 15%);
		}
	}

	&Image {
		height: 24px;
	}
	&Image, &Icon {
		padding-left: 15px;
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
		background-color: $surface-variant;
		border-radius: 3px;

		&:hover {
			background-color: darken($surface-variant, 3%);
		}

		&.znpb-panel-item--active {
			color: $surface;
			background-color: $secondary;
		}

		&-item {
			padding-left: 15px;

			&:hover {
				cursor: pointer;
				.znpb-editor-icon-wrapper {
					color: darken($primary-color--accent, 10%);
				}
			}
		}
		&-rename {
			position: relative;
			flex-grow: 1;
			padding-top: 15px;
			padding-right: 15px;
			padding-bottom: 15px;
			cursor: text;
			&:focus {
				outline: 0;
			}
		}

		&-expand, &-more {
			padding: 15px;
		}

		&-expand {
			padding-right: 0;

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
</style>
