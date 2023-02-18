<template>
	<AddSelector>
		<template #default="{ actions }">
			<div class="znpb-option-cssSelectorChildActions">
				<template v-if="childSelectors.length === 0">
					<!-- No children yet -->
					<Icon v-znpb-tooltip="'Add new inner selector'" icon="child-add" @click.stop="actions.toggleModal" />
				</template>
				<template v-else>
					<!-- ICON -->
					<span
						class="znpb-option-cssSelectorChildActionsChildNumber"
						@click.stop="(showChilds = !showChilds), emit('toggle-view-children')"
					>
						<Icon icon="child" />
						{{ childSelectors.length }}
					</span>

					<Icon v-znpb-tooltip="'Add new inner selector'" icon="plus" @click.stop="actions.toggleModal" />
				</template>
			</div>
		</template>
	</AddSelector>
</template>

<script lang="ts" setup>
import AddSelector from '../common/AddSelector.vue';

// Props
withDefaults(defineProps<{ childSelectors: any[] }>(), {
	childSelectors: () => [],
});

// Emits
const emit = defineEmits(['toggle-view-children']);
</script>
