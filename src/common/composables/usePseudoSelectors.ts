import { ref } from 'vue';

interface PseudoSelector {
	name: string;
	id: string;
}

const pseudoSelectors = ref<PseudoSelector[]>([
	{
		name: 'default',
		id: 'default',
	},
	{
		name: ':hover',
		id: ':hover',
	},
	{
		name: ':before',
		id: ':before',
	},
	{
		name: ':after',
		id: ':after',
	},
	{
		name: ':active',
		id: ':active',
	},
	{
		name: ':focus',
		id: ':focus',
	},
	{
		name: ':custom',
		id: 'custom',
	},
]);

const activePseudoSelector = ref(pseudoSelectors.value[0]);

export const usePseudoSelectors = () => {
	function setActivePseudoSelector(value: PseudoSelector) {
		activePseudoSelector.value = value || pseudoSelectors.value[0];
	}

	function deleteCustomSelector(selector: PseudoSelector) {
		const selectorIndex = pseudoSelectors.value.indexOf(selector);
		if (selectorIndex !== -1) {
			pseudoSelectors.value.splice(selectorIndex, 1);

			activePseudoSelector.value = pseudoSelectors.value[0];
		}
	}

	function addCustomSelector(selector: PseudoSelector) {
		pseudoSelectors.value.push(selector);
	}

	return {
		activePseudoSelector,
		pseudoSelectors,
		// Methods
		addCustomSelector,
		setActivePseudoSelector,
		deleteCustomSelector,
	};
};
