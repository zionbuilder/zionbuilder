<template>
	<div class="znpb-tab__wrapper--columns-template-elements">
		<div class="znpb-add-elements__filter">
			<InputSelect
				class="znpb-add-elements__filter-category"
				:options="elementCategories"
				:placeholder="elementCategories[0].name"
				v-model="categoryValue"
				:placement="isRtl ? 'bottom-end' : 'bottom-start'"
			/>

			<BaseInput
				class="znpb-columns-templates__search-wrapper znpb-add-elements__filter-search"
				v-model="computedSearchKeyword"
				:placeholder="$translate('search_elements')"
				:clearable="true"
				icon="search"
				autocomplete="off"
				ref="searchInputEl"
			/>
		</div>

		<div
			class="znpb-fancy-scrollbar znpb-wrapper-category"
			ref="categoriesWrapper"
		>
			<template v-if="computedRuleCategories.length">
				<ElementList
					v-for="(category,i) in computedRuleCategories"
					:key="i"
					:elements="getElements(category.id)"
					:element="element"
					:category="category.name"
					@add-element="onAddElement"
				/>
			</template>

			<div
				style="text-align:center;"
				v-if="!elementsAreFound"
			>{{$translate('no_elements_found')}}</div>
		</div>
	</div>
</template>
<script>
import { ref, computed, onMounted, watch, nextTick } from 'vue'

import { useElementTypes, useElementTypeCategories, useAddElementsPopup, useHistory, useEditorData } from '@composables'

// Components
import ElementList from './ElementList.vue'

export default {
	name: 'ElementsTab',
	components: {
		ElementList
	},
	props: {
		element: {
			type: Object
		},
		searchKeyword: String
	},
	setup (props) {
		const { getVisibleElements } = useElementTypes()
		const { categories } = useElementTypeCategories()
		const { editorData } = useEditorData()
		// Refs
		const foundElements = ref([])
		const categoriesWrapper = ref(false)

		const localSearchKeyword = ref(null)
		const computedSearchKeyword = computed(
			{
				get: () => {
					return localSearchKeyword.value !== null ? localSearchKeyword.value : props.searchKeyword
				},
				set: (newValue) => {
					localSearchKeyword.value = newValue
					foundElements.value = []
				}
			}
		)
		const categoryValue = ref('all')
		const searchInputEl = ref(null)

		const sortedCategories = computed(() => {
			return categories.value.sort((a, b) => {
				return a.priority < b.priority ? -1 : 1
			})
		})

		// Normal data
		const elementCategories = [{
			id: 'all',
			name: 'All'
		}].concat(sortedCategories.value)

		// Computed
		const computedRuleCategories = computed(() => {
			let categoriesArray = []
			foundElements.value = []
			let category = categoryValue.value

			if (category !== 'all') {
				categoriesArray = categories.value.filter(cat => cat.id === category)

			} else {
				categoriesArray = sortedCategories.value
			}

			return categoriesArray

		})


		// Methods
		const onAddElement = (element) => {
			const { hideAddElementsPopup, insertElement } = useAddElementsPopup()

			const config = {
				element_type: element.element_type,
				...element.extra_data
			}

			insertElement(config)

			const { getElementType } = useElementTypes()
			const { addToHistory } = useHistory()
			const elementType = getElementType(config.element_type)
			addToHistory(`Added ${elementType.name}`)

			hideAddElementsPopup()
		}

		function getElements (category) {
			let elements = getVisibleElements.value

			const keyword = computedSearchKeyword.value

			// Check if we have a specific category selected
			elements = elements.filter((element) => {
				return element.category.includes(category)
			})

			// Check if we have a keyword
			if (keyword.length > 0) {
				elements = elements.filter((element) => {
					return (
						element.name.toLowerCase().indexOf(keyword.toLowerCase()) !== -1 ||
						element.keywords
							.join()
							.toLowerCase()
							.indexOf(keyword.toLowerCase()) !== -1
					)
				})
			}
			foundElements.value.push(elements.length)

			return elements
		}

		watch(foundElements, () => {
			if (categoriesWrapper.value) {
				nextTick(() => {
					categoriesWrapper.value.scrollTop = 0
				})
			}
		})

		// Lifecycle
		onMounted(() => {
			setTimeout(() => {
				searchInputEl.value.focus()
			}, 0)
		})

		const elementsAreFound = computed(() => {
			let foundIndex = foundElements.value.findIndex(element => element !== 0)
			return foundIndex !== -1
		})

		return {
			// Normal values
			elementCategories,
			getElements,
			foundElements,
			elementsAreFound,
			// Refs
			computedSearchKeyword,
			categoryValue,
			searchInputEl,
			categoriesWrapper,
			// Computed
			computedRuleCategories,
			// Methods
			onAddElement,
			// rtl
			isRtl: editorData.value.rtl
		}
	}
}
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

	.zion-input input, .zion-input input::placeholder {
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

		input[type="text"][readonly] {
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
