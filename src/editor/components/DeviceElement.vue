
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
			<BaseIcon
				:icon="deviceConfig.icon"
				class="znpb-device__item-icon"
				:style="{
					'margin-right': showDeviceTitles ? '5px' : 0
				}"
			></BaseIcon>
			<span
				v-if="showDeviceTitles"
				class="znpb-device__item-name"
			>
				{{deviceConfig.name}}
			</span>
			<HasChanges
				v-if="hasChanges"
				:discard-changes-title="discardChangesTitle"
				@remove-styles="removeStylesGroup"
			/>
		</div>
	</a>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
import HasChanges from './elementOptions/HasChanges'

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
	data: function () {
		return {}
	},
	components: {
		HasChanges
	},
	computed: {
		...mapGetters([
			'getActiveDevice'
		]),
		discardChangesTitle () {
			return this.$translate('discard_changes_for') + ' ' + this.deviceConfig.name
		},
		isActiveDevice: function () {
			return this.deviceConfig === this.getActiveDevice
		},
		hasChanges () {
			const activeDevice = window.zb.OptionsManager.getActiveResponsiveOptions()
			return (activeDevice && activeDevice.value && activeDevice.value[this.deviceConfig.id]) || false
		},
		getActiveDeviceId () {
			return this.getActiveDevice.id
		}
	},
	methods: {
		...mapActions([
			'setActiveDevice'
		]),
		changeDevice () {
			if (this.getActiveDevice.id !== this.deviceConfig.id) {
				// Set a new active device
				this.setActiveDevice(this.deviceConfig.id)
			}
		},
		removeStylesGroup () {
			const activeDevice = window.zb.OptionsManager.getActiveResponsiveOptions()
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
