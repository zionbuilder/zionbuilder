import { ref, Ref, computed } from 'vue'

const activeResponsiveDeviceId = ref('default')
const responsiveDevices: Ref<[]> = ref([
	{
		name: 'Desktop',
		id: 'default',
		width: {
			value: '100',
			unit: '%'
		},
		height: {
			value: '100',
			unit: '%'
		},
		icon: 'desktop'
	},
	{
		name: 'Laptop',
		id: 'laptop',
		width: {
			value: '991',
			unit: 'px'
		},
		height: {
			value: '768',
			unit: 'px'
		},
		units: 'px',
		icon: 'laptop'
	},
	{
		name: 'Tablet',
		id: 'tablet',
		width: {
			value: '767',
			unit: 'px'
		},
		height: {
			value: '1024',
			unit: 'px'
		},
		icon: 'tablet'
	},
	{
		name: 'Mobile',
		id: 'mobile',
		width: {
			value: '575',
			unit: 'px'
		},
		height: {
			value: '667',
			unit: 'px'
		},
		units: 'px',
		icon: 'mobile'
	}
])

export const useResponsiveDevices = () => {
	const activeResponsiveDeviceInfo = computed(() => responsiveDevices.value.find(device => device.id === activeResponsiveDeviceId.value))
	const setActiveResponsiveDevice = (device) => {
		activeResponsiveDeviceId.value = device
	}

	return {
		activeResponsiveDeviceId,
		activeResponsiveDeviceInfo,
		responsiveDevices,
		setActiveResponsiveDevice
	}
}