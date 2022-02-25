<template>
	<FlyoutWrapper @mousedown.stop="">
		<template v-slot:panel-icon>
			<Icon :icon="deviceIcon" />
		</template>
		<div class="znpb-responsiveDeviceHeader">
			<div>
				<Icon icon="delete" />
				<input type="text" @keydown="onWidthKeyDown" :value="computedIframeWidth" />


			</div>

			<div>
				<Icon icon="delete" />
				<BaseInput v-model="computedIframeScale"/>

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

const { activeResponsiveDeviceInfo, responsiveDevices, iframeWidth } = useResponsiveDevices()

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

function onWidthKeyDown(event) {
	if (event.key==='Enter') {
		computedIframeWidth.value = event.target.value
	}
}
</script>