<template>
	<div
		class="znpb-element-options__vertical-breadcrumbs-item"
		:class="{ 'znpb-element-options__vertical-breadcrumbs-item--active': item.active }"
		@mouseenter.capture="item.element.highlight"
		@mouseleave="item.element.unHighlight"
		@click.stop="UIStore.editElement(item.element)"
	>
		<span>{{ contentStore.getElementName(item.element) }}</span>

		<template v-if="item.children.length > 0">
			<Breadcrumbs
				v-for="child in item.children"
				:key="child.element.uid"
				class="znpb-element-options__vertical-breadcrumbs-wrapper--inner"
				:item="child"
			/>
		</template>
	</div>
</template>

<script lang="ts" setup>
import Breadcrumbs from './Breadcrumbs.vue';
import { useUIStore, useContentStore } from '@/editor/store';

const UIStore = useUIStore();
const contentStore = useContentStore();
defineProps<{
	item: Record<string, unknown>;
}>();
</script>
