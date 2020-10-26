
<template>
	<a
		href="#"
		@click.stop="changeDevice"
		@mousedown.stop=""
		class="znpb-device__item"
		:class="{'znpb-device__item--active': isActiveDevice}"
		:style="{
			'justify-content': showDeviceTitles ? 'flex-start' : 'center'
		}"
	>
		<div class="znpb-device__item-content">
			<Icon
				:icon="deviceConfig.icon"
				class="znpb-device__item-icon"
				:style="{
					'margin-right': showDeviceTitles ? '5px' : 0
				}"
			></Icon>
			<span
				v-if="showDeviceTitles"
				class="znpb-device__item-name"
			>
				{{deviceConfig.name}}
			</span>
			<ChangesBullet
				v-if="hasChanges"
				:discard-changes-title="discardChangesTitle"
				@remove-styles="removeStylesGroup"
			/>
		</div>
	</a>
</template>

<script>
import { useResponsiveDevices } from '@zb/components'

// TODO: extract active device methods from options form manager
// import { getActiveResponsiveOptions, removeDeviceStyles } from '../manager/options/optionsInstance'
// import { getActiveResponsiveOptions, removeDeviceStyles } from '../manager/options/optionsInstance'

export default {
	name: 'device-element',
	props: {
		deviceConfig: Object,
		showDeviceTitles: {
			type: Boolean,
			default: true,
			required: false
		}
	},
	setup () {
		const { activeResponsiveDeviceInfo, setActiveResponsiveDeviceId } = useResponsiveDevices()

		return {
			activeResponsiveDeviceInfo,
			setActiveResponsiveDeviceId
		}
	},
	computed: {
		discardChangesTitle () {
			return this.$translate('discard_changes_for') + ' ' + this.deviceConfig.name
		},
		isActiveDevice: function () {
			return this.deviceConfig === this.activeResponsiveDeviceInfo
		},
		hasChanges () {
			const activeDevice = getActiveResponsiveOptions()
			return (activeDevice && activeDevice.value && activeDevice.value[this.deviceConfig.id]) || false
		},
		getActiveDeviceId () {
			return this.activeResponsiveDeviceInfo.id
		}
	},
	methods: {
		changeDevice () {
			if (this.activeResponsiveDeviceInfo.id !== this.deviceConfig.id) {
				// Set a new active device
				this.setActiveResponsiveDeviceId(this.deviceConfig.id)
			}
		},
		removeStylesGroup () {
			const activeDevice = getActiveResponsiveOptions()
			if (activeDevice) {
				activeDevice.removeDeviceStyles(this.deviceConfig.id)
			}
		}
	}
}
</script>

<style lang="scss">
.znpb-device__item {
	display: flex;
	display: flex;
	justify-content: flex-start;
	align-items: center;
	&-name {
		margin-top: -1px;
	}
	&-content {
		display: flex;
		align-items: center;
		min-height: 20px;
	}
	&-icon {
		margin-right: 5px;
	}
	&-has-changes {
		display: flex;
		align-items: center;
		margin-left: 5px;
		.znpb-options-has-changes-wrapper {
			&__delete {
				font-size: 7px !important;
			}
		}
	}

	&--active {
		span {
			color: $surface-active-color;
		}
	}
}
</style>
