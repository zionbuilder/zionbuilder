<template>
	<li
		class="znpb-section-view-item"
		:class="{'znpb-section-view-item--hidden': !isElementVisible}"
		@contextmenu.stop.prevent="showContextMenu"
		@mouseenter.capture="highlightElement"
		@mouseleave="unHighlightElement"
		@click.stop="onItemClick"
	>
		<div v-if="loading || error">
			<Loader :size="16" />
			<span v-if="error">{{ $translate('preview_not_available')}}</span>
		</div>
		<img :src="imageSrc">
		<div
			class="znpb-section-view-item__header"
			:class="{'znpb-panel-item--hovered': hovered, 'znpb-panel-item--active': isActiveItem}"
		>
			<div class="znpb-section-view-item__header-left">
				<ElementRename
					class="znpb-section-view-item__header-title"
					@name-changed="doRenameElement"
					:is-active="isNameChangeActive"
					:content="elementName"
				/>
			</div>

			<Tooltip
				v-if="!isElementVisible"
				:content="$translate('enable_hidden_element')"
			>
				<span>
					<transition name="fade">
						<Icon
							icon="visibility-hidden"
							@click="makeElementVisible"
							class="znpb-editor-icon-wrapper--show-element"
						>
						</Icon>
					</transition>
				</span>
			</Tooltip>

			<DropdownOptions
				class="znpb-section-view-item__header-right"
				:element-uid="elementUid"
				:parentUid="parentUid"
				:isActive="isActiveItem"
				@changename="isNameChangeActive=true"
			></DropdownOptions>
		</div>
	</li>
</template>
<script>
import { mapActions, mapGetters } from 'vuex'
import domtoimage from 'dom-to-image'
import templateElementMixin from '../../../mixins/templateElement.js'
import TreeViewMixin from '../elementMixins.js'
import { on } from '@zb/hooks'

export default {
	name: 'element-section-view',
	mixins: [
		templateElementMixin,
		TreeViewMixin
	],
	data: function () {
		return {
			showIconMore: false,
			imageSrc: null,
			loading: true,
			error: false,
			isNameChangeActive: false,
			hovered: false
		}
	},
	created () {
		on('rename-element', this.activateRenameElement)
	},
	mounted () {
		const domElement = window.frames['znpb-editor-iframe'].contentDocument.getElementById(this.elementCssId)
		const filter = function (node) {
			if (node && node.classList) {
				if (node.classList.contains('znpb-empty-placeholder')) {
					return false
				}
			}
			return true
		}

		domtoimage.toSvg(domElement, {
			style: {
				'width': '100%',
				'margin': 0
			},
			filter: filter
		})
			.then((dataUrl) => {
				this.imageSrc = dataUrl
			})
			.catch((error) => {
				this.error = true
				// eslint-disable-next-line
				console.error(this.$translate('oops_something_wrong'), error)
			})
			.finally(() => {
				this.loading = false
			})
	},
	computed: {
		...mapGetters([
			'getElementFocus'
		]),
		options () {
			return this.elementTemplateData.options || {}
		},
		isActiveItem () {
			return this.getElementFocus && this.getElementFocus.uid === this.elementUid
		},
		elementCssId () {
			return (this.options._advanced_options || {})._element_id || this.elementTemplateData.uid
		}
	},
	methods: {
		...mapActions([
			'setElementFocus',
			'setRightClickMenu'
		]),
		activateRenameElement () {
			if (this.isActiveItem) {
				this.isNameChangeActive = true
			}
		},
		onItemClick () {
			this.setElementFocus({
				uid: this.elementUid,
				parentUid: this.parentUid,
				insertParent: this.elementModel.wrapper ? this.elementUid : this.parentUid,
				scrollIntoView: true
			})
		},
		showContextMenu (e) {
			this.setRightClickMenu({
				visibility: true,
				previewIframeLeft: 0,
				initialScrollTop: document.getElementById('znpb-section-view').scrollTop,
				position: {
					top: e.clientY + window.pageYOffset,
					left: e.clientX
				},
				source: 'editor'
			})

			this.setElementFocus({
				uid: this.elementUid,
				parentUid: this.parentUid,
				insertParent: this.elementModel.wrapper ? this.elementUid : this.parentUid,
				scrollIntoView: false
			})
		}
	}
}
</script>

<style lang="scss" >
.znpb-section-view-item {
	position: relative;
	display: flex;
	flex-direction: column;
	justify-content: space-between;
	align-items: center;
	padding: 0;
	margin: 0 auto;
	margin-bottom: 20px;
	background-color: $surface-variant;
	border-bottom-right-radius: 3px;
	border-bottom-left-radius: 3px;
	cursor: move;

	&__header-left {
		position: relative;
		overflow: hidden;
	}
	&--hidden {
		.znpb-section-view-item__header-left {
			transition: opacity .5s ease;
			opacity: .5;
		}
	}

	.znpb-loader {
		margin: 15px 0;
	}

	&__image-wrapper {
		display: flex;
		justify-content: space-between;
		width: 100%;
		color: $font-color;
		line-height: 18px;
		border: 1px solid #f1f1f1;
		cursor: pointer;
	}

	&__header {
		position: relative;
		display: flex;
		justify-content: space-between;
		align-content: center;
		align-items: center;
		width: 100%;
		margin: 0;
		font-size: 13px;
		font-weight: 500;
		background-color: $surface-variant;
		border-bottom-right-radius: 3px;
		border-bottom-left-radius: 3px;

		&:hover {
			cursor: move;
		}

		&.znpb-panel-item--hovered {
			background-color: darken($surface-variant, 3%);
		}

		&.znpb-panel-item--active {
			color: $surface;
			background-color: $secondary;
		}

		&-left {
			flex-grow: 1;
		}

		&-title {
			padding: 15px;
			color: $surface-active-color;
			font-weight: 500;
			cursor: pointer;
		}

		&.znpb-panel-item--active &-title {
			color: $surface;
		}
	}
}
.znpb-admin-small-loader.znpb-admin-small-loader {
	position: absolute;
	top: 50%;
	left: 50%;
	z-index: 999999;
	z-index: 9;
	margin: 0;
	transform: translate(-50%, -50%);
}
</style>
