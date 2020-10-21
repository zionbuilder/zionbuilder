<template>
	<div
		v-if="fontData"
		class="znpb-admin__google-font-tab"
	>
		<div class="znpb-admin__google-font-tab-title">{{font.font_family}}</div>
		<div class="znpb-admin__google-font-tab-variants">
			<HiddenContainer>
				{{niceFontVariants}}
				<template #content>
					<InputCheckboxGroup
						:options="fontVariantsOption"
						v-model="variantModel"
						:min="1"
					/>
				</template>
			</HiddenContainer>
		</div>
		<div class="znpb-admin__google-font-tab-subset">
			<HiddenContainer>
				{{niceFontSubsets}}
				<template #content>
					<InputCheckboxGroup
						:options="fontSubsetOption"
						v-model="subsetModel"
						:min="1"
					/>
				</template>
			</HiddenContainer>
		</div>
		<div class="znpb-admin__google-font-tab-actions">
			<Tooltip
				class="znpb-edit-icon-pop"
				append-to="element"
				:modifiers="[
					{
					name: 'offset',
					options: {
						offset: [0, 15],
					},
					},
				]"
				:content="$translate('click_to_delete_font')"
			>
				<Icon
					icon="delete"
					@click="showModalConfirm = true"
				/>
			</Tooltip>
		</div>
		<ModalConfirm
			v-if="showModalConfirm"
			:width="530"
			:confirm-text="$translate('font_delete_confirm')"
			:cancel-text="$translate('font_delete_cancel')"
			@confirm="$emit('delete', font), showModalConfirm = false"
			@cancel="showModalConfirm = false"
		>
			{{$translate('are_you_sure_google_font_delete')}}
		</ModalConfirm>
	</div>
</template>

<script>
import HiddenContainer from '../HiddenContainer.vue'
import { mapGetters } from 'vuex'
import { Icon, Tooltip, ModalConfirm } from '@zionbuilder/components'

export default {
	name: 'GoogleFontTab',
	props: {
		font: {
			type: Object,
		}
	},
	data () {
		return {
			showModalConfirm: false
		}
	},
	components: {
		HiddenContainer,
		Icon,
		Tooltip,
		ModalConfirm
	},

	computed: {
		variantModel: {
			get () {
				return this.font.font_variants
			},
			set (newValue) {
				// this.$emit('font-updated', {
				// 	...this.font,
				// 	font_variants: newValue
				// })
				let updatedValue = {
					...this.font,
					font_variants: newValue
				}
				this.$zb.options.updateOptionValue('google_fonts', {font,	value: updatedValue})
			}
		},
		subsetModel: {
			get () {
				return this.font.font_subset
			},
			set (newValue) {
				let updatedValue = {
					...this.font,
					font_subset: newValue
				}
			this.$zb.options.updateOptionValue('google_fonts', {font,	value: updatedValue})
			}
		},
		niceFontVariants () {
			let variants = []
			this.font.font_variants.forEach((variant) => {
				variants.push(this.getVarianNameFromId(variant))
			})

			return variants.join(', ')
		},
		niceFontSubsets () {
			let subsets = []
			this.font.font_subset.forEach((subset) => {
				subsets.push(this.capitalizeWords(subset.split('-').join(' ')))
			})

			return subsets.join(', ')
		},
		fontData () {
			return this.$zb.googleFonts.getFontData(this.font['font_family'])
		},
		fontVariantsOption () {
			let options = []

			this.fontData.variants.forEach((variant) => {
				options.push({
					id: variant,
					name: this.getVarianNameFromId(variant)
				})
			})
			return options
		},
		fontSubsetOption () {
			let options = []

			this.fontData.subsets.forEach((subset) => {
				options.push({
					id: subset,
					name: this.capitalizeWords(subset.split('-').join(' '))
				})
			})
			return options
		}
	},
	methods: {
		getVarianNameFromId (variant) {
			const names = {
				'100': '100',
				'100italic': '100 Italic',
				'300': '300',
				'300italic': '300 Italic',
				'regular': 'Regular',
				'italic': 'Italic',
				'500': '500',
				'500italic': '500 Italic',
				'700': '700',
				'700italic': '700 Italic',
				'900': '900',
				'900italic': '900 Italic'
			}

			if (typeof names[variant] !== 'undefined') {
				return names[variant]
			}

			return variant
		},
		capitalizeWords (words) {
			let wordsArray = words.split(' ')
			wordsArray.forEach((word, i) => {
				wordsArray[i] = wordsArray[i].charAt(0).toUpperCase() + wordsArray[i].substring(1)
			})

			return wordsArray.join(' ')
		}
	}
}
</script>

<style lang="scss">
.znpb-admin__google-font-tab {
	@extend %list-item-helper;
	padding: 17px 10px;

	& > div {
		position: relative;
		flex: 1;
		min-width: 0;
		padding: 0 10px;
	}

	& > &-title, & > &-variants {
		min-width: 32%;
	}

	&-title {
		color: $surface-active-color;
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
		.znpb-admin__google-font-tab-title, .znpb-admin__google-font-tab-variants, .znpb-admin__google-font-tab-subset, .znpb-admin__google-font-tab-actions {
			color: $font-color;
			font-size: 11px;
			font-weight: 700;
			letter-spacing: .5px;
			text-transform: uppercase;
		}
		.znpb-admin__google-font-tab-variants, .znpb-admin__google-font-tab-subset {
			text-align: left;
		}
		&:hover {
			box-shadow: none;
		}
	}
	&-actions {
		display: flex;
		justify-content: flex-end;
		.znpb-edit-icon-pop {
			cursor: pointer;
		}

		.znpb-editor-icon-wrapper {
			box-sizing: content-box;
			padding: 5px;
			font-size: 14px;
			transition: color .15s ease;

			&:hover {
				color: darken($font-color, 20%);
			}
		}
	}
}
</style>
