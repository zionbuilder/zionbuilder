<template>
	<div class="znpb-tab__wrapper--columns-template-elements">
		<div class="znpb-add-elements__filter">
			<InputSelect
				v-model="categoryValue"
				class="znpb-add-elements__filter-category"
				:options="dropdownOptions"
				:placeholder="dropdownOptions[0].name"
				:placement="isRtl ? 'bottom-end' : 'bottom-start'"
			/>

			<BaseInput
				ref="searchInputEl"
				v-model="computedSearchKeyword"
				class="znpb-columns-templates__search-wrapper znpb-add-elements__filter-search"
				:placeholder="$translate('search_elements')"
				:clearable="true"
				icon="search"
				autocomplete="off"
			/>
		</div>

		<div ref="categoriesWrapper" class="znpb-fancy-scrollbar znpb-wrapper-category">
			<template v-if="categoriesWithElements.length">
				<ElementList
					v-for="(category, i) in categoriesWithElements"
					:key="i"
					:ref="
						el => {
							if (el) categoriesRefs[category.id] = el;
						}
					"
					:elements="category.elements"
					:element="element"
					:category="category.name"
					@add-element="onAddElement"
				/>
			</template>

			<div v-if="!activeElements.length" style="text-align: center">{{ $translate('no_elements_found') }}</div>
		</div>
	</div>
</template>
<script>
import { ref, computed, onMounted, watch, nextTick } from 'vue';
import { useHistory, useEditorData, useUserData } from '../../composables';
import { translate } from '/@/common/modules/i18n';
import { useElementDefinitionsStore, useUIStore, useContentStore } from '/@/editor/store';

// Components
import ElementList from './ElementList.vue';

export default {
	name: 'ElementsTab',
	components: {
		ElementList,
	},
	props: {
		element: {
			type: Object,
			required: true,
		},
		searchKeyword: {
			required: false,
			type: String,
			default: '',
		},
	},
	setup(props) {
		const contentStore = useContentStore();
		const UIStore = useUIStore();
		const elementsDefinitionsStore = useElementDefinitionsStore();
		const { editorData } = useEditorData();
		const { getUserData } = useUserData();

		// Refs
		const categoriesWrapper = ref(false);
		const categoriesRefs = ref([]);

		const localSearchKeyword = ref(null);
		const computedSearchKeyword = computed({
			get: () => {
				return localSearchKeyword.value !== null ? localSearchKeyword.value : props.searchKeyword;
			},
			set: newValue => {
				localSearchKeyword.value = newValue;
			},
		});
		const categoryValue = ref('all');
		const searchInputEl = ref(null);

		// Normal data
		const elementCategories = computed(() => {
			const categoriesToReturn = [
				{
					id: 'all',
					name: translate('all'),
				},
			];

			if (getUserData('favorite_elements', []).length > 0) {
				categoriesToReturn.push({
					id: 'favorites',
					name: translate('favorites'),
					priority: 1,
				});
			}

			// Add the categories from server and sort them
			const clonedCategories = [...elementsDefinitionsStore.categories];
			const sortedCategories = clonedCategories.sort((a, b) => {
				return a.priority < b.priority ? -1 : 1;
			});

			return categoriesToReturn.concat(sortedCategories);
		});

		const activeElements = computed(() => {
			let elements = elementsDefinitionsStore.getVisibleElements;
			const keyword = computedSearchKeyword.value;

			if (keyword.length > 0) {
				elements = elements.filter(element => {
					return (
						element.name.toLowerCase().indexOf(keyword.toLowerCase()) !== -1 ||
						element.keywords.join().toLowerCase().indexOf(keyword.toLowerCase()) !== -1
					);
				});
			}

			return elements;
		});

		const dropdownOptions = computed(() => {
			const keyword = computedSearchKeyword.value;

			if (keyword.length === 0) {
				return elementCategories.value;
			} else {
				return elementCategories.value.filter(category => {
					return (
						category.id === 'all' ||
						activeElements.value.filter(element => element.category.includes(category.id)).length > 0
					);
				});
			}
		});

		const categoriesWithElements = computed(() => {
			const clonedCategories = [...elementCategories.value];

			// remove the all category
			clonedCategories.shift();

			return clonedCategories.map(category => {
				// Get elements for current category
				const elements = activeElements.value.filter(element => {
					const elementCategories = Array.isArray(element.category) ? element.category : [element.category];
					if (category.id === 'favorites') {
						return getUserData('favorite_elements', []).indexOf(element.element_type) >= 0;
					} else {
						return elementCategories.includes(category.id);
					}
				});

				return {
					name: category.name,
					id: category.id,
					elements,
				};
			});
		});

		// Methods
		const onAddElement = element => {
			const config = {
				element_type: element.element_type,
				version: element.version,
				...element.extra_data,
			};

			// Insert element
			contentStore.addElement(config, UIStore.activeAddElementPopup.element, UIStore.activeAddElementPopup.index);

			const { addToHistory } = useHistory();
			const elementType = elementsDefinitionsStore.getElementDefinition(config);
			addToHistory(`Added ${elementType.name}`);

			UIStore.hideAddElementsPopup();
		};

		watch(activeElements, () => {
			nextTick(() => {
				categoriesWrapper.value.scrollTop = 0;
				categoryValue.value = 'all';
			});
		});

		// Scroll to the proper category on click
		watch(categoryValue, newValue => {
			if (newValue === 'all') {
				categoriesWrapper.value.scrollTop = 0;
			} else {
				if (typeof categoriesRefs.value[newValue] !== 'undefined') {
					if (categoriesRefs.value[newValue].$el) {
						categoriesRefs.value[newValue].$el.scrollIntoView({
							behavior: 'smooth',
							inline: 'start',
							block: 'nearest',
						});
					}
				}
			}
		});

		// Lifecycle
		onMounted(() => {
			setTimeout(() => {
				searchInputEl.value.focus();
			}, 0);
		});

		return {
			// Normal values
			elementCategories,
			dropdownOptions,
			// Refs
			categoriesRefs,
			computedSearchKeyword,
			categoryValue,
			searchInputEl,
			categoriesWrapper,
			// Computed
			categoriesWithElements,
			// Methods
			onAddElement,
			// rtl
			isRtl: editorData.value.rtl,
			activeElements,
		};
	},
};
</script>

<style lang="scss">
.znpb-columns-templates-wrapper {
	.znpb-tab__wrapper {
		&--columns-template-elements {
			display: flex;
			flex-direction: column;
			min-height: 385px;

			.znpb-wrapper-category {
				align-items: center;
				max-height: 321px;
				padding-top: 20px;
				& > div {
					width: 100%;
				}
			}
		}

		.znpb-wrapper-category {
			flex-grow: 1;
			padding: 0 6px 0 10px;
		}

		.hg-popper-list {
			padding: 0;
		}
	}
	.zion-input__prepend {
		padding: 0;
		background: transparent;
		border-right: 2px solid var(--zb-surface-icon-color);

		.znpb-baseselect__trigger > .zion-input {
			border: none;
		}
	}

	.zion-input input,
	.zion-input input::placeholder {
		color: var(--zb-input-placeholder-color);
	}

	.znpb-element-category-list {
		.znpb-element-box {
			padding: 0;
		}
	}
}

.znpb-add-elements__filter {
	display: flex;
	padding: 0 10px;
	margin-bottom: 15px;

	&-category {
		flex-grow: 1;
		flex-shrink: 0;
		width: 130px;
		margin-right: 10px;

		input[type='text'][readonly] {
			background: var(--zb-input-bg-color);
		}
	}
}

.znpb-add-elements__search-results-wrapper {
	flex-grow: 1;
	height: 100%;
	max-height: 100%;
}
.znpb-columns-templates {
	display: grid;
	color: var(--zb-surface-lighter-color);

	grid-column-gap: 20px;
	grid-row-gap: 20px;
	grid-template-columns: 1fr 1fr 1fr;

	&__search-wrapper.zion-input {
		// width: calc(100% - 20px);
		// padding: 0 10px;
		margin-bottom: 5px;
		// margin-left: 10px;
		background: var(--zb-input-bg-color);
	}
}
</style>
