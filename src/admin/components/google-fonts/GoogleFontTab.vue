<template>
	<div v-if="fontData" class="znpb-admin__google-font-tab">
		<div class="znpb-admin__google-font-tab-title">{{ font.font_family }}</div>
		<div class="znpb-admin__google-font-tab-variants">
			<HiddenContainer>
				{{ niceFontVariants }}
				<template #content>
					<InputCheckboxGroup v-model="variantModel" :options="fontVariantsOption" :min="1" />
				</template>
			</HiddenContainer>
		</div>
		<div class="znpb-admin__google-font-tab-subset">
			<HiddenContainer>
				{{ niceFontSubsets }}
				<template #content>
					<InputCheckboxGroup v-model="subsetModel" :options="fontSubsetOption" :min="1" />
				</template>
			</HiddenContainer>
		</div>
		<div class="znpb-admin__google-font-tab-actions">
			<Icon
				v-znpb-tooltip="__('Delete font?', 'zionbuilder')"
				class="znpb-edit-icon-pop"
				icon="delete"
				@click="showModalConfirm = true"
			/>
		</div>
		<ModalConfirm
			v-if="showModalConfirm"
			:width="530"
			:confirm-text="__('Yes, delete the font', 'zionbuilder')"
			:cancel-text="__('No, keep the font', 'zionbuilder')"
			@confirm="$emit('delete', font), (showModalConfirm = false)"
			@cancel="showModalConfirm = false"
		>
			{{ __('Are you sure you want to delete this font?', 'zionbuilder') }}
		</ModalConfirm>
	</div>
</template>

<script lang="ts" setup>
import { ref, computed } from 'vue';
import { __ } from '@wordpress/i18n';
import { useGoogleFontsStore } from '@zb/store';

// Components
import HiddenContainer from '../HiddenContainer.vue';

const props = defineProps<{
	font: {
		font_family: string;
		font_variants: string[];
		font_subset: string[];
	};
}>();

const emit = defineEmits(['font-updated', 'delete']);

const showModalConfirm = ref(false);
const googleFontsStore = useGoogleFontsStore();
const fontData = googleFontsStore.getFontData(props.font['font_family']);

const variantModel = computed({
	get() {
		return props.font.font_variants;
	},
	set(newValue) {
		emit('font-updated', {
			...props.font,
			font_variants: newValue,
		});
	},
});

const subsetModel = computed({
	get() {
		return props.font.font_subset;
	},
	set(newValue) {
		emit('font-updated', {
			...props.font,
			font_subset: newValue,
		});
	},
});

const niceFontVariants = computed(() => {
	const variants: string[] = [];
	props.font.font_variants.forEach(variant => {
		variants.push(getVarianNameFromId(variant));
	});

	return variants.join(', ');
});

const niceFontSubsets = computed(() => {
	const subsets: string[] = [];
	props.font.font_subset.forEach(subset => {
		subsets.push(capitalizeWords(subset.split('-').join(' ')));
	});

	return subsets.join(', ');
});

const fontVariantsOption = computed(() => {
	const options: { id: string; name: string }[] = [];

	fontData?.variants.forEach(variant => {
		options.push({
			id: variant,
			name: getVarianNameFromId(variant),
		});
	});

	return options;
});

const fontSubsetOption = computed(() => {
	const options: { id: string; name: string }[] = [];

	fontData?.subsets.forEach(subset => {
		options.push({
			id: subset,
			name: capitalizeWords(subset.split('-').join(' ')),
		});
	});
	return options;
});

function getVarianNameFromId(variant: string) {
	const names: Record<string, string> = {
		'100': '100',
		'100italic': '100 Italic',
		'300': '300',
		'300italic': '300 Italic',
		regular: 'Regular',
		italic: 'Italic',
		'500': '500',
		'500italic': '500 Italic',
		'700': '700',
		'700italic': '700 Italic',
		'900': '900',
		'900italic': '900 Italic',
	};

	if (typeof names[variant] !== 'undefined') {
		return names[variant];
	}

	return variant;
}

function capitalizeWords(words: string) {
	return words.replace(/\w\S*/g, function (txt: string) {
		return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
	});
}
</script>

<style lang="scss">
@import '/@/common/scss/_mixins.scss';
.znpb-admin__google-font-tab {
	@extend %list-item-helper;
	padding: 17px 10px;

	@media (max-width: 767px) {
		flex-direction: column;
		align-items: flex-start;
	}

	& > div {
		position: relative;
		flex: 1;
		min-width: 0;
		padding: 0 10px;
	}

	& > &-title,
	& > &-variants {
		min-width: 32%;

		@media (max-width: 991px) {
			min-width: 20%;
		}
	}

	&-variants {
		max-width: 0;
	}

	&-title,
	&-variants,
	&-subset {
		@media (max-width: 767px) {
			margin-bottom: 10px;
		}
	}

	&-title {
		color: var(--zb-surface-text-active-color);
		font-weight: 500;
		text-transform: capitalize;
	}

	// &-variants, &-subset, &-actions {
	// 	text-align: right;
	// }

	&--titles {
		padding: 0 10px;
		margin-bottom: 12px;
		background: transparent;
		box-shadow: none;
		border: 0;

		@media (max-width: 767px) {
			display: none;
		}

		.znpb-admin__google-font-tab-title,
		.znpb-admin__google-font-tab-variants,
		.znpb-admin__google-font-tab-subset,
		.znpb-admin__google-font-tab-actions {
			color: var(--zb-surface-text-color);
			font-size: 11px;
			font-weight: 700;
			letter-spacing: 0.5px;
			text-transform: uppercase;
		}
		.znpb-admin__google-font-tab-variants,
		.znpb-admin__google-font-tab-subset {
			text-align: left;
		}
		&:hover {
			box-shadow: none;
		}
	}
	&-actions {
		display: flex;
		justify-content: flex-end;
		flex-grow: 0 !important;
		margin-left: 30px;

		@media (max-width: 767px) {
			margin-left: 0;
		}

		.znpb-edit-icon-pop {
			cursor: pointer;
		}

		.znpb-editor-icon-wrapper {
			box-sizing: content-box;
			padding: 5px;
			font-size: 14px;
			transition: color 0.15s ease;

			&:hover {
				color: var(--zb-surface-text-hover-color);
			}
		}
	}
}
</style>
