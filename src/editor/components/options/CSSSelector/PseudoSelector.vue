<template>
	<Tooltip
		tooltip-class="hg-popper--no-padding"
		trigger="click"
		placement="bottom"
		append-to="body"
		strategy="fixed"
		:close-on-outside-click="true"
		class="znpb-option-cssChildSelectorPseudoSelector"
		@hide="showAddModal = false"
	>
		<template #content>
			<div class="znpb-option-cssChildSelectorPseudoSelectorListWrapper">
				<ul class="znpb-option-cssChildSelectorPseudoSelectorList">
					<li class="znpb-option-cssChildSelectorPseudoSelectorListTitle">Active states:</li>
					<li v-for="state in activePseudo" :key="state.id" @click="removeState(state)">{{ state.name }}</li>
				</ul>

				<ul class="znpb-option-cssChildSelectorPseudoSelectorList">
					<li class="znpb-option-cssChildSelectorPseudoSelectorListTitle">Available states:</li>
					<li v-for="state in remainingPseudo" :key="state.id" @click="addState(state)">{{ state.name }}</li>
				</ul>
			</div>
		</template>

		<template v-if="states.length === 1">
			{{ states[0] }}
		</template>
		<template v-if="states.length > 1"> {{ states.length }} states </template>
	</Tooltip>
</template>

<script lang="ts" setup>
import { computed } from 'vue';

const { usePseudoSelectors } = window.zb.composables;

const props = withDefaults(defineProps<{ states: string[] }>(), {
	states: () => [],
});

const emit = defineEmits(['update:states']);

const { pseudoSelectors } = usePseudoSelectors();
const allPseudoSelectors = computed(() => {
	const disabledStates = ['custom', ':before', ':after'];
	return pseudoSelectors.value.filter(state => !disabledStates.includes(state.id));
});

const computedStates = computed({
	get() {
		return props.states || [];
	},
	set(newStates) {
		emit('update:states', newStates);
	},
});

const activePseudo = computed(() => {
	return computedStates.value.map(state => allPseudoSelectors.value.find(stateConfig => stateConfig.id === state));
});

const remainingPseudo = computed(() => {
	return allPseudoSelectors.value.filter(state => !computedStates.value.includes(state.id));
});

function removeState(state) {
	const value = computedStates.value.slice();
	// Don't allow removing all states
	if (value.length === 1) {
		return;
	}

	if (value.includes(state.id)) {
		const index = value.indexOf(state.id);
		value.splice(index, 1);

		computedStates.value = value;
	}
}

function addState(state) {
	const value = computedStates.value.slice();
	value.push(state.id);

	computedStates.value = value;
}
</script>

<style lang="scss">
.znpb-option-cssChildSelectorPseudoSelectorList {
	padding: 0;
	margin: 0 0 5px 0;

	& > li {
		color: #858585;
		font-weight: 500;
		padding: 8px 16px;
		cursor: pointer;

		&:hover {
			background-color: #f6f6f6;
		}
	}

	& > .znpb-option-cssChildSelectorPseudoSelectorListTitle {
		color: #bcbcbc;
		font-size: 11px;
		font-weight: 700;
		line-height: 1;
		text-transform: uppercase;
		cursor: text;
		margin: 10px 0 0;

		&:hover {
			background-color: transparent;
		}
	}
}
</style>
