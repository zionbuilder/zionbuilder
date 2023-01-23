<template>
	<div class="znpb-tab__wrapper--columns-template-elements">
		<div class="znpb-add-elements__filter">
			<InputSelect
				v-model="categoryValue"
				class="znpb-add-elements__filter-category"
				:options="dropdownOptions"
				:placeholder="dropdownOptions[0].name"
			/>

			<BaseInput
				ref="searchInputEl"
				v-model="computedSearchKeyword"
				class="znpb-columns-templates__search-wrapper znpb-add-elements__filter-search"
				:placeholder="i18n.__('Search elements', 'zionbuilder')"
				:clearable="true"
				icon="search"
				autocomplete="off"
			/>
		</div>

		<div ref="categoriesWrapper" class="znpb-fancy-scrollbar znpb-wrapper-category">
			<template v-if="categoriesWithElements.length">
				<ElementList
					v-for="category in categoriesWithElements"
					:key="category.id"
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

			<div v-if="!activeElements.length" style="text-align: center">
				{{ i18n.__('No elements found matching the search criteria', 'zionbuilder') }}
			</div>
		</div>
	</div>
</template>

<script lang="ts" setup>
import * as i18n from '@wordpress/i18n';
import { ref, computed, onMounted, watch, nextTick, onBeforeUnmount } from 'vue';
import { useUserData } from '/@/editor/composables';
import { useElementDefinitionsStore, useUIStore } from '/@/editor/store';

// Components
import ElementList from './ElementList.vue';

const props = withDefaults(
	defineProps<{
		element: ZionElement;
		searchKeyword: string;
	}>(),
	{
		searchKeyword: '',
	},
);

const emit = defineEmits(['update:search-keyword']);

const UIStore = useUIStore();
const elementsDefinitionsStore = useElementDefinitionsStore();
const { getUserData } = useUserData();

// Refs
const categoriesWrapper = ref(false);
const categoriesRefs = ref([]);

const computedSearchKeyword = computed({
	get: () => {
		return props.searchKeyword;
	},
	set: newValue => {
		emit('update:search-keyword', newValue);
	},
});
const categoryValue = ref('all');
const searchInputEl = ref(null);

// Normal data
const elementCategories = computed(() => {
	const categoriesToReturn = [
		{
			id: 'all',
			name: i18n.__('All', 'zionbuilder'),
		},
	];

	if (getUserData('favorite_elements', []).length > 0) {
		categoriesToReturn.push({
			id: 'favorites',
			name: i18n.__('Favorites', 'zionbuilder'),
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
	window.zb.run('editor/elements/add', {
		element: config,
		parentUID: UIStore.activeAddElementPopup.element.uid,
		index: UIStore.activeAddElementPopup.index,
	});

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

onBeforeUnmount(() => {
	computedSearchKeyword.value = '';
});
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
