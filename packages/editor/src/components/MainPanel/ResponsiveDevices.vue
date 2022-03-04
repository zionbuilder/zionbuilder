<template>
	<FlyoutWrapper @mousedown.stop="">
		<template v-slot:panel-icon>
			<Icon :icon="deviceIcon" />
		</template>
		<div class="znpb-responsiveDeviceHeader">
			<div class="znpb-responsiveDeviceHeader__item">
				<Icon
					icon="width"
					class="znpb-responsiveDeviceHeader__iconIndicator"
				/>
				<input
					type="number"
					@keydown="onWidthKeyDown"
					:value="computedIframeWidth"
				/>

			</div>

			<div class="znpb-responsiveDeviceHeader__item">
				<Icon
					icon="zoom"
					class="znpb-responsiveDeviceHeader__iconIndicator"
				/>
				<input
					type="number"
					@keydown="onScaleKeyDown"
					:value="computedIframeScale"
				/>
				<Icon
					icon="lock"
					@click="setAutoScale(!autoscaleActive)"
					class="znpb-responsiveDeviceHeader__iconLock"
				/>
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
	get () {
		return `${iframeWidth.value}`
	},
	set (newValue) {
		iframeWidth.value = newValue
	}
})

const computedIframeScale = computed({
	get () {
		return `${scaleValue.value}`
	},
	set (newValue) {
		scaleValue.value = newValue
	}
})

function onWidthKeyDown (event) {
	if (event.key === 'Enter') {
		setCustomWidth(event.target.value)
	}
}

function onScaleKeyDown (event) {
	if (event.key === 'Enter') {
		setCustomScale(event.target.value)
	}
}
</script>

<style lang="scss">
.znpb-responsiveDeviceHeader {
	display: flex;
	gap: 15px;
	border-bottom: 1px solid var(--zb-surface-border-color);
	padding: 0 16px 8px;

	&__item {
		display: flex;
		align-items: center;
		position: relative;

		.znpb-editor-icon-wrapper {
			color: var(--zb-surface-icon-color);
		}

		.znpb-responsiveDeviceHeader__iconIndicator {
			font-size: 12px;
			margin-right: 5px;
		}

		.znpb-responsiveDeviceHeader__iconLock {
			position: absolute;
			right: 8px;
		}

		&--locked .znpb-responsiveDeviceHeader__iconLock {
			color: var(--zb-secondary-color);
		}

		input {
			color: var(--zb-input-text-color);
			font-family: var(--zb-font-stack);
			font-size: 13px;
			font-weight: 500;
			line-height: 1;
			max-width: 80px;
			background-color: var(--zb-input-bg-color);
			border: 2px solid var(--zb-input-border-color);
			border-radius: 3px;
			-webkit-appearance: none;
			padding: 6.5px 25px 6.5px 8px;

			&:focus {
				outline: 0;
			}
		}

		&--locked input {
			cursor: not-allowed;
			opacity: 0.6;
			pointer-events: none;
		}
	}
}
</style>