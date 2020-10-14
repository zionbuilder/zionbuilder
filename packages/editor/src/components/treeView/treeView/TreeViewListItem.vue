<template>
	<li
		class="znpb-tree-view__item"
		:class="{'znpb-tree-view__item--hidden': !element.isVisible}"
		:id="element.uid"
		@mouseenter.capture="element.highlight"
		@mouseleave="element.unHighlight"
		@click.stop.left="element.focus"
	>
		<div
			class="znpb-tree-view__item-header"
		>
			<Icon
				icon="select"
				class="znpb-tree-view__item-header-item znpb-tree-view__item-header-expand znpb-utility__cursor--pointer"
				:class="{
					'znpb-tree-view__item-header-expand--expanded': expanded
				}"
				@click.stop="expanded = !expanded"
				v-if="element.isWrapper"
			/>

			<InlineEdit
				class="znpb-tree-view__item-header-item znpb-tree-view__item-header-rename"
				v-model="elementName"
			/>

			<Tooltip
				:content="$translate('enable_hidden_element')"
				placement="top"
			>
				<span>
					<transition name="fade">
						<Icon
							icon="visibility-hidden"
							v-if="!element.isVisible"
							class="znpb-editor-icon-wrapper--show-element"
							@click="element.delete()"
						/>
							<!-- @click="element.isVisible = true" -->
					</transition>
				</span>
			</Tooltip>

			<DropdownOptions
				:element-uid="element.uid"
			/>
		</div>
		<TreeViewList
			v-if="expanded"
			:element="element"
		/>
	</li>

	<!-- <li
		class="znpb-tree-view__item"
		:class="{'znpb-tree-view__item--hidden': !isElementVisible}"
		@mouseenter.capture="highlightElement"
		@mouseleave="unHighlightElement"
		@mouseup="onMouseup"
		@click.stop.left="onItemClick"
		@contextmenu.stop.prevent="showContextMenu"
		@dblclick.stop="openOptions"
		:id="elementUid"
	>
		<div
			class="znpb-tree-view__item-header"
			:class="{'znpb-panel-item--hovered': hovered, 'znpb-panel-item--active': isActiveItem}"
		>
			<ElementRename
				class="znpb-tree-view__item-header-item znpb-tree-view__item-header-rename"
				@name-changed="doRenameElement"
				@dblclick="isNameChangeActive=true"
				:is-active="isNameChangeActive"
				:content="elementName"
			/>

			<DropdownOptions
				:element-uid="elementUid"
				:parentUid="parentUid"
				:position="dropdownPosition"
				:isActive="isActiveItem"
				@changename="isNameChangeActive=true"
			/>
		</div>
		<TreeViewList
			:content="templateItems"
			v-show="expanded && elementModel.wrapper"
			:elementUid="elementUid"
			:parentsToExpand="parentsToExpand"
			:addButton="elementModel.wrapper && expanded"
			:addButtonColor="addElementIconColor"
			@scroll-to-item="onScrollToItem"
		/>
	</li> -->
</template>
<script lang="ts">
import { ref, PropType, defineComponent, computed } from 'vue'
import { mapActions, mapGetters } from 'vuex'
import DropdownOptions from '../../DropdownOptions.vue'
import { on } from '@zb/hooks'
import { Element } from '@data'

export default defineComponent({
	components: {
		DropdownOptions
	},
	props: {
		element: Object as PropType<Element>
	},
	setup (props, { attrs, slots, emit }) {
		const expanded = ref(false)
		const elementName = computed({
			get () {
				return props.element.name
			},
			set (newValue) {
				props.element.rename(newValue)
			}
		})
		return {
			expanded,
			elementName
		}
	}
})
</script>
<style lang="scss">
.znpb-tree-view__item {
	.zion-visibility-hidden {
		cursor: pointer;
	}
	.znpb-element-options__dropdown-icon {
		cursor: pointer;

		&:hover {
			color: darken($font-color, 15%);
		}
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

		&.znpb-panel-item--hovered {
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
.znpb-tree-view__item .znpb-element-options__dropdown-icon {
	padding-left: 5px;
}
</style>
