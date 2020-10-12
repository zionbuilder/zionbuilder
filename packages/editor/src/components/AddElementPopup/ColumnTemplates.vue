<template>
	<div class="znpb-columns-templates-wrapper">
		<Tabs
			title-position="center"
			:active-tab="activeTab"
		>
			<Tab
				name="Columns"
				class="znpb-tab__wrapper--columns-template"
			>
				<div class="znpb-columns-templates">

					<div
						v-for="(columnConfig, columnId) in layouts"
						:key="columnId"
						:icon="columnId"
						@click.stop="addElements(columnConfig)"
						:class="['znpb-columns-templates__icon znpb-columns-templates__icons--' + columnId]"
					>
						<span
							v-for="(span,i) in getSpanNumber(columnId)"
							:key="i"
						></span>
					</div>
				</div>
			</Tab>
			<Tab name="Elements">
				<!-- <ElementsList
					@close-popper="$emit('close-popper', true)"
					:parentUid="this.element.uid"
					:data="data"
					:insertIndex="getInsertIndex()"
				/> -->
			</Tab>
			<Tab name="Library">
				<!-- <Icon
					icon="library-illustration"
					class="znpb-columns-templates__library-img"
				/>
				<h3 class="znpb-columns-templates__library-title">{{$translate('access_library_templates')}}</h3>
				<p class="znpb-columns-templates__libray-text">{{$translate('access_video_library')}}</p>
				<div class="znpb-columns-templates__library-buttonWrap">
					<Button
						type="secondary"
						@click.stop="openLibrary"
					>
						{{$translate('open_library')}}
					</Button>
				</div> -->
			</Tab>

		</Tabs>
	</div>
</template>
<script>
import { mapActions, mapGetters } from 'vuex'
import ElementsList from './ElementsList.vue'
import { getOptionValue, generateElements } from '@zb/utils'
import { on, off, trigger } from '@zb/hooks'
import { getLayoutConfigs } from './layouts.js'

export default {
	name: 'ColumnTemplates',
	props: {
		element: {
			required: true
		}
	},
	components: {
		ElementsList
	},

	data () {
		return {
			active: null,
			spanElements: {
				'full': 1,
				'one-of-two': 2,
				'one-of-three': 3,
				'one-of-four': 4,
				'one-of-five': 5,
				'one-of-six': 6,
				'4-8': 2,
				'8-4': 2,
				'3-9': 2,
				'9-3': 2,
				'3-6-3': 3,
				'3-3-6': 3,
				'6-3-3': 3,
				'6-3-3-3-3': 5,
				'3-3-3-3-6': 5,
				'3-3-6-3-3': 5
			},
			layouts: getLayoutConfigs()
		}
	},
	computed: {
		activeTab () {
			if (this.active !== null) {
				return this.active
			} else if (this.element.element_type === 'zion_column') {
				return 'elements'
			} else return null
		}
	},
	methods: {
		getSpanNumber (id) {
			return this.spanElements[id]
		},
		wrapColumn (config) {
			return [
				{
					element_type: 'zion_column',
					content: config,
					options: {
						_styles: {
							wrapper: {
								styles: {
									default: {
										default: {
											'flex-direction': 'row'
										}
									}
								}
							}
						}
					}

				}
			]
		},
		addElements (config) {
			// If it's a wrapper, it means that it can have childs
			if (this.element.isWrapper || this.element.element_type === 'contentRoot') {
				this.element.addChildrens(config)
			} else {
				this.element.parent.addChildrens(config)
			}

			// Send close event
			this.$emit('added-element', true)
			// const elementType = this.element.element_type

			// // Add a section if this will be inserted on root
			// if (elementType === 'root') {
			// 	config = [
			// 		{
			// 			element_type: 'zion_section',
			// 			content: config
			// 		}
			// 	]
			// } else {
			// 	if (this.emptySortable) {
			// 		if (elementType === 'zion_column') {
			// 			// Check content orientation
			// 			if (getOptionValue(this.elementInfo.options, '_styles.wrapper.styles.default.default.flex-direction', 'column') === 'column') {
			// 				config = this.wrapColumn(config)
			// 			}
			// 		} else if (elementType === 'zion_section') {
			// 			if (getOptionValue(this.elementInfo.options, '_styles.inner_content_styles.styles.default.default.flex-direction', 'row') === 'column') {
			// 				config = this.wrapColumn(config)
			// 			}
			// 		}
			// 	} else {
			// 		// check parent orientation
			// 		if (elementType === 'zion_column') {
			// 			if (getOptionValue(this.getElementData(this.parentUid).options, '_styles.wrapper.styles.default.default.flex-direction', 'column') === 'column') {
			// 				config = this.wrapColumn(config)
			// 			}
			// 		} else if (elementType === 'zion_section') {
			// 			if (getOptionValue(this.getElementData(this.parentUid).options, '_styles.inner_content_styles.styles.default.default.flex-direction', 'row') === 'column') {
			// 				config = this.wrapColumn(config)
			// 			}
			// 		}
			// 	}
			// }

			// const elements = generateElements(config)

			// // Get index
			// // this.setShouldOpenAddElementsPopup(true)
			// // deactivate setPopup because on multiple columns multiple popups appear
			// this.insertElements({
			// 	parentUid: this.parentUid,
			// 	index: this.getInsertIndex(),
			// 	childElements: elements.childElements,
			// 	parentElements: elements.parentElements
			// })
		},
		openLibrary () {
			this.$emit('close-popper', true)

			this.setElementConfigForLibrary({
				parentUid: this.parentUid,
				index: this.getInsertIndex()
			})

			this.$zb.panels.togglePanel('PanelLibraryModal')
		}
	},
	beforeUnmount: function () {
		// remove events
		document.removeEventListener('keypress', this.onKeyPress)
		off('change-tab-pop', this.changeTab)
	}
}
</script>

<style lang="scss">
.znpb-columns-templates-wrapper {
	display: flex;
	flex-direction: column;
	max-width: 360px;
	max-height: 500px;

	.znpb-tabs--card {
		.znpb-tabs__header {
			padding: 3px;
			margin: 10px 10px 20px;
			background: $surface-variant;
			border-radius: 3px;

			.znpb-tabs__header-item {
				flex: 1;
				padding: 15px 20px;
				color: $font-color;
				font-size: 13px;
				font-weight: 500;
				border-radius: 2px;
				cursor: pointer;

				&:hover {
					color: $font-color;
					background-color: darken($surface-variant, 3%);
				}

				&--active {
					color: $surface;
					background: $secondary;

					&:hover {
						color: $surface;
						background: $secondary;
					}
				}
			}
		}
	}

	.znpb-tab__wrapper {
		overflow: hidden;

		&--columns-template {
			overflow: hidden;
			padding: 0 10px 10px;
		}
	}

	.znpb-button--secondary {
		margin-bottom: 30px;
	}
}
.znpb-columns-templates {
	display: grid;
	color: $surface-variant;

	grid-column-gap: 20px;
	grid-row-gap: 20px;
	grid-template-columns: 1fr 1fr 1fr;

	.znpb-editor-icon-wrapper {
		font-size: 100px;
		cursor: pointer;

		&:hover {
			color: $secondary;
		}
		&:last-child {
			grid-column-start: 2;
		}
		&:first-child {
			border-top-left-radius: 3px;
		}

		&:last-child {
			border-top-right-radius: 3px;
		}

		.zion-icon {
			height: auto;
		}
	}

	&__icon {
		display: grid;
		box-sizing: content-box;
		width: 100px;
		height: 50px;
		cursor: pointer;

		grid-auto-flow: dense;
		grid-gap: 3px;

		span {
			display: block;
			background: darken($surface-variant, 3%);
			border-radius: 1px;
			transition: box-shadow .15s;
		}

		&:hover span {
			background: darken($surface-variant, 10%);
		}
	}

	&__icons {
		&--one-of-two {
			grid-template-columns: repeat(2, 1fr);
		}
		&--one-of-three {
			grid-template-columns: repeat(3, 1fr);
		}
		&--one-of-four {
			grid-template-columns: repeat(4, 1fr);
		}
		&--one-of-five {
			grid-template-columns: repeat(5, 1fr);
		}
		&--one-of-six {
			grid-template-columns: repeat(6, 1fr);
		}
		&--4-8 {
			grid-template-columns: 1fr 2fr;
		}
		&--8-4 {
			grid-template-columns: 2fr 1fr;
		}
		&--3-9 {
			grid-template-columns: 1fr 3fr;
		}
		&--9-3 {
			grid-template-columns: 3fr 1fr;
		}
		&--3-6-3 {
			grid-template-columns: 1fr 2fr 1fr;
		}
		&--3-3-6 {
			grid-template-columns: 1fr 1fr 2fr;
		}
		&--6-3-3 {
			grid-template-columns: 2fr 1fr 1fr;
		}
		&--6-3-3-3-3 {
			grid-template-columns: 2fr 1fr 1fr;

			span:first-child {
				grid-row: 1 / span 2;
			}
		}
		&--3-3-3-3-6 {
			grid-template-columns: 1fr 1fr 2fr;

			span:nth-child(2) {
				grid-column-start: 3;
				grid-row: 1 / span 2;
			}
		}
		&--3-3-6-3-3 {
			grid-template-columns: 1fr 2fr 1fr;

			span:nth-child(2) {
				grid-column-start: 2;
				grid-row: 1 / span 2;
			}
		}
	}

	&__library-title {
		text-align: center;
	}

	&__libray-text {
		display: block;
		width: 70%;
		padding-bottom: 20px;
		margin: 0 auto;
		margin: 0 auto !important;
		font-size: 14px;
		line-height: 26px;
		text-align: center;
	}

	&__library-img {
		&.znpb-editor-icon-wrapper {
			width: 100%;
			margin-bottom: 10px;
			color: $secondary;
			font-size: 182px;
		}
	}

	&__library-buttonWrap {
		text-align: center;
	}
}
</style>
