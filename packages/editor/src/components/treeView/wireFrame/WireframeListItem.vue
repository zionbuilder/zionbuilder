<template>
	<li
		class="znpb-wireframe-item"
		:class="getClasses"
		@click.stop="element.focus"
		@contextmenu.stop.prevent="showElementMenu"
	>
		<div class="znpb-wireframe-item__header">
			<div class="znpb-wireframe-item__header-area znpb-wireframe-item__header-area--left">

				<Icon
					class="znpb-wireframe-item__header-item znpb-wireframe-item__header-button znpb-wireframe-item__header-more znpb-utility__cursor--pointer"
					v-if="element.isWrapper"
					icon="select"
					:rotate="expanded ? '180' : false"
					@click="expanded = !expanded"
				/>

				<UIElementIcon
					:element="elementModel"
					class="znpb-tree-view__itemIcon"
					:size="24"
				/>

				<InlineEdit
					v-model="elementName"
					class="znpb-wireframe-item__header-title znpb-wireframe-item__header-item"
				/>

			</div>
			<div class="znpb-wireframe-item__header-area znpb-wireframe-item__header-area--right">

				<Icon
					icon="visibility-hidden"
					class="znpb-editor-icon-wrapper--show-element znpb-tree-view__item-enable-visible znpb-wireframe-item__header-area--visibility-icon"
					@click.stop="element.toggleVisibility()"
					v-if="!element.isVisible"
					v-znpb-tooltip="$translate('enable_hidden_element')"
				/>

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
		</div>

		<WireframeList
			v-if="expanded && element.isWrapper"
			:element="element"
			class="znpb-wireframe-item__content"
			:class="{[`znpb-flex--${hasFlexDirection}`]: hasFlexDirection}"
		/>

	</li>
</template>
<script>
import { computed } from 'vue'
import SortablePlaceholder from '../../../common/SortablePlaceholder.vue'
import SortableHelper from '../../../common/SortableHelper.vue'
import { getOptionValue } from '@zb/utils'
import { useTreeViewItem } from '../useTreeViewItem'
import { useElementTypes } from "@composables";


export default {
	name: 'element-wireframe-view',
	components: {
		SortablePlaceholder,
		SortableHelper
	},
	props: {
		element: {
			type: Object,
			required: true
		}
	},
	setup (props) {
		const {
			showElementMenu,
			elementOptionsRef,
			isActiveItem
		} = useTreeViewItem(props)
		const columnSize = computed(() => props.element.options.column_size)

		const { getElementType } = useElementTypes();

		const elementModel = getElementType(props.element.element_type);

		const elementName = computed({
			get () {
				return props.element.name;
			},
			set (newValue) {
				props.element.name = newValue;
			},
		});

		return {
			showElementMenu,
			elementModel,
			elementOptionsRef,
			isActiveItem,
			columnSize,
			elementName
		}
	},
	data () {
		return {
			expanded: true,
			isNameChangeActive: false,
			hovered: false,
			showColumnTemplates: false
		}
	},
	computed: {
		hasFlexDirection () {
			let orientation = 'column'
			let mediaOrientation = getOptionValue(this.element.options, '_styles.wrapper.styles.default.default.flex-direction')

			if (this.element.element_type === 'zion_section') {
				mediaOrientation = getOptionValue(this.element.options, '_styles.inner_content_styles.styles.default.default.flex-direction', 'row')
			}

			if (mediaOrientation) {
				orientation = mediaOrientation
			}

			return orientation
		},
		getClasses () {
			let cssClass = {
				[`znpb-wireframe-item--item--hidden`]: !this.element.isVisible,
				[`znpb-wireframe-item--${this.element.element_type}`]: this.element.element_type,
				[`znpb-wireframe-item__empty`]: !this.element.content.length
			}

			if (this.columnSize) {
				Object.keys(this.columnSize).forEach((key) => {
					let responsivePrefix = this.getColumnResponsivePrefix(key)
					cssClass[`zb-column--${responsivePrefix}${this.columnSize[key]}`] = !!this.columnSize[key]
				})
			}
			return cssClass
		}

	},
	methods: {
		activateRenameElement () {
			if (this.isActiveItem) {
				this.isNameChangeActive = true
			}
		},
		getColumnResponsivePrefix (responsiveMediaId) {
			const devices = {
				default: '',
				laptop: 'lg--',
				tablet: 'md--',
				mobile: 'sm--'
			}

			return devices[responsiveMediaId]
		},
		shrinkPanel () {
			this.expanded = false
		}
	}
}
</script>
<style lang="scss">
@import "~@zionbuilder/css-variables/frontend/_grid.scss";

.znpb-editor-icon-wrapper--show-element {
	padding: 15px 15px 15px;
	transition: opacity 0.2s ease;
	cursor: pointer;

	&:hover {
		opacity: 1;
	}
}

.znpb-wireframe-view-wrapper {
	padding-top: 5px;
	padding-bottom: 50px;
}
.znpb-wireframe-item {
	flex-grow: 1;
	flex-shrink: 1;
	padding: 0 8px 16px 8px;

	&Image {
		height: 24px;
	}
	&Image,
	&Icon {
		padding-right: 15px;
	}

	.znpb-wireframe-item--column .znpb-empty-placeholder {
		border-right: 2px solid #faeec6;
		border-left: 2px solid #faeec6;
	}

	.znpb-empty-placeholder {
		height: auto;
		min-height: 24px;
	}

	.znpb-element-toolbox__add-element-button {
		--button-size: 24px;
		--font-size: 12px;
	}

	&__empty {
		display: block;
		.znpb-wireframe-item__content {
			padding: 15px;
		}
	}
	&__sortable {
		cursor: pointer;
	}

	&__delete-icon {
		padding: 13px 20px 13px 0;
		transition: opacity 0.2s;
		cursor: pointer;

		span {
			transition: none;
		}

		&:hover,
		&:focus {
			opacity: 0.5;
		}
	}

	.znpb-element-toolbox__add-element-button {
		margin: 0 auto;
		text-align: center;
		transform: translate(-50%, -50%);
	}

	&__header {
		display: flex;
		width: 100%;
		color: var(--zb-primary-text-color);
		text-align: center;
		background-color: var(--zb-surface-lightest-color);
		border-radius: 6px;
		padding: 0 15px;
		z-index: 1;
		transition: all 0.2s;

		&-area {
			display: flex;
			flex-basis: 0;
			flex-grow: 1;

			&--visibility-icon {
				display: flex;
				align-items: center;

				.znpb-editor-icon-wrapper--show-element {
					padding-right: 0;
				}
			}
			&--left {
				align-items: center;
				overflow: hidden;

				.znpb-utility__text--elipse {
					&:after {
						display: none;
					}
				}
				& > span {
					color: var(--zb-surface-icon-color);
				}
			}
			&--right {
				position: relative;
				justify-content: flex-end;
				flex-grow: 0;
			}
		}

		&-item {
			position: relative;
			padding: 13px 20px;
			transition: opacity 0.2s;

			&:focus {
				outline: 0;
			}
		}

		&-title {
			overflow: hidden;
			padding: 0;
			color: var(--zb-surface-text-hover-color);
			font-weight: 500;
			text-overflow: ellipsis;
			white-space: nowrap;
			cursor: text;

			.znpb-utility__text--elipse {
				width: 100%;
				max-width: 170px;
			}
		}

		&-more {
			padding: 14px 15px 14px 8px;
			margin-left: -15px;

			&:hover {
				color: var(--zb-surface-icon-active-color);
			}
		}

		.znpb-tree-view__itemIcon {
			padding: 0;
			margin-right: 8px;
		}

		.znpb-element-options__dropdown-icon {
			color: var(--zb-surface-icon-color);
			padding: 14px 15px;
			margin-right: -15px;

			&:hover {
				color: var(--zb-surface-icon-active-color);
			}
		}
	}

	&--zion_section {
		position: relative;
		display: flex;
		flex-wrap: wrap;
		justify-content: center;
		align-items: center;
		flex: 1 1 auto;
		width: auto;

		& > .znpb-wireframe-item__content {
			position: relative;
			display: flex;
			flex-wrap: wrap;
			align-items: flex-start;
			flex: 1 1 auto;
			width: 100%;
		}
	}

	&--zion_column {
		position: relative;
		display: flex;
		flex-direction: column;
		flex-wrap: wrap;
		flex-grow: 1;
		min-height: 1px;

		& > .znpb-wireframe-item__content {
			position: relative;
			display: flex;
			flex-direction: column;
			flex-wrap: wrap;
			flex: 1 1 auto;
			width: 100%;
			border: 1px solid var(--zb-surface-lightest-color);
			border-bottom-left-radius: 6px;
			border-bottom-right-radius: 6px;
		}
		&
			> .znpb-wireframe-item__content
			> .znpb-element-toolbox__add-element-button {
			& > .znpb-editor-icon-wrapper {
				background: var(--zb-column-color);
			}
		}
	}

	&--item--hidden {
		opacity: 0.5;
	}
	&__content {
		position: relative;
		width: 100%;
		&.znpb-flex {
			&--row {
				flex-direction: row;
			}
			&--column {
				flex-direction: column;
				justify-content: center;
				align-items: stretch;
			}
		}
	}

	// Children of content should have dashed border instead of shadow
	&__content &__content {
		box-shadow: none;
		border-top: none;
	}

	// Children of content should have dashed border instead of shadow
	&__content &__content {
		box-shadow: none;
		border-top: none;
	}

	//nested children
	ul.znpb-wireframe-item__content {
		padding: 21px 8px 16px 8px;
		background: var(--zb-surface-light-color);
		cursor: pointer;
		border-bottom-left-radius: 6px;
		border-bottom-right-radius: 6px;
		margin-top: -5px;
	}
}
</style>
