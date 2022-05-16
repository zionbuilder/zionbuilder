<template>
	<AddSelector>
		<template v-slot="{actions}">
			<div class="znpb-option-cssSelectorChildActions">
				<template v-if="childSelectors.length === 0">
					<!-- No childs yet -->
					<Icon
						icon="child-add"
						v-znpb-tooltip="'Add new inner selector'"
						@click.stop="actions.toggleModal"
					/>

				</template>
				<template v-else>
					<!-- ICON -->
					<span
						@click.stop="showChilds = !showChilds, $emit('toggle-view-childs')"
						class="znpb-option-cssSelectorChildActionsChildNumber"
					>
						<Icon icon="child" />
						{{childSelectors.length}}
					</span>

					<Icon
						icon="plus"
						v-znpb-tooltip="'Add new inner selector'"
						@click.stop="actions.toggleModal"
					/>

				</template>
			</div>
		</template>

	</AddSelector>
</template>

<script>
import { ref, computed } from 'vue'

import AddSelector from '../common/AddSelector.vue'

export default {
	name: 'AddChildActions',
	components: {
		AddSelector
	},
	props: {
		childSelectors: {
			type: Array,
			default: []
		}
	},
	setup (props, { emit }) {
		const showAddModal = ref(false)

		return {
			showAddModal
		}
	}
}
</script>