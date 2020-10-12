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
import { getGoogleFonts } from '@zb/rest'
import GoogleFontModalElement from './GoogleFontModalElement.vue'
import ListScroll from '../ListScroll.vue'
// import { ref } from "vue"

export default {
	name: 'GoogleFontsModalContent',
	props: {
		activeFonts: {
			type: Array,
			required: true
		}
	},
	data () {
		return {
			visibleFonts: [],
			fontsList: [],
			filteredList: [],
			currentPage: 1,
			fontsPerPage: 20,
			keyword: '',
			loading: false
		}
	},
	computed: {
		maxPages () {
			return Math.ceil(this.filteredList.length / this.fontsPerPage)
		}
	},
	components: {
		GoogleFontModalElement,
		ListScroll
	},
	created () {
		// Set initial fonts
		if (this.$zb.googleFonts.models.length) {
			console.log('this.$zb.googleFonts',this.$zb.googleFonts)
			this.fontsList = this.$zb.googleFonts.models
			this.filteredList = this.$zb.googleFonts.models
		} else {
			Promise.all([getGoogleFonts]).then((values) => {
				this.fontsList = this.$zb.googleFonts.add(values[0].data)
				this.filteredList = this.$zb.googleFonts.add(values[0].data)
			}).finally(() => {
				this.loading = false
			})
		}


		this.visibleFonts = this.getfontsPage(1)
	},
	watch: {
		// Update font link source
		visibleFonts (newValue) {
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
		},
		keyword (newValue) {
			if (this.keyword.length > 3) {
				this.loading = true
				setTimeout(() => {
					this.visibleFonts = this.getfontsPage(1)
					this.loading = false
					this.$refs.fontsContainer.scrollTo(0, 0)
				}, 300)
				this.filteredList = this.fontsList.filter((font) => {
					return font.family.toLowerCase().indexOf(this.keyword.toLowerCase()) !== -1
				})
			} else if (this.keyword.length === 0) {
				this.filteredList = this.fontsList
				this.visibleFonts = this.getfontsPage(1)
			}
		}
	},
	methods: {
		onScrollEnd (event) {
			if (this.currentPage !== this.maxPages) {
				this.currentPage++
				this.loading = true
				setTimeout(() => {
					this.visibleFonts.push(...this.getfontsPage(this.currentPage))
					this.loading = false
				}, 300)
			}
		},
		getfontsPage (page) {
			const start = this.fontsPerPage * page - this.fontsPerPage
			const end = this.fontsPerPage * page
			return this.filteredList.slice(start, end)
		}
	}
}
</script>
<style lang="scss">
.znpb-modal-google-fonts {
	& > .znpb-modal__wrapper {
		width: 100%;
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
			background: $surface;
			&:before, &:after {
				border: 2px solid $surface-variant;
			}
			&:after {
				border-right-color: #fff;
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
