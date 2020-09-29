<template>
	<li
		class="znpb-wireframe-item"
		:class="getClasses"
		@contextmenu.stop.prevent="showContextMenu"
		@mouseenter.capture="highlightElement"
		@mouseleave="unHighlightElement"
		@click.stop="onItemClick"
	>
		<div class="znpb-wireframe-item__header znpb-utility__flex">
			<div class="znpb-wireframe-item__header-area znpb-utility__flex znpb-wireframe-item__header-area--left">
				<BaseIcon
					class="znpb-wireframe-item__header-item znpb-wireframe-item__header-button znpb-wireframe-item__header-more znpb-utility__cursor--pointer"
					v-if="elementTemplateData.content && elementTemplateData.content.length"
					icon="select"
					:rotate="expanded ? '180' : false"
					@click.native="expanded = !expanded"
				/>
			</div>
			<div class="znpb-wireframe-item__header-area znpb-utility__flex znpb-wireframe-item__header-area--center znpb-utility__flex--center">
				<ElementRename
					class="znpb-wireframe-item__header-title znpb-wireframe-item__header-item"
					@name-changed="doRenameElement"
					@dblclick="isNameChangeActive=true"
					:is-active="isNameChangeActive"
					:content="elementName"
				/>
			</div>
			<div class="znpb-wireframe-item__header-area znpb-wireframe-item__header-area--right znpb-utility__flex znpb-utility__flex--end">

				<Tooltip
					v-if="!isElementVisible"
					:content="$translate('enable_hidden_element')"
					class="znpb-wireframe-item__header-area--visibility-icon"
				>
					<span>
						<transition name="fade">
							<BaseIcon
								icon="visibility-hidden"
								@click.native="makeElementVisible"
								class="znpb-editor-icon-wrapper--show-element"
							>
							</BaseIcon>
						</transition>
					</span>
				</Tooltip>

				<DropdownOptions
					:element-uid="elementUid"
					:parentUid="parentUid"
					:isActive="isActiveItem"
					@changename="isNameChangeActive=true"
				></DropdownOptions>
				<BaseIcon
					icon="delete"
					@click.native.stop="deleteElementMenu"
					class="znpb-wireframe-item__delete-icon"
				/>

			</div>
		</div>
		<EmptySortablePlaceholder
			slot="empty-placeholder"
			v-if="!elementTemplateData.content.length && elementModel.wrapper"
			:parentUid="elementUid"
			:data="elementTemplateData"
		/>
		<Sortable
			class="znpb-wireframe-item__content"
			v-model="templateDataModel"
			v-if="expanded && elementModel.wrapper"
			tag="ul"
			group="pagebuilder-wireframe-elements"
			@start="sortableStart"
			@end="sortableEnd"
			:class="{[`-flex--${hasFlexDirection}`]: hasFlexDirection}"
		>
			<SortableHelper slot="helper"></SortableHelper>
			<SortablePlaceholder slot="placeholder"></SortablePlaceholder>
			<ElementWireframeView
				v-for="(childElementUid, i) in templateDataModel"
				v-bind:key="i"
				:parentUid="elementUid"
				:elementUid="childElementUid"
			/>
			<Tooltip
				tooltip-class="hg-popper--big-arrows"
				placement='auto'
				:show="showColumnTemplates"
				append-to="body"
				trigger="click"
				:close-on-outside-click="true"
				:close-on-escape="true"
				:modifiers="{ offset: { offset: '0,10px' } }"
				@hide="onAddColumnsHide"
				@show="onAddColumnsShow"
				key="addElements"
			>
				<div class="znpb-element-toolbox__add-element-button">
					<BaseIcon
						icon="plus"
						:rounded="true"
					/>
				</div>

				<ColumnTemplates
					slot="content"
					@close-popper="showColumnTemplates=false"
					:parentUid="parentUid"
					:data="elementTemplateData"
					:empty-sortable="false"
				/>
			</Tooltip>
		</Sortable>
	</li>
</template>
<script>
import { mapActions, mapGetters } from 'vuex'
import templateElementMixin from '../../../mixins/templateElement.js'
import DropdownOptions from '@/editor/components/DropdownOptions.vue'
import TreeViewMixin from '../elementMixins.js'
import { Sortable, Tooltip } from '@zb/components'
import SortablePlaceholder from '../../../common/SortablePlaceholder.vue'
import SortableHelper from '../../../common/SortableHelper.vue'
import ColumnTemplates from '@/editor/common/ColumnTemplates.vue'
import EmptySortablePlaceholder from '@/editor/common/EmptySortablePlaceholder'
import eventMarshall from '@/editor/common/eventMarshall'
import { getOptionValue } from '@zb/utils'
import { on } from '@zb/event-bus'

export default {
	name: 'element-wireframe-view',
	mixins: [
		templateElementMixin,
		TreeViewMixin
	],

	components: {
		DropdownOptions,
		Sortable,
		SortablePlaceholder,
		SortableHelper,
		Tooltip,
		ColumnTemplates,
		EmptySortablePlaceholder
	},
	data () {
		return {
			expanded: true,
			isNameChangeActive: false,
			hovered: false,
			showColumnTemplates: false
		}
	},
	created () {
		on('rename-element', this.activateRenameElement)
	},
	computed: {
		...mapGetters([
			'getElementFocus',
			'getElementData'
		]),
		columnSize () {
			return this.elementTemplateData.options.column_size
		},
		templateDataModel: {
			get () {
				return this.elementTemplateData.content !== undefined ? this.elementTemplateData.content : ''
			},
			set (value) {
				this.saveElementsOrder({
					newOrder: value,
					content: this.elementTemplateData.content
				})
			}
		},
		isActiveItem () {
			return this.getElementFocus && this.getElementFocus.uid === this.elementUid
		},

		hasFlexDirection () {
			let orientation = 'column'
			let mediaOrientation = getOptionValue(this.elementTemplateData.options, '_styles.wrapper.styles.default.default.flex-direction')

			if (this.elementTemplateData['element_type'] === 'zion_section') {
				mediaOrientation = getOptionValue(this.elementTemplateData.options, '_styles.inner_content_styles.styles.default.default.flex-direction', 'row')
			}

			if (mediaOrientation) {
				orientation = mediaOrientation
			}

			return orientation
		},
		hasName () {
			return this.elementTypeName !== undefined ? this.elementTypeName.toLowerCase() : null
		},
		getClasses () {
			let cssClass = {
				[`znpb-wireframe-item--item--hidden`]: !this.isElementVisible,
				[`znpb-wireframe-item--${this.hasName}`]: this.hasName,
				[`znpb-wireframe-item__empty`]: !this.elementTemplateData.content.length && this.elementModel.wrapper
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
		...mapActions(
			[
				'saveElementsOrder',
				'setDraggingState',
				'setRightClickMenu',
				'setElementFocus',
				'deleteElement'
			]
		),
		activateRenameElement () {
			if (this.isActiveItem) {
				this.isNameChangeActive = true
			}
		},
		deleteElementMenu () {
			this.deleteElement({
				elementUid: this.elementTemplateData.uid,
				parentUid: this.parentUid
			})
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
		onAddColumnsShow () {
			this.$emit('update:canHideToolbox', false)
			this.showColumnTemplates = true

			if (eventMarshall.getActiveTooltip) {
				eventMarshall.getActiveTooltip.showColumnTemplates = false
			}

			eventMarshall.addActiveTooltip(this)
		},
		onAddColumnsHide () {
			this.$emit('update:canHideToolbox', true)
			this.showColumnTemplates = false
			this.resetAddElementsPopup()
		},
		resetAddElementsPopup () {
			if (eventMarshall.getActiveTooltip && eventMarshall.getActiveTooltip === this) {
				eventMarshall.reset()
			}
		},
		onItemClick () {
			this.setElementFocus({
				uid: this.elementUid,
				parentUid: this.parentUid,
				insertParent: this.elementModel.wrapper ? this.elementTemplateData.uid : this.parentUid,
				scrollIntoView: true
			})
		},
		showContextMenu (e) {
			this.setRightClickMenu({
				visibility: true,
				previewIframeLeft: 0,
				initialScrollTop: document.getElementById('znpb-wireframe-panel').scrollTop,
				position: {
					top: e.clientY + window.pageYOffset,
					left: e.clientX
				},
				source: 'editor'
			})
			this.setElementFocus({
				uid: this.elementUid,
				parentUid: this.parentUid,
				insertParent: this.elementModel.wrapper ? this.elementTemplateData.uid : this.parentUid,
				scrollIntoView: true
			})
		},
		sortableStart () {
			this.setDraggingState(true)
		},
		sortableEnd () {
			this.setDraggingState(false)
		},
		shrinkPanel () {
			this.expanded = false
		}
	}
}
</script>
<style lang="scss">
@import "@/scss/frontend/_grid.scss";

.znpb-editor-icon-wrapper--show-element {
	padding: 10px 5px 10px;
	margin-right: 5px;
	transition: opacity .2s ease;
	cursor: pointer;

	&:hover {
		opacity: 1;
	}
}

.znpb-wireframe-view-wrapper {
	padding-top: 5px;
	padding-bottom: 50px;

	& > li {
		box-shadow: 0 10px 35px 0 rgba(192, 192, 192, .3);
	}
}
.znpb-wireframe-item {
	flex-grow: 1;
	flex-shrink: 1;
	margin: 0 0 30px;
	background: $primary-color--accent;
	.znpb-wireframe-item--column .znpb-empty-placeholder {
		border-right: 2px solid #faeec6;
		border-left: 2px solid #faeec6;
	}

	.znpb-empty-placeholder {
		height: auto;
		min-height: auto;
		padding-top: 15px;
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

	& > .znpb-wireframe-item__header {
		background: $secondary;
	}

	&__delete-icon {
		padding: 13px 20px 13px 0;
		transition: opacity .2s;
		cursor: pointer;

		span {
			transition: none;
		}

		&:hover, &:focus {
			opacity: .5;
		}
	}

	.znpb-element-toolbox__add-element-button {
		position: absolute;
		top: 100%;
		left: 50%;
		margin: 0 auto;
		text-align: center;
		transform: translate(-50%, -50%);

		.znpb-editor-icon-wrapper {
			width: 34px;
			height: 34px;
			color: $surface;
			background: $elements-toolbox-color;
		}
	}

	&__header {
		width: 100%;
		color: $primary-color--accent;
		text-align: center;
		transition: all .2s;

		&-area {
			flex-basis: 0;
			flex-grow: 1;
			&--visibility-icon {
				display: flex;
				align-items: center;
			}
			&--center {
				.znpb-utility__text--elipse {
					&:after {
						display: none;
					}
				}
				& > span {
					color: $surface-variant;
				}
			}
			&--right {
				position: relative;
				min-width: 110px;
			}
		}

		&-item {
			position: relative;
			padding: 13px 20px;
			transition: opacity .2s;
			cursor: text;
			&:hover {
				opacity: .5;
			}
			&:focus {
				outline: 0;
			}
		}

		&-title {
			width: auto;
			color: $surface;
			font-weight: 500;
			.znpb-utility__text--elipse {
				width: 100%;
				max-width: 170px;
			}
		}
	}

	&--section {
		position: relative;
		display: flex;
		flex-wrap: wrap;
		justify-content: center;
		align-items: center;
		flex: 1 1 auto;
		width: auto;

		& > .znpb-wireframe-item__header {
			background: $section-color;
		}

		& > .znpb-wireframe-item__content {
			position: relative;
			display: flex;
			flex-wrap: wrap;
			flex: 1 1 auto;
			width: 100%;
		}
	}

	&--column {
		position: relative;
		display: flex;
		flex-direction: column;
		flex-wrap: wrap;
		flex-grow: 1;
		min-height: 1px;
		padding-right: 15px;
		padding-left: 15px;

		& > .znpb-wireframe-item__header {
			background: $column-color;
		}
		& > .znpb-wireframe-item__content {
			position: relative;
			display: flex;
			flex-direction: column;
			flex-wrap: wrap;
			flex: 1 1 auto;
			width: 100%;
			border: 2px solid #faeec6;
		}
		& > .znpb-wireframe-item__content > .znpb-element-toolbox__add-element-button {
			& > .znpb-editor-icon-wrapper {
				background: $column-color;
			}
		}
	}

	&--item--hidden {
		opacity: .5;
	}
	&__content {
		position: relative;
		width: 100%;
		&.-flex {
			&--row {
				flex-direction: row;
			}
			&--column {
				flex-direction: column;
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
}
//nested children
ul.znpb-wireframe-item__content {
	padding: 30px 15px;
	cursor: pointer;
}
</style>
