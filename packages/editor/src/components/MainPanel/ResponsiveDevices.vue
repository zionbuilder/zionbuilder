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
				<label
					for="znpb-responsive__iframeWidth"
					class="znpb-responsiveDeviceHeader__iconIndicator"
				>
					<Icon
						icon="width"
					/>
				</label>

				<input
					id="znpb-responsive__iframeWidth"
					type="number"
					@keydown.enter="onWidthKeyDown"
					@blur="onWidthKeyDown"
					@focus="preventClose = true"
					:value="iframeWidth"
				/>

			</div>

			<div class="znpb-responsiveDeviceHeader__item">
				<label
					for="znpb-responsive__iframeScale"
					class="znpb-responsiveDeviceHeader__iconIndicator"
				>
					<Icon
						icon="zoom"
					/>
				</label>

				<input
					id="znpb-responsive__iframeScale"
					type="number"
					@keydown.enter="onScaleKeyDown"
					@blur="onScaleKeyDown"
					@focus="preventClose = true"
					:value="scaleValue"
					:disabled="autoscaleActive"
				/>
				<Icon
					:icon="autoscaleActive ? 'lock' : 'unlock'"
					@click="setAutoScale(!autoscaleActive)"
					class="znpb-responsiveDeviceHeader__iconLock"
					:class="{
						'znpb-responsiveDeviceHeader__iconLock--locked': autoscaleActive
					}"
					v-znpb-tooltip="autoscaleActive ? $translate('disable_autoscale') : $translate('enable_autoscale')"
				/>
			</div>

		</div>

		<FlyoutMenuItem
			v-for="(deviceConfig, i) in orderedResponsiveDevices"
			v-bind:key="i"
		>
			<DeviceElement
				:device-config="deviceConfig"
				:allow-edit="editBreakpoints"
				:edited-breakpoint="editedBreakpoint"
				@edit-breakpoint="(breakpoint) => editedBreakpoint = breakpoint"
			/>
		</FlyoutMenuItem>

		<li
			class="menu-items znpb-device__addBreakpointForm"
			v-if="enabledAddBreakpoint"
		>
			<a class="znpb-device__item-content">
				<Icon
					:icon="addBreakpointDeviceIcon"
					class="znpb-device__item-icon"
				/>

				<span class="znpb-device__item-name" >
					{{$translate('max')}}

					<span class="znpb-device__itemValue">
						<span class="znpb-device__itemValue-inner">
							<input
								ref="widthInput"
								type="number"
								class="znpb-device__itemValueInput"
								v-model="newBreakpointValue"
								@keydown.enter="addNewBreakpoint"
								min="240"
							>
							px
						</span>
					</span>
					<div
						class="znpb-device__item-actions"
					>
						<Icon
							icon="check"
							class="znpb-device__item-action"
							@click.stop="addNewBreakpoint"
							v-znpb-tooltip="$translate('save')"
						/>
						<Icon
							icon="close"
							class="znpb-device__item-action"
							@click="cancelNewBreakpointAdd"
							v-znpb-tooltip="$translate('cancel')"
						/>

					</div>
				</span>
			</a>
		</li>

		<div
			class="znpb-device__addBreakpoint"
			v-if="editBreakpoints"
			@click="enabledAddBreakpoint = true"
		>
			{{$translate('add_breakpoint')}}
		</div>

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
import { orderBy } from 'lodash-es'
import { useResponsiveDevices } from '@zb/components'


const { activeResponsiveDeviceInfo, responsiveDevices, iframeWidth, setCustomIframeWidth, scaleValue, setCustomScale, autoscaleActive, setAutoScale, deviceSizesConfig, addCustomBreakpoint } = useResponsiveDevices()

const preventClose = ref(false)
const enabledAddBreakpoint = ref(false)
const newBreakpointValue = ref(500)

const orderedResponsiveDevices = computed(() => {
	return orderBy(responsiveDevices.value, ['width'], ['desc'])
})

/**
 * This will be used to set the proper device icon when adding new breakpoint
 */
const widthInput = ref(null)
const addBreakpointDeviceIcon = computed(() => {
	let deviceIcon = 'desktop'
	const currentValue = newBreakpointValue.value

	deviceSizesConfig.forEach(device => {
		if (currentValue < device.width) {
			deviceIcon = device.icon
		}
	})

	return deviceIcon
})

function addNewBreakpoint() {
	const newValue = newBreakpointValue.value < 240 ? 240 : newBreakpointValue.value

	addCustomBreakpoint({
		width: newValue,
		icon: addBreakpointDeviceIcon.value
	})

	// Cleanup add form
	cancelNewBreakpointAdd()
}

function cancelNewBreakpointAdd() {
	enabledAddBreakpoint.value = false
	newBreakpointValue.value = 500
}


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
	margin-bottom: 8px;
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
			cursor: pointer;
		}

		.znpb-responsiveDeviceHeader__iconLock {
			position: absolute;
			right: 8px;

			&--locked {
				color: var(--zb-secondary-color);
			}
		}

		input {
			max-width: 85px;
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

			&[disabled] {
				opacity: .6;
				pointer-events: none;
			}
		}
	}
}

.znpb-responsiveDeviceFooter {
	margin-top: 8px;
	border-top: 1px solid var(--zb-surface-border-color);
}

.znpb-responsiveDeviceEditButton {
	display: flex;
	justify-content: center;
	align-items: center;
	padding: 12px 16px 4px;
	transition: color .2s;

	&:hover {
		color: var(--zb-surface-text-hover-color);
	}

	.znpb-editor-icon-wrapper {
		margin-right: 5px;
		font-size: 12px;
	}
}
</style>