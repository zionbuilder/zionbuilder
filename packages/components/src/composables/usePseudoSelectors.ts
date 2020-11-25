import { ref } from 'vue'

const pseudoSelectors = ref([
	{
		name: 'default',
		id: 'default'
	},
	{
		name: ':hover',
		id: ':hover'
	},
	{
		name: ':before',
		id: ':before'
	},
	{
		name: ':after',
		id: ':after'
	},
	{
		name: ':active',
		id: ':active'
	},
	{
		name: ':focus',
		id: ':focus'
	},
	{
		name: ':custom',
		id: 'custom'
	}
])
const activePseudoSelector = ref(pseudoSelectors.value[0])

export const usePseudoSelectors = () => {

	function setActivePseudoSelector (value) {
		activePseudoSelector.value = value
	}

	function deleteCustomSelector (selector) {
		const selectorIndex = pseudoSelectors.value.indexOf(selector)
		pseudoSelectors.value.splice(selectorIndex, 1)

		console.log(pseudoSelectors.value[0]);
		activePseudoSelector.value = pseudoSelectors.value[0]
	}

	function addCustomSelector (selector) {
		pseudoSelectors.value.push(selector)
	}

	return {
		activePseudoSelector,
		pseudoSelectors,
		// Methods
		addCustomSelector,
		setActivePseudoSelector,
		deleteCustomSelector
	}
}