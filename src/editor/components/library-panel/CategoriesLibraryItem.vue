<template>
	<li
		class="znpb-editor-library-modal-category__item"
		:class="{ 'znpb-editor-library-modal-category__item--active': category.isActive }"
	>
		<div class="znpb-editor-library-modal-category__header" @click="onCategoryActivate(category)">
			<h6 class="znpb-editor-library-modal-category__title" v-html="category.name" />

			<span v-if="showCount" class="znpb-editor-library-modal-category__number">
				{{ category.count }}
			</span>

			<Icon
				v-if="hasSubcategories"
				icon="select"
				:rotate="activeDropdown ? '180' : '0'"
				class="znpb-editor-library-modal-category__header-icon"
				@click.stop="activeDropdown = !activeDropdown"
			/>
		</div>

		<CategoriesLibrary
			v-if="hasSubcategories && activeDropdown"
			:categories="category.subcategories"
			:on-category-activate="onCategoryActivate"
		/>
	</li>
</template>

<script lang="ts" setup>
import { computed, ref, watch } from 'vue';
import CategoriesLibrary from './CategoriesLibrary.vue';

const props = defineProps<{
	category: Record<string, unknown>;
	isActive?: boolean;
	showCount?: boolean;
	onCategoryActivate: Function;
}>();

const hasSubcategories = computed(() => {
	return props.category.subcategories && props.category.subcategories.length > 0;
});

const activeDropdown = ref(props.isActive);

watch(
	() => props.isActive,
	newValue => {
		activeDropdown.value = newValue;
	},
);
</script>
<style lang="scss">
.znpb-editor-library-modal-category__item--active
	> .znpb-editor-library-modal-category__header
	> .znpb-editor-library-modal-category__title {
	position: relative;
	width: 100%;
	color: var(--zb-surface-text-active-color);
}
</style>
