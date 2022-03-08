import { ref, Ref, computed, readonly } from 'vue'
import { generateUID } from '@zb/utils'
import { orderBy } from 'lodash-es'

interface ResponsiveDevice {
	name?: string;
	id: string;
	width?: number,
	height?: number,
	icon: string,
	isCustom?: boolean,
	isDefault?: boolean,
	builtIn?: boolean
}

const deviceSizesConfig = [
	{
		width: 992,
		icon: 'laptop'
	},
	{
		width: 768,
		icon: 'tablet'
	},
	{
		width: 575,
		icon: 'mobile'
	}
]

const activeResponsiveDeviceId = ref('default')
const responsiveDevices: Ref<Array<ResponsiveDevice>> = ref([
	{
		name: 'Desktop',
		id: 'default',
		icon: 'desktop',
		minWidth: 1200,
		builtIn: true,
		isDefault: true
	},
	{
		name: 'Laptop',
		id: 'laptop',
		width: 991,
		height: 768,
		units: 'px',
		icon: 'laptop',
		builtIn: true
	},
	{
		name: 'Tablet',
		id: 'tablet',
		width: 767,
		height: 1024,
		icon: 'tablet',
		builtIn: true
	},
	{
		name: 'Mobile',
		id: 'mobile',
		width: 575,
		height: 667,
		icon: 'mobile',
		builtIn: true
	}
])

const activeResponsiveOptions: Ref<{} | null> = ref(null)
const iframeWidth: Ref<number | null> = ref(0)
const autoscaleActive = ref(false)
const scaleValue = ref(100)
const ignoreWidthChangeFlag = ref(false)

const orderedResponsiveDevices = computed(() => {
	return orderBy(responsiveDevices.value, ['width'], ['desc'])
})

const responsiveDevicesAsIdWidth = computed(() => {
	let devices = {}
	orderedResponsiveDevices.value.forEach(deviceConfig => {
		devices[deviceConfig.id] = deviceConfig.width
	})

	return devices
})

const activeResponsiveDeviceInfo = computed(() => responsiveDevices.value.find(device => device.id === activeResponsiveDeviceId.value) || responsiveDevices.value[0])

export const useResponsiveDevices = () => {
	/**
	 * Sets a current device as active
	 * Sets the current device width
	 *
	 * @param device string The device id to set as default
	 */
	function setActiveResponsiveDeviceId(device: string) {
		activeResponsiveDeviceId.value = device
	}

	/**
	 * Enables or disables automatic scale
	 *
	 * @param {boolean} scaleEnabled  If the scaling is enabled or not
	 */
	function setAutoScale(scaleEnabled: boolean) {
		autoscaleActive.value = scaleEnabled

		if (scaleEnabled) {
			scaleValue.value = 100
		}
	}

	/**
	 * Set a custom scaling value
	 *
	 * @param {number} newValue The custom scaling factor
	 */
	function setCustomScale(newValue: number) {
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

	function setCustomIframeWidth(newWidth: number, changeDevice = false) {
		// Set the minimum width to 240 as there shouldn't be any devices with lower screen size
		const actualWidth = newWidth < 240 ? 240 : newWidth

		// Set the active device base on width
		if (newWidth && changeDevice) {
			// Set the active device
			let activeDevice = 'default'
			responsiveDevices.value.forEach(device => {
				if (device.width && device.width >= actualWidth) {
					activeDevice = device.id
				}
			})

			if (activeDevice && activeDevice !== activeResponsiveDeviceId.value) {
				// This is needed for the watch inside PreviewIframe that changes the width when the device changes
				ignoreWidthChangeFlag.value = true
				setActiveResponsiveDeviceId(activeDevice)
			}
		}

		iframeWidth.value = actualWidth
	}

	function addCustomBreakpoint(breakPoint: ResponsiveDevice) {
		const { width, icon = 'desktop' } = breakPoint

		const newDeviceData = {
			width,
			icon,
			isCustom: true,
			id: generateUID()
		}

		responsiveDevices.value.push(newDeviceData)

		return newDeviceData
	}

	function deleteBreakpoint(breakpointID: string) {
		const deviceConfig = responsiveDevices.value.find(deviceConfig => deviceConfig.id === breakpointID)

		if (deviceConfig) {
			const index = responsiveDevices.value.indexOf(deviceConfig)
			responsiveDevices.value.splice(index, 1)
		}
	}

	return {
		ignoreWidthChangeFlag,
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
		setAutoScale,
		addCustomBreakpoint,
		deleteBreakpoint,
		deviceSizesConfig,

		// Computed
		responsiveDevicesAsIdWidth,
		orderedResponsiveDevices
	}
}