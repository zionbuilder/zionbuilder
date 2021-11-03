<template>
	<Tooltip
		tooltipClass="hg-popper--no-padding"
		trigger="null"
		placement="right"
		v-model:show="expanded"
		:close-on-outside-click="true"
		:close-on-escape="true"
		class="znpb-hiddenMenuWrapper"
	>

		<template v-slot:content>
			<Menu
				:actions="actions"
				@action="expanded =! expanded"
			/>
		</template>

		<Icon
			icon="more"
			:bgSize="14"
			@click.stop="expanded =! expanded"
		/>
	</Tooltip>
</template>

<script>
import { ref } from 'vue'

// Components
import Menu from './Menu.vue'

export default {
	name: "HiddenMenu",
	components: {
		Menu
	},
	props: {
		actions: {
			type: Array,
			required: true
		}
	},
	setup (props, { emit }) {
		const expanded = ref(false)

		return {
			expanded
		}
	}
}
</script>