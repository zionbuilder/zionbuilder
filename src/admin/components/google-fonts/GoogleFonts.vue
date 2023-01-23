<template>
	<PageTemplate>
		<h3>{{ i18n.__('Google Fonts', 'zionbuilder') }}</h3>
		<div v-if="googleFonts.length > 0" class="znpb-admin__google-font-tab znpb-admin__google-font-tab--titles">
			<div class="znpb-admin__google-font-tab-title">{{ i18n.__('Font name', 'zionbuilder') }}</div>
			<div class="znpb-admin__google-font-tab-variants">{{ i18n.__('variants', 'zionbuilder') }}</div>
			<div class="znpb-admin__google-font-tab-subset">{{ i18n.__('subsets', 'zionbuilder') }}</div>
			<div class="znpb-admin__google-font-tab-actions">{{ i18n.__('actions', 'zionbuilder') }}</div>
		</div>

		<EmptyList
			v-if="googleFonts.length === 0"
			v-znpb-tooltip="i18n.__('Click Me or the Blue button to add a Font', 'zionbuilder')"
			@click="showModal = true"
			>{{ i18n.__('No Google fonts added', 'zionbuilder') }}</EmptyList
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
			:title="i18n.__('Google Fonts', 'zionbuilder')"
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
				{{ i18n.__('Add Font', 'zionbuilder') }}
			</Button>
		</div>
		<template #right>
			<p class="znpb-admin-info-p">
				{{ i18n.__('Setting up', 'zionbuilder') }}
				<a href="https://fonts.google.com/">{{ i18n.__('Google web fonts', 'zionbuilder') }} </a>
				{{ i18n.__("has never been easier. Choose which ones to use for your website's stylish typography", 'zionbuilder') }}
			</p>
		</template>
	</PageTemplate>
</template>

<script lang="ts" setup>
import * as i18n from '@wordpress/i18n';
import { computed, ref } from 'vue';
import { useBuilderOptionsStore } from '@zb/store';

// Components
import GoogleFontTab from './GoogleFontTab.vue';
import GoogleFontsModalContent from './GoogleFontsModalContent.vue';

const { getOptionValue, addGoogleFont, removeGoogleFont, updateGoogleFont } = useBuilderOptionsStore();
const showModal = ref(false);

const googleFonts = computed(() => {
	return getOptionValue('google_fonts');
});
const activeFontNames = computed(() => {
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
