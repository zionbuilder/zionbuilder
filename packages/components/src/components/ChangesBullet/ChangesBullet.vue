<template>
	<span
		class="znpb-options-has-changes-wrapper"
		v-znpb-tooltip="discardChangesTitle"
	>
		<span
			@click.stop="$emit('remove-styles')"
			@mouseover="showIcon=true"
			@mouseleave="showIcon=false"
			class="znpb-options__has-changes"
		>
			<span v-if="!showIcon"></span>
			<Icon
				v-else
				class="znpb-options-has-changes-wrapper__delete"
				icon="close"
				:size="6"
			/>
		</span>

	</span>
</template>

<script>
import { Tooltip } from '@zionbuilder/tooltip'
import { translate } from '@zb/i18n'

export default {
	name: 'ChangesBullet',
	components: {
		Tooltip
	},
	data () {
		return {
			showIcon: false
		}
	},
	props: {
		discardChangesTitle: {
			type: String,
			required: false,
			default () {
				return translate('discard_changes')
			}
		}
	}
}
</script>

<style lang="scss">
.znpb-options__has-changes {
	position: relative;
	top: 0;
	display: flex;
	justify-content: center;
	align-items: center;
	width: 10px;
	height: 10px;
	cursor: pointer;
}

.znpb-options__has-changes::after {
	content: "";
	position: absolute;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	background: #31d783;
	border-radius: 50%;
	transition: transform .15s, background-color .1s;
}

.znpb-options__has-changes:hover::after {
	background-color: var(--zb-surface-icon-color);
	transform: scale(1.6);
}

.znpb-options-has-changes-wrapper {
	margin-left: 5px;
	cursor: pointer;
	.znpb-options-has-changes-wrapper__delete {
		z-index: 9;
		justify-content: center;

		stroke: var(--zb-surface-text-active-color);
		.zion-svg-inline {
			margin: 0;
		}
	}

	.znpb-editor-icon-wrapper {
		width: 9px;
		height: 9px;

		stroke-width: 5px;
	}
}
.znpb-horizontal-accordion__header--has-slots {
	.znpb-editor-icon-wrapper.znpb-options-has-changes-wrapper__delete {
		padding: 0;
	}
}
</style>
