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

	return {
		activePseudoSelector,
		pseudoSelectors
	}
}