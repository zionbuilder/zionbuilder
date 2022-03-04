import { ref, Ref, computed, readonly } from 'vue'

interface ResponsiveDevice {
	name: string;
	id: string;
	width?: {
		value: number,
		unit: string
	},
	height?: {
		value: number,
		unit: string
	},
	icon: string
}

const activeResponsiveDeviceId = ref('default')
const responsiveDevices: Ref<Array<ResponsiveDevice>> = ref([
	{
		name: 'Desktop',
		id: 'default',
		icon: 'desktop'
	},
	{
		name: 'Laptop',
		id: 'laptop',
		width: 991,
		height: 768,
		units: 'px',
		icon: 'laptop'
	},
	{
		name: 'Tablet',
		id: 'tablet',
		width: 767,
		height: 1024,
		icon: 'tablet'
	},
	{
		name: 'Mobile',
		id: 'mobile',
		width: 575,
		height: 667,
		icon: 'mobile'
	}
])

const activeResponsiveOptions: Ref<{} | null> = ref(null)
const iframeWidth: Ref<number> = ref(0)
const autoscaleActive = ref(false)
const scaleValue = ref(100)

export const useResponsiveDevices = () => {
	const activeResponsiveDeviceInfo = computed(() => responsiveDevices.value.find(device => device.id === activeResponsiveDeviceId.value) || responsiveDevices.value[0])


	function setActiveResponsiveDeviceId(device: string) {
		activeResponsiveDeviceId.value = device

		if (activeResponsiveDeviceInfo.value.width) {
			setCustomIframeWidth(activeResponsiveDeviceInfo.value.width)
		}
	}

	function setCustomScale(newValue) {
		scaleValue.value = newValue
	}

	function setActiveResponsiveOptions(instanceConfig: {}) {
		activeResponsiveOptions.value = instanceConfig
	}

	function getActiveResponsiveOptions() {
		return activeResponsiveOptions.value
	}

	function removeActiveResponsiveOptions() {
		activeResponsiveOptions.value = null
	}

	function setCustomIframeWidth(newWidth: number) {
		iframeWidth.value = newWidth
	}

	function setAutoScale(scaleEnabled) {
		autoscaleActive.value = scaleEnabled
	}

	return {
		activeResponsiveDeviceId,
		activeResponsiveDeviceInfo,
		responsiveDevices,
		iframeWidth,
		autoscaleActive,
		scaleValue: readonly(scaleValue),
		setActiveResponsiveDeviceId,
		removeActiveResponsiveOptions,
		getActiveResponsiveOptions,
		setActiveResponsiveOptions,
		setCustomIframeWidth,
		setCustomScale,
		setAutoScale
	}
}