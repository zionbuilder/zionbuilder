<template>
	<PageTemplate>
		<h3>{{ $translate('google_fonts') }}</h3>
		<div v-if="googleFonts.length > 0" class="znpb-admin__google-font-tab znpb-admin__google-font-tab--titles">
			<div class="znpb-admin__google-font-tab-title">{{ $translate('font_name') }}</div>
			<div class="znpb-admin__google-font-tab-variants">{{ $translate('variants') }}</div>
			<div class="znpb-admin__google-font-tab-subset">{{ $translate('subsets') }}</div>
			<div class="znpb-admin__google-font-tab-actions">{{ $translate('actions') }}</div>
		</div>

		<EmptyList
			v-if="googleFonts.length === 0"
			v-znpb-tooltip="$translate('click_me_to_add_font')"
			@click="showModal = true"
			>{{ $translate('no_google_fonts') }}</EmptyList
		>

		<div v-if="googleFonts.length > 0" class="znpb-admin-google-fonts-wrapper">
			<ListAnimation>
				<GoogleFontTab
					v-for="font in googleFonts"
					:key="font.font_family"
					class="znpb-admin-tab"
					:font="font"
					@delete="deleteFont"
					@font-updated="
						onGoogleFontUpdated({
							font,
							value: $event,
						})
					"
				/>
			</ListAnimation>
		</div>

		<Modal
			v-model:show="showModal"
			:width="570"
			class="znpb-modal-google-fonts"
			:title="$translate('google_fonts')"
			:show-backdrop="false"
		>
			<GoogleFontsModalContent
				:active-fonts="activeFontNames"
				@font-selected="onGoogleFontAdded"
				@font-removed="onGoogleFontRemoved"
			/>
		</Modal>

		<div class="znpb-admin-google-fonts-actions">
			<Button type="line" @click="showModal = true">
				<Icon icon="plus" />
				{{ $translate('add_font') }}
			</Button>
		</div>
		<template #right>
			<p class="znpb-admin-info-p">
				{{ $translate('google_fonts_1') }}
				<a href="https://fonts.google.com/">{{ $translate('google_fonts_link') }} </a>
				{{ $translate('google_fonts_2') }}
			</p>
		</template>
	</PageTemplate>
</template>

<script>
import { computed, ref } from 'vue';
import { useBuilderOptionsStore } from '/@/common/store';

// Components
import GoogleFontTab from './GoogleFontTab.vue';
import GoogleFontsModalContent from './GoogleFontsModalContent.vue';

export default {
	name: 'GoogleFonts',
	components: {
		GoogleFontTab,
		GoogleFontsModalContent,
	},
	setup(props) {
		const { getOptionValue, addGoogleFont, removeGoogleFont, updateGoogleFont } = useBuilderOptionsStore();
		const showModal = ref(false);

		let googleFonts = computed(() => {
			return getOptionValue('google_fonts');
		});
		let activeFontNames = computed(() => {
			return googleFonts.value.map(font => {
				return font.font_family;
			});
		});

		function deleteFont(font) {
			removeGoogleFont(font.font_family);
			showModal.value = false;
		}

		function onGoogleFontUpdated({ font, value: newValue }) {
			updateGoogleFont(font.font_family, newValue);
		}

		function onGoogleFontAdded(font) {
			addGoogleFont(font.family);
			showModal.value = false;
		}

		function onGoogleFontRemoved(font) {
			removeGoogleFont(font);
			showModal.value = false;
		}

		return {
			googleFonts,
			activeFontNames,
			onGoogleFontRemoved,
			onGoogleFontAdded,
			onGoogleFontUpdated,
			deleteFont,
			showModal,
		};
	},
};
</script>

<style lang="scss">
// TODO: add this
// @import "@/scss/_buttons.scss";

.znpb-admin-google-fonts {
	&-wrapper {
		margin-bottom: 10px;
	}

	&-actions {
		display: flex;
		justify-content: flex-end;
		.znpb-button {
			display: flex;
			align-items: center;
			.znpb-editor-icon-wrapper {
				margin-right: 5px;
				color: white;
				font-size: 10px;
			}
		}
	}
}
</style>
