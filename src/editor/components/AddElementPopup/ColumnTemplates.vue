<template>
	<div class="znpb-columns-templates-wrapper">
		<Tabs v-model:activeTab="active" title-position="center" @changed-tab="onTabChange">
			<Tab name="Layouts" class="znpb-tab__wrapper--columns-template">
				<div class="znpb-columns-templates">
					<div
						v-for="(columnConfig, columnId) in layouts"
						:key="columnId"
						:icon="columnId"
						:class="['znpb-columns-templates__icon znpb-columns-templates__icons--' + columnId]"
						@click.stop="addElements(columnConfig)"
					>
						<span v-for="(span, i) in getSpanNumber(columnId)" :key="i"></span>
					</div>
				</div>
			</Tab>
			<Tab name="Elements">
				<ElementsTab :element="element" :search-keyword="searchKeyword" />
			</Tab>
			<Tab name="Library">
				<span />
			</Tab>
		</Tabs>
	</div>
</template>
<script>
import { ref, onMounted, onUnmounted } from 'vue';
import { get } from 'lodash-es';
import { getLayoutConfigs } from './layouts.js';
import { useAddElementsPopup, useWindows, useHistory } from '../../composables';
import { useLibrary } from '/@/common/composables';
import { useUIStore, useElementDefinitionsStore } from '../../store';

// Components
import ElementsTab from './ElementsTab.vue';

export default {
	name: 'ColumnTemplates',
	components: {
		ElementsTab,
	},
	props: {
		element: {
			type: Object,
			required: true,
		},
	},
	setup(props, { emit }) {
		const UIStore = useUIStore();
		const defaultTab = props.element.element_type === 'zion_column' ? 'elements' : 'layouts';
		const active = ref(defaultTab);
		const { addEventListener, removeEventListener } = useWindows();
		const searchKeyword = ref('');

		const spanElements = {
			full: 1,
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
			'3-3-6-3-3': 5,
		};

		const layouts = getLayoutConfigs();

		const getSpanNumber = id => {
			return spanElements[id];
		};

		const wrapColumn = config => {
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
											'flex-direction': 'row',
										},
									},
								},
							},
						},
					},
				},
			];
		};

		function getOrientation(element) {
			const elementsDefinitionsStore = useElementDefinitionsStore();
			let orientation = 'horizontal';

			if (element.element_type === 'contentRoot') {
				return 'vertical';
			}

			const elementType = elementsDefinitionsStore.getElementDefinition(element);

			if (elementType) {
				orientation = elementType.content_orientation;
			}

			// Check columns and section direction
			if (element.options.inner_content_layout) {
				orientation = element.options.inner_content_layout;
			}

			// Check media settings
			const mediaOrientation = get(element.options, '_styles.wrapper.styles.default.default.flex-direction');

			if (mediaOrientation) {
				orientation = mediaOrientation === 'row' ? 'horizontal' : 'vertical';
			}

			return orientation;
		}

		const addElements = config => {
			const { insertElement, getElementForInsert, shouldOpenPopup } = useAddElementsPopup();
			const activeElementForInsert = getElementForInsert();
			const elementType = activeElementForInsert.element.element_type;
			const parentOrientation = getOrientation(activeElementForInsert.element);

			// Add a section if this will be inserted on root
			if (elementType === 'contentRoot') {
				config = [
					{
						element_type: 'zion_section',
						content: config,
					},
				];
			} else {
				if (parentOrientation === 'vertical') {
					config = wrapColumn(config);
				}
			}

			// Open the first sortable content popup
			shouldOpenPopup.value = true;

			// If it's a wrapper, it means that it can have childs
			insertElement(config);

			const { addToHistory } = useHistory();
			addToHistory(`Added Columns Layout`);

			// Send close event
			emit('close');
		};

		const openLibrary = () => {
			const { activePopup } = useAddElementsPopup();
			const { setActiveElementForLibrary } = useLibrary();

			setActiveElementForLibrary(activePopup.value.element, activePopup.value.config);

			UIStore.toggleLibrary();
			emit('close');
		};

		function onKeyDown(event) {
			const currentTab = active.value;
			active.value = 'elements';

			if (currentTab !== 'elements') {
				searchKeyword.value += event.key;
			}
		}

		function onTabChange(tab) {
			searchKeyword.value = '';

			if (tab === 'library') {
				openLibrary();
			}
		}

		onMounted(() => addEventListener('keypress', onKeyDown));
		onUnmounted(() => removeEventListener('keypress', onKeyDown));

		return {
			layouts,
			active,
			spanElements,
			// methods
			getSpanNumber,
			addElements,
			searchKeyword,
			openLibrary,
			onTabChange,
		};
	},
};
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
			background: var(--zb-surface-lighter-color);
			border-radius: 3px;

			.znpb-tabs__header-item {
				flex: 1;
				padding: 15px 20px;
				color: var(--zb-surface-text-color);
				font-size: 13px;
				font-weight: 500;
				border-radius: 2px;
				cursor: pointer;

				&:hover {
					color: var(--zb-surface-text-active-color);
					background-color: var(--zb-surface-lightest-color);
				}

				&--active {
					color: var(--zb-secondary-text-color);
					background: var(--zb-secondary-color);

					&:hover {
						color: var(--zb-secondary-text-color);
						background: var(--zb-secondary-color);
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
	color: var(--zb-surface-lighter-color);

	grid-column-gap: 15px;
	grid-row-gap: 15px;
	grid-template-columns: 1fr 1fr 1fr;

	.znpb-editor-icon-wrapper {
		font-size: 100px;
		cursor: pointer;

		&:hover {
			color: var(--zb-secondary-color);
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
		grid-gap: 4px;

		span {
			display: block;
			background: var(--zb-surface-lightest-color);
			border-radius: 3px;
			transition: box-shadow 0.15s;
		}

		&:hover span {
			background: var(--zb-surface-lighter-color);
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
		margin-top: 0;
		margin-bottom: 20px;
		font-family: var(--zb-font-stack);
		font-size: 28px;
		font-weight: 700;
		line-height: 1;
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
			color: var(--zb-secondary-color);
			font-size: 182px;
		}
	}

	&__library-buttonWrap {
		text-align: center;
	}
}
</style>
