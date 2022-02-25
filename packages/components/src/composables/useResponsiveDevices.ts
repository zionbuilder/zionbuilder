import { ref, Ref, computed } from 'vue'

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
		width: {
			value: 991,
			unit: 'px'
		},
		height: {
			value: 768,
			unit: 'px'
		},
		units: 'px',
		icon: 'laptop'
	},
	{
		name: 'Tablet',
		id: 'tablet',
		width: {
			value: 767,
			unit: 'px'
		},
		height: {
			value: 1024,
			unit: 'px'
		},
		icon: 'tablet'
	},
	{
		name: 'Mobile',
		id: 'mobile',
		width: {
			value: 575,
			unit: 'px'
		},
		height: {
			value: 667,
			unit: 'px'
		},
		icon: 'mobile'
	}
])

const activeResponsiveOptions: Ref<{} | null> = ref(null)
const iframeWidth: Ref<number> = ref(0)

export const useResponsiveDevices = () => {
	const activeResponsiveDeviceInfo = computed(() => responsiveDevices.value.find(device => device.id === activeResponsiveDeviceId.value) || responsiveDevices.value[0])
	const setActiveResponsiveDeviceId = (device: string) => {
		activeResponsiveDeviceId.value = device

		if (activeResponsiveDeviceInfo.value.width.value) {
			setIframeWidth(activeResponsiveDeviceInfo.value.width.value)
		}
	}

	const setActiveResponsiveOptions = (instanceConfig: {}) => {
		activeResponsiveOptions.value = instanceConfig
	}

	const getActiveResponsiveOptions = () => {
		return activeResponsiveOptions.value
	}

	const removeActiveResponsiveOptions = () => {
		activeResponsiveOptions.value = null
	}

	function setIframeWidth(newWidth: number) {
		iframeWidth.value = newWidth
	}


	return {
		activeResponsiveDeviceId,
		activeResponsiveDeviceInfo,
		responsiveDevices,
		iframeWidth,
		setActiveResponsiveDeviceId,
		removeActiveResponsiveOptions,
		getActiveResponsiveOptions,
		setActiveResponsiveOptions,
		setIframeWidth
	}
}