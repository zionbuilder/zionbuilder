<template>
	<div class="znpb-admin-color-preset-box" :class="{ ['znpb-admin-color-preset-box--' + type]: type }">
		<Tooltip
			tooltip-class="hg-popper--no-padding"
			:trigger="null"
			:show="showColorPicker"
			placement="right-start"
			:modifiers="[]"
			:show-arrows="false"
		>
			<template #content>
				<ColorPicker
					ref="colorpickerHolder"
					v-click-outside="closeColorpicker"
					:model="color || ''"
					:show-library="false"
					@color-changed="updateColor"
				/>
			</template>
			<div v-if="type == 'addcolor'" class="znpb-admin-color-preset-box__empty" @click.stop="showColorPicker = true">
				<Icon icon="plus" />
				<div>{{ $translate('add_color') }}</div>
			</div>
			<div v-else class="znpb-admin-color-preset-box__color" @click.stop="showColorPicker = true">
				<Icon
					v-znpb-tooltip="$translate('delete_color_from_preset')"
					icon="close"
					@click.stop="$emit('delete-color')"
				/>

				<div class="znpb-admin-color-preset-box__circle--transparent">
					<div
						ref="circleTrigger"
						class="znpb-admin-color-preset-box__circle"
						:style="{ background: localColor }"
					></div>
				</div>
				<div class="znpb-admin-color-preset-box__color-name">
					<span>{{ localColor }}</span>
				</div>
			</div>
		</Tooltip>
	</div>
</template>
<script>
import { ref, watchEffect } from 'vue';
import clickOutside from '../../../common/directives/click-outside.ts';

export default {
	name: 'ColorBox',
	directives: {
		clickOutside,
	},
	props: {
		color: {
			type: String,
			required: false,
		},
		type: {
			type: String,
			required: false,
		},
	},
	setup(props, { emit }) {
		const localColor = ref('');
		const showColorPicker = ref(false);

		watchEffect(() => {
			localColor.value = props.color;
		});

		function updateColor(color) {
			localColor.value = color;
		}

		function closeColorpicker() {
			showColorPicker.value = false;
			// Check if color has changed
			if (props.color !== localColor.value) {
				emit('option-updated', localColor.value);
			}
		}

		return {
			localColor,
			showColorPicker,
			updateColor,
			closeColorpicker,
		};
	},
};
</script>

<style lang="scss">
@import "/@/common/scss/_mixins.scss";

.znpb-admin-content__center {
	.znpb-tabs--minimal {
		.znpb-tabs__header-item {
			margin-bottom: 15px;
		}
	}
}
.znpb-admin-colors__wrapper {
	.znpb-admin-colors__container {
		display: grid;

		grid-gap: 20px;
		grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
		grid-template-rows: repeat(1, 145px);
	}
}
.znpb-admin-color-preset-box {
	position: relative;
	margin-bottom: 5px;
	box-shadow: 0 5px 10px 0 var(--zb-surface-shadow);
	border: 1px solid var(--zb-surface-border-color);
	border-radius: 3px;

	&--addcolor {
		position: relative;
		color: var(--zb-surface-icon-color);
		background: var(--zb-surface-lightest-color);
		box-shadow: none;
		border: 2px dashed var(--zb-surface-border-color);
		cursor: pointer;
		.znpb-admin-color-preset-box__empty {
			.znpb-editor-icon-wrapper {
				align-self: center;
				margin-bottom: 15px;
				background: transparent;
				.zion-icon.zion-svg-inline {
					width: 16px;
					height: 16px;
				}
			}
		}
	}
	&__color {
		span:first-child {
			align-self: flex-end;
		}
	}
	&__color, &__empty {
		position: relative;
		display: flex;
		flex-direction: column;
		align-items: center;
		padding: 0 9px;
	}
	&__empty {
		justify-content: center;
		min-height: 136px;
	}

	.znpb-editor-icon-wrapper {
		@include circlesimple(20px);
		align-self: flex-end;
		margin-top: 7px;
		border: 1px solid var(--zb-surface-border-color);
		transition: all .15s;
		cursor: pointer;
		.zion-icon.zion-svg-inline {
			width: 8px;
			margin: 0 auto;
			color: var(--zb-surface-icon-color);
		}
	}

	&__circle {
		@include circlesimple(60px);
		cursor: pointer;
		&--transparent {
			@include circlesimple(60px);
			@extend %opacitybg;
			margin-bottom: 18px;
			box-shadow: 0 0 0 2px var(--zb-surface-border-color);
		}
	}

	&__color-name {
		overflow: hidden;
		max-width: 70px;
		margin-bottom: 18px;
		line-height: 13px;
		white-space: nowrap;
		span {
			text-overflow: ellipsis;
		}
	}
}
</style>
