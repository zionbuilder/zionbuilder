<template>
	<PageTemplate>
		<h3>{{$translate('google_fonts')}}</h3>
		<div v-if="googleFonts.length > 0" class="znpb-admin__google-font-tab znpb-admin__google-font-tab--titles">
			<div class="znpb-admin__google-font-tab-title">{{$translate('font_name')}}</div>
			<div class="znpb-admin__google-font-tab-variants">{{$translate('variants')}}</div>
			<div class="znpb-admin__google-font-tab-subset">{{$translate('subsets')}}</div>
			<div class="znpb-admin__google-font-tab-actions">{{$translate('actions')}}</div>
		</div>

		<Tooltip
			v-if="googleFonts.length === 0"
			:content="$translate('click_me_to_add_font')"
		>
			<EmptyList
				@click.native="showModal=true"
			>{{$translate('no_google_fonts')}}</EmptyList>
		</Tooltip>

		<div v-if="googleFonts.length > 0" class="znpb-admin-google-fonts-wrapper">
			<ListAnimation>
				<GoogleFontTab
					v-for="font in googleFonts"
					:key="font.font_family"
					class="znpb-admin-tab"
					:font="font"
					@delete="deleteFont(font.font_family)"
					@font-updated="updateGoogleFont({
						font,
						value: arguments[0]
					})"
				/>
			</ListAnimation>
		</div>

		<modal
			:show.sync="showModal"
			:width="570"
			class="znpb-modal-google-fonts"
			:title="$translate('google_fonts')"
			:fullscreen="true"
			:show-backdrop="false"
		>
			<GoogleFontsModalContent
				:activeFonts="activeFontNames"
				@font-selected="onGoogleFontAdded"
				@font-removed="onGoogleFontRemoved"
			/>
		</modal>

		<div class="znpb-admin-google-fonts-actions">
			<BaseButton @click.native="showModal=true" type="line">
				<BaseIcon icon="plus"/>
				{{$translate('add_font')}}
			</BaseButton>
		</div>
		<template slot="right">
			<p class="znpb-admin-info-p">{{$translate('google_fonts_1')}} <a href="https://fonts.google.com/">{{$translate('google_fonts_link')}} </a> {{$translate ('google_fonts_2')}}</p>
		</template>

	</PageTemplate>

</template>

<script>
import { mapGetters, mapActions } from 'vuex'
import GoogleFontTab from './GoogleFontTab.vue'
import GoogleFontsModalContent from './GoogleFontsModalContent.vue'

export default {
	name: 'GoogleFonts',
	data () {
		return {
			showModal: false
		}
	},
	components: {
		GoogleFontTab,
		GoogleFontsModalContent
	},
	computed: {
		...mapGetters([
			'getOptionValue'
		]),
		googleFonts () {
			return this.getOptionValue('google_fonts')
		},
		activeFontNames () {
			return this.googleFonts.map((font) => {
				return font.font_family
			})
		}
	},
	methods: {
		...mapActions(['deleteGoogleFont', 'updateGoogleFont', 'addGoogleFont', 'saveOptions']),
		deleteFont (font) {
			this.deleteGoogleFont(font)
		},
		onGoogleFontAdded (font) {
			this.addGoogleFont({
				font_family: font.family,
				font_variants: ['regular'],
				font_subset: ['latin']
			})
			this.showModal = false
		},
		onGoogleFontRemoved (font) {
			this.deleteGoogleFont(font)
			this.showModal = false
		}
	}
}
</script>

<style lang="scss">

@import "@/scss/_buttons.scss";

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
