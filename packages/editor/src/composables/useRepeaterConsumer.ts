import { inject, computed, provide } from 'vue'
import { get } from 'lodash-es'

export function useRepeaterConsumer(element) {
	const mainElement = element
	let currentIndex = 0
	const providedRepeaterData = inject('repeaterDataValues', [])

	const repeaterItems = computed(() => {
		// get actual data
		const repeaterConsumerConfig = element.getRepeaterConsumerConfig()
		const repeaterStartIndexValue = get(repeaterConsumerConfig, 'start', 0) || 0
		const dataLength = providedRepeaterData.value.length
		const repeaterEndIndexValue = get(repeaterConsumerConfig, 'end', dataLength) || dataLength

		// Check if the repeater end index is higher than
		const repeaterEndValueOrMax = repeaterEndIndexValue > dataLength ? dataLength : repeaterEndIndexValue
		return providedRepeaterData.value.slice(repeaterStartIndexValue, repeaterEndValueOrMax)
	})

	const repeaterElements = computed(() => {
		return repeaterItems.value.map((repeaterItemData, index) => {
			const repeaterItem = index === 0 ? element : element.getClone()

			if (index !== 0) {
				repeaterItem.setUid(`${element.uid}_${index}`)
			}

			return repeaterItem
		})
	})

	const repeaterItemsUids = computed(() => {
		return repeaterItems.value.map(item => item.uid)
	})

	function iterate() {
		currentIndex++;
	}

	function provideRepeaterItem() {
		const currentItem = repeaterItems.value[currentIndex]
		provide('repeaterItemData', currentItem)
	}

	return {
		providedRepeaterData,
		repeaterItems,
		mainElement,
		repeaterItemsUids,
		repeaterElements,
		iterate,
		provideRepeaterItem
	}
}