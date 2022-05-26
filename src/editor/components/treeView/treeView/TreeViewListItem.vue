<template>
	<li
		:id="elementUid"
		ref="listItem"
		class="znpb-tree-view__item"
		:class="{
			'znpb-tree-view__item--hidden': !isVisible,
			'znpb-tree-view__item--justAdded': justAdded,
		}"
		@mouseover.stop="highlight"
		@mouseout.stop="unHighlight"
		@click.stop.left="editElement"
		@contextmenu.stop.prevent="showElementMenu"
	>
		<div class="znpb-tree-view__item-header" :class="{ 'znpb-panel-item--active': isActiveItem }">
			<Icon
				v-if="isWrapper"
				icon="select"
				class="znpb-tree-view__item-header-item znpb-tree-view__item-header-expand"
				:class="{
					'znpb-tree-view__item-header-expand--expanded': element?.treeViewItemExpanded,
				}"
				@click.stop="element.treeViewItemExpanded = !element?.treeViewItemExpanded"
			/>

			<UIElementIcon :element="elementModel" class="znpb-tree-view__itemIcon znpb-utility__cursor--move" :size="24" />

			<InlineEdit v-model="elementName" class="znpb-tree-view__item-header-item znpb-tree-view__item-header-rename" />

			<Icon
				v-if="!isVisible"
				v-znpb-tooltip="$translate('enable_hidden_element')"
				icon="visibility-hidden"
				class="znpb-editor-icon-wrapper--show-element znpb-tree-view__item-enable-visible"
				@click.stop="toggleVisibility"
			/>

			<div ref="elementOptionsRef" class="znpb-element-options__container" @click.stop="showElementMenu">
				<Icon class="znpb-element-options__dropdown-icon znpb-utility__cursor--pointer" icon="more" />
			</div>

			<AddElementIcon :element="element" class="znpb-tree-view__itemAddButton" position="centered-bottom" />
		</div>

		<TreeViewList v-if="element?.treeViewItemExpanded" :element="element" />
	</li>
</template>

<script lang="ts" setup>
import { ref, Ref, watch, onMounted } from 'vue';
import { usePreviewLoading, useElementUtils } from '../../../composables';
import { useTreeViewItem } from '../useTreeViewItem';

const props = defineProps<{
	elementUid: string;
}>();

const listItem: Ref = ref(null);
const { element, elementName, isVisible, highlight, unHighlight, toggleVisibility, isWrapper } = useElementUtils(
	props.elementUid,
);
const { showElementMenu, elementOptionsRef, isActiveItem, editElement, elementModel } = useTreeViewItem({
	element,
});

const { contentTimestamp } = usePreviewLoading();
let justAdded = false;
if (contentTimestamp.value) {
	const justAdded = ref(element.addedTime > contentTimestamp.value ? Date.now() - element.addedTime < 1000 : null);
	if (justAdded.value) {
		setTimeout(() => {
			justAdded.value = false;
		}, 1000);
	}
}

watch(
	() => isActiveItem.value,
	newValue => {
		if (newValue) {
			scrollToItem();
		}
	},
);

function scrollToItem() {
	if (listItem.value) {
		listItem.value.scrollIntoView({
			behavior: 'smooth',
			inline: 'center',
			block: 'center',
		});
	}
}

onMounted(() => {
	if (isActiveItem.value) {
		scrollToItem();
	}
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
		color: var(--zb-surface-text-color);
		background-color: var(--zb-surface-lighter-color);
		border-radius: 3px;
		box-shadow: -3px 0 0 0 var(--zb-surface-color);
		margin-bottom: 5px;
		transition: background-color 0.2s;

		& > *:first-child {
			padding-left: 15px;
		}

		&:hover {
			color: var(--zb-surface-text-hover-color);
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
			margin: -19px 45px 0 0;
		}
	}
}

.znpb-tree-view__itemIcon {
	padding: 10px 8px;
	font-size: 24px;

	& svg {
		pointer-events: none;
	}
}

.znpb-tree-view__itemAddButton {
	z-index: 1;
	opacity: 0;
	visibility: hidden;
	transform: translateX(10px);
	transition: all 0.1s;
}

.znpb-tree-view__item-header:hover > .znpb-tree-view__itemAddButton,
.znpb-element-toolbox__add-element-button--active {
	opacity: 1;
	visibility: visible;
	transform: translateY(0);

	&:hover::before {
		transform: scale(1.1);
	}
}

.znpb-tree-view__item--justAdded > .znpb-tree-view__item-header {
	background: #3a8f6f;
	color: var(--zb-secondary-text-color);

	.znpb-editor-icon-wrapper {
		color: var(--zb-secondary-text-color);
	}
}
</style>
