<template>
	<FlyoutWrapper @mousedown.stop="">
		<template v-slot:panel-icon>
			<Icon :icon="deviceIcon" />
		</template>
		<div class="znpb-responsiveDeviceHeader">
			<div>
				<Icon icon="delete" />
				<input type="number" @keydown="onWidthKeyDown" :value="computedIframeWidth" />


			</div>

			<div>
				<Icon icon="delete" />
				<input type="number" @keydown="onScaleKeyDown" :value="computedIframeScale" />
				<Icon icon="plus" @click="setAutoScale(!autoscaleActive)" />
			</div>

		</div>

		<FlyoutMenuItem
			v-for="(deviceConfig, i) in responsiveDevices"
			v-bind:key="i"
		>
			<DeviceElement :device-config="deviceConfig" />

		</FlyoutMenuItem>
	</FlyoutWrapper>
</template>

<script setup>
import DeviceElement from './DeviceElement.vue'
import FlyoutWrapper from './FlyoutWrapper.vue'
import FlyoutMenuItem from './FlyoutMenuItem.vue'

import { computed } from 'vue'
import { useResponsiveDevices } from '@zb/components'

const { activeResponsiveDeviceInfo, responsiveDevices, iframeWidth, setCustomWidth, scaleValue, setCustomScale, autoscaleActive, setAutoScale } = useResponsiveDevices()

const deviceIcon = computed(() => {
	return activeResponsiveDeviceInfo.value.icon
})

const computedIframeWidth = computed({
	get() {
		return `${iframeWidth.value}`
	},
	set(newValue) {
		iframeWidth.value = newValue
	}
})

const computedIframeScale = computed({
	get() {
		return `${scaleValue.value}`
	},
	set(newValue) {
		scaleValue.value = newValue
	}
})

function onWidthKeyDown(event) {
	if (event.key==='Enter') {
		setCustomWidth(event.target.value)
	}
}

function onScaleKeyDown(event) {
	if (event.key==='Enter') {
		setCustomScale(event.target.value)
	}
}
</script>