<template>
	<div class="znpb-option-cssChildSelectorPseudoSelector">
		<Tooltip
			trigger="click"
			placement="bottom"
			append-to="body"
			strategy="fixed"
			@hide="showAddModal = false"
			:close-on-outside-click="true"
		>
			<template #content>
				<div class="znpb-option-cssChildSelectorPseudoSelectorListWrapper">
					<ul class="znpb-option-cssChildSelectorPseudoSelectorList">
						<li class="znpb-option-cssChildSelectorPseudoSelectorListTitle">Active states:</li>
						<li
							v-for="state in activePseudo"
							:key="state.id"
							@click="removeState(state)"
						>{{state.name}}</li>
					</ul>

					<ul class="znpb-option-cssChildSelectorPseudoSelectorList">
						<li class="znpb-option-cssChildSelectorPseudoSelectorListTitle">Available states:</li>
						<li
							v-for="state in remainingPseudo"
							:key="state.id"
							@click="addState(state)"
						>{{state.name}}</li>
					</ul>
				</div>
			</template>

			<template v-if="states.length === 1">
				{{states[0]}}
			</template>
			<template v-if="states.length > 1">
				{{states.length}} states
			</template>
		</Tooltip>

	</div>
</template>

<script>
import { computed } from 'vue'
import { usePseudoSelectors } from '@zb/components'

export default {
	name: 'PseudoSelector',
	props: {
		states: {
			type: Array,
			default: []
		}
	},
	setup (props, { emit }) {
		const { pseudoSelectors } = usePseudoSelectors()
		const allPseudoSelectors = computed(() => {
			const disabledStates = ['custom', ':before', ':after']
			return pseudoSelectors.value.filter(state => !disabledStates.includes(state.id))
		})

		const computedStates = computed({
			get () {
				return props.states || []
			},
			set (newStates) {
				console.log({ newStates });
				emit('update:states', newStates)
			}
		})

		const activePseudo = computed(() => {
			return computedStates.value.map(state => allPseudoSelectors.value.find(stateConfig => stateConfig.id === state))
		})

		const remainingPseudo = computed(() => {
			return allPseudoSelectors.value.filter(state => !computedStates.value.includes(state.id))
		})

		function removeState (state) {
			const value = computedStates.value.slice()
			// Don't allow removing all states
			if (value.length === 1) {
				return
			}

			if (value.includes(state.id)) {
				const index = value.indexOf(state.id)
				value.splice(index, 1)

				computedStates.value = value
			}
		}


		function addState (state) {
			const value = computedStates.value.slice()
			value.push(state.id)

			computedStates.value = value
		}

		return {
			computedStates,
			activePseudo,
			remainingPseudo,
			addState,
			removeState
		}
	}
}
</script>

<style lang="scss">
.znpb-option-cssChildSelectorPseudoSelectorList {
	padding: 0;
	margin: 0 0 15px 0;

	& > li {
		padding: 0;
		margin: 0 0 8px 0;
		cursor: pointer;
	}

	& > .znpb-option-cssChildSelectorPseudoSelectorListTitle {
		color: #c3c3c3;
		font-size: 10px;
		font-weight: 700;
		text-transform: uppercase;
		cursor: text;
	}
}
</style>