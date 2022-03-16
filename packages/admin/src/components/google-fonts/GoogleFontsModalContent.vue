<template>
	<div class="znpb-admin__google-fonts-modal-wrapper">
		<div class="znpb-admin__google-fonts-modal-search">
			<BaseInput
				v-model="keyword"
				:placeholder="$translate('search_for_fonts')"
				:clearable="true"
				icon="search"
				size="big"
			/>
		</div>
		<ListScroll
			class="znpb-admin__google-fonts-modal-font-list-wrapper"
			list-class="znpb-admin__google-fonts-modal-font-list-container"
			@scroll-end="onScrollEnd"
			:loading="loading"
		>
			<GoogleFontModalElement
				v-for="font in visibleFonts"
				:key="font.family"
				:font="font"
				:is-active="activeFonts.includes(font.family)"
				@font-selected="$emit('font-selected', font)"
				@font-removed="$emit('font-removed', $event)"
			/>
		</ListScroll>
	</div>
</template>

<script>
import { ref, computed, watch } from 'vue'
import { useGoogleFonts } from '@zionbuilder/composables'

// Components
import GoogleFontModalElement from './GoogleFontModalElement.vue'

export default {
	name: 'GoogleFontsModalContent',
	components: {
		GoogleFontModalElement
	},
	props: {
		activeFonts: {
			type: Array,
			required: true
		}
	},
	setup (props) {
		const { googleFonts } = useGoogleFonts()
		const fontsPerPage = 20

		const currentPage = ref(1)
		const keyword = ref('')
		const loading = ref(false)
		const allFonts = computed(() => {
			let fonts = googleFonts.value

			if (keyword.value.length > 0) {
				fonts = googleFonts.value.filter((font) => {
					return font.family.toLowerCase().indexOf(keyword.value.toLowerCase()) !== -1
				})
			}

			return fonts
		})

		const visibleFonts = computed(() => {
			const end = fontsPerPage * currentPage.value

			return allFonts.value.slice(0, end)
		})

		const maxPages = computed(() => Math.ceil(allFonts.value.length / fontsPerPage))

		watch(visibleFonts, (newValue) => {
			let fontLink = document.getElementById('znpb-google-fonts-script')

			let fontsSource = newValue.map((font) => {
				let variant = ''
				if (!font.variants.includes(400)) {
					variant = `:${font.variants[0]}`
				}

				return font.family.replace(' ', '+') + variant
			})

			if (!fontLink) {
				const head = document.head
				fontLink = document.createElement('link')
				fontLink.rel = 'stylesheet'
				fontLink.id = `znpb-google-fonts-script`
				fontLink.type = 'text/css'
				fontLink.media = 'all'
				head.appendChild(fontLink)
			}

			fontLink.href = `https://fonts.googleapis.com/css?family=${fontsSource.join('|')}`
		})

		function onScrollEnd (event) {
			console.log('scroll end');
			if (currentPage.value !== maxPages.value) {
				currentPage.value++
				loading.value = true

				// Fake loading
				setTimeout(() => {
					loading.value = false
				}, 300)
			}
		}

		return {
			visibleFonts,
			keyword,
			loading,
			onScrollEnd
		}
	}
}
</script>
<style lang="scss">
.znpb-modal-google-fonts {
	& > .znpb-modal__wrapper {
		height: 90%;
	}
}
.znpb-admin__google-fonts-modal {
	&-wrapper {
		display: flex;
		flex-direction: column;
		min-height: 0;
		padding: 20px;

		.znpb-admin-small-loader {
			top: auto;
			right: 0;
			bottom: 30px;
			left: 0;
			width: 20px;
			height: 20px;
			background: var(--zb-surface-color);
			&:before, &:after {
				border: 2px solid var(--zb-surface-border-color);
			}
			&:after {
				border-right-color: var(--zb-surface-color);
			}
		}
	}

	&-search {
		margin-bottom: 20px;
	}

	&-font-list {
		&-wrapper {
			display: flex;
			flex-direction: column;
			min-height: 0;
		}
	}
}
</style>
