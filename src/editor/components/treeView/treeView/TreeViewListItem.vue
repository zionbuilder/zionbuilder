<template>
	<li
		:id="element.uid"
		ref="listItem"
		class="znpb-tree-view__item"
		:class="{
			'znpb-tree-view__item--hidden': !element.isVisible,
			'znpb-tree-view__item--justAdded': justAdded,
			'znpb-tree-view__item--loopProvider': element.isRepeaterProvider,
			'znpb-tree-view__item--loopConsumer': element.isRepeaterConsumer,
		}"
		@mouseenter="element.highlight"
		@mouseleave="element.unHighlight"
		@click.stop.left="UIStore.editElement(element)"
		@contextmenu.stop.prevent="showElementMenu"
	>
		<div class="znpb-tree-view__item-header" :class="{ 'znpb-panel-item--active': isActiveItem }">
			<Icon
				v-if="element.isWrapper"
				icon="select"
				class="znpb-tree-view__item-header-item znpb-tree-view__item-header-expand"
				:class="{
					'znpb-tree-view__item-header-expand--expanded': expanded,
				}"
				@click.stop="expanded = !expanded"
			/>

			<UIElementIcon
				:element="element.elementDefinition"
				class="znpb-tree-view__itemIcon znpb-utility__cursor--move"
				:size="24"
			/>

			<span
				v-if="element.isRepeaterProvider"
				v-znpb-tooltip="$translate('repeater_provider')"
				class="znpb-tree-view__itemLooperIcon"
				>P</span
			>
			<span
				v-if="element.isRepeaterConsumer"
				v-znpb-tooltip="$translate('repeater_consumer')"
				class="znpb-tree-view__itemLooperIcon"
				>C</span
			>

			<InlineEdit v-model="elementName" class="znpb-tree-view__item-header-item znpb-tree-view__item-header-rename" />

			<Icon
				v-if="!element.isVisible"
				v-znpb-tooltip="translate('enable_hidden_element')"
				icon="visibility-hidden"
				class="znpb-editor-icon-wrapper--show-element znpb-tree-view__item-enable-visible"
				@click.stop="element.isVisible = !element.isVisible"
			/>

			<div ref="elementOptionsRef" class="znpb-element-options__container" @click.stop="showElementMenu">
				<Icon class="znpb-element-options__dropdown-icon znpb-utility__cursor--pointer" icon="more" />
			</div>

			<AddElementIcon :element="element" class="znpb-tree-view__itemAddButton" position="centered-bottom" />
		</div>

		<TreeViewList v-if="expanded" :element="element" />
	</li>
</template>

<script lang="ts" setup>
import { ref, Ref, computed, watch, inject } from 'vue';
import { translate } from '/@/common/modules/i18n';
import { useTreeViewItem } from '../useTreeViewItem';
import { useUIStore } from '/@/editor/store';

// Components
import TreeViewList from './TreeViewList.vue';

const props = defineProps<{
	element: ZionElement;
}>();

const UIStore = useUIStore();

const listItem: Ref<HTMLElement | null> = ref(null);

const elementName = computed({
	get: () => props.element.name,
	set(newValue: string) {
		props.element.name = newValue;
	},
});

const justAdded = ref(false);
const { showElementMenu, elementOptionsRef, isActiveItem } = useTreeViewItem(props.element);

// Highlight the element when it is first added in the page
if (UIStore.contentTimestamp) {
	justAdded.value =
		props.element.addedTime > UIStore.contentTimestamp ? Date.now() - props.element.addedTime < 1000 : false;

	if (justAdded.value) {
		setTimeout(() => {
			justAdded.value = false;
		}, 1000);
	}
}

// Scroll to item when it is edited or active
watch(
	() => UIStore.editedElement,
	newValue => {
		if (newValue === props.element) {
			if (listItem.value) {
				listItem.value.scrollIntoView({
					behavior: 'smooth',
					inline: 'center',
					block: 'center',
				});
			}
		}
	},
);

// Expanded state
const expandedItems: Ref<[]> = inject('treeViewExpandedItems');
const treeViewExpandStatus: Ref<boolean> = inject('treeViewExpandStatus');
const expanded = ref(treeViewExpandStatus.value || expandedItems.value.includes(props.element.uid) || false);

watch(treeViewExpandStatus, newValue => {
	expanded.value = newValue;
});

watch(expandedItems, newValue => {
	if (expandedItems.value.includes(props.element.uid)) {
		expanded.value = true;
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
		color: var(--zb-surface-icon-color);
		// color: var(--zb-surface-text-color);
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

			.znpb-tree-view__item-header-rename {
				color: var(--zb-secondary-text-color);
			}
		}

		&-item {
			padding-left: 15px;
			font-weight: 500;
		}

		&-rename {
			position: relative;
			flex-grow: 1;
			padding: 10px 4px;
			cursor: text;
			width: 100%;
			color: var(--zb-surface-text-color);

			&:focus {
				outline: 0;
			}
		}

		&-expand,
		&-more {
			padding: 10px 4px;
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
	padding: 10px 4px;
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

.znpb-tree-view__item--loopProvider {
	& > .znpb-tree-view__item-header {
		background-color: #14ae5c;
		color: var(--zb-secondary-text-color);

		.znpb-tree-view__item-header-rename {
			color: var(--zb-secondary-text-color);
		}
	}
}

.znpb-tree-view__item--loopConsumer {
	& > .znpb-tree-view__item-header {
		background-color: #eda926;
		color: var(--zb-secondary-text-color);

		.znpb-tree-view__item-header-rename {
			color: var(--zb-secondary-text-color);
		}
	}
}

.znpb-tree-view__item--justAdded > .znpb-tree-view__item-header {
	background: var(--zb-surface-lightest-color);
}

.znpb-tree-view__itemLooperIcon {
	display: inline-flex;
	justify-content: center;
	align-items: center;
	font-size: 10px;
	font-weight: 700;
	line-height: 1;
	background: rgba(255, 255, 255, 0.3);
	border-radius: 2px;
	padding: 3px 4px 2px 5px;
	margin-right: 2px;
	margin-left: 2px;
	cursor: default;
}
</style>
