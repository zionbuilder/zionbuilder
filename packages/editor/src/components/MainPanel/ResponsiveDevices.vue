<template>
	<FlyoutWrapper
		@mousedown.stop=""
		:prevent-close="preventClose"
	>
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
					@keydown.enter="onWidthKeyDown"
					@blur="onWidthKeyDown"
					@focus="preventClose = true"
					:value="iframeWidth"
				/>

			</div>

			<div class="znpb-responsiveDeviceHeader__item">
				<Icon
					icon="zoom"
					class="znpb-responsiveDeviceHeader__iconIndicator"
				/>
				<input
					type="number"
					@keydown.enter="onScaleKeyDown"
					@blur="onScaleKeyDown"
					@focus="preventClose = true"
					:value="scaleValue"
					:disabled="autoscaleActive"
				/>
				<Icon
					icon="lock"
					@click="setAutoScale(!autoscaleActive)"
					class="znpb-responsiveDeviceHeader__iconLock"
					:class="{
						'znpb-responsiveDeviceHeader__iconLock--locked': autoscaleActive
					}"
				/>
			</div>

		</div>

		<FlyoutMenuItem
			v-for="(deviceConfig, i) in responsiveDevices"
			v-bind:key="i"
		>
			<DeviceElement
				:device-config="deviceConfig"
				:allow-edit="editBreakpoints"
				:edited-breakpoint="editedBreakpoint"
				@edit-breakpoint="(breakpoint) => editedBreakpoint = breakpoint"
			/>
		</FlyoutMenuItem>

		<div class="znpb-responsiveDeviceFooter">
			<div
				@click="editBreakpoints = !editBreakpoints"
				class="znpb-responsiveDeviceEditButton"
			>
				<template v-if="!editBreakpoints">
					<Icon icon="edit" />
					{{$translate('edit_breakpoints')}}
				</template>
				<template v-else>
					<Icon icon="close" />
					{{$translate('disable_edit_breakpoints')}}
				</template>
			</div>

		</div>
	</FlyoutWrapper>
</template>

<script setup>
import DeviceElement from './DeviceElement.vue'
import FlyoutWrapper from './FlyoutWrapper.vue'
import FlyoutMenuItem from './FlyoutMenuItem.vue'

import { computed, ref } from 'vue'
import { useResponsiveDevices } from '@zb/components'

const { activeResponsiveDeviceInfo, responsiveDevices, iframeWidth, setCustomIframeWidth, scaleValue, setCustomScale, autoscaleActive, setAutoScale } = useResponsiveDevices()

const preventClose = ref(false)

const deviceIcon = computed(() => {
	return activeResponsiveDeviceInfo.value.icon
})

const editBreakpoints = ref(false)
const editedBreakpoint = ref(null)

function onWidthKeyDown (event) {
	setCustomIframeWidth(event.target.value, true)
	preventClose.value = false
}

function onScaleKeyDown (event) {
	setCustomScale(event.target.value)
	preventClose.value = false
}
</script>

<style lang="scss">
.znpb-responsiveDeviceHeader {
	display: flex;
	padding: 0 16px 8px;
	border-bottom: 1px solid var(--zb-surface-border-color);

	gap: 15px;

	// Hide the number input arrows
	input[type="number"] {
		-moz-appearance: textfield;

		&::-webkit-inner-spin-button, &::-webkit-outer-spin-button {
			-webkit-appearance: none;
		}
	}

	&__item {
		position: relative;
		display: flex;
		align-items: center;

		.znpb-editor-icon-wrapper {
			color: var(--zb-surface-icon-color);
		}

		.znpb-responsiveDeviceHeader__iconIndicator {
			margin-right: 5px;
			font-size: 12px;
		}

		.znpb-responsiveDeviceHeader__iconLock {
			position: absolute;
			right: 8px;

			&--locked {
				color: var(--zb-secondary-color);
			}
		}



		input {
			max-width: 80px;
			padding: 6.5px 25px 6.5px 8px;
			color: var(--zb-input-text-color);
			font-family: var(--zb-font-stack);
			font-size: 13px;
			font-weight: 500;
			line-height: 1;
			background-color: var(--zb-input-bg-color);
			border: 2px solid var(--zb-input-border-color);
			border-radius: 3px;

			-webkit-appearance: none;

			&:focus {
				outline: 0;
			}
		}

		&--locked input {
			cursor: not-allowed;
			opacity: .6;
			pointer-events: none;
		}
	}
}

.znpb-responsiveDeviceEditButton {
	display: flex;
	justify-content: center;
	align-items: center;
	padding: 5px 20px;
}
</style>