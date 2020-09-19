<template>
	<div>
		<slot name="start" />

		<div
			:class="{'znpb__server-element--loading': loading}"
			v-html="elementContent"
			ref="elementContent"
		/>

		<div
			class="znpb__server-element--empty"
			v-if="!loading && (elementContent.length === 0 || elementNotSelectable || requiresDataForRender)"
		>
			<img :src="logoUrl" />
		</div>

		<div
			v-if="loading"
			class="znpb__server-element-loader--loading"
		/>

		<slot name="end" />
	</div>
</template>

<script>
// Utils
import { getElementRender } from '@/api/Elements'
import { debounce } from '@/utils/'

export default {
	name: 'ServerComponent',
	inject: {
		elementInfo: {
			from: 'elementInfo',
			default: () => { }
		}
	},
	props: {
		options: {
			type: Object
		},
		data: {
			type: Object
		}
	},
	data () {
		return {
			elementContent: '',
			loading: true,
			elementNotSelectable: false
		}
	},
	computed: {
		requiresDataForRender () {
			const elementModel = this.getElementModel()
			const { _styles, _advanced_options: advancedOptions, ...options } = this.options
			return elementModel.requires_data_for_render && Object.keys(options).length === 0
		},
		logoUrl () {
			return window.zb.urls.logo
		}
	},
	methods: {
		getElementModel () {
			return this.elementInfo.elementModel
		},
		getElementFromServer () {
			this.loading = true
			getElementRender(this.data).then((response) => {
				this.elementContent = response.data.element
				this.loading = false
				this.$nextTick(() => {
					this.checkForContentHeight()
				})
			}).finally(() => {
				this.loading = false
			})
		},
		debouncedGetElementFromServer: debounce(function () {
			this.getElementFromServer()
		}, 500),
		checkForContentHeight () {
			const loadableElements = this.$refs.elementContent.querySelectorAll('img, iframe, video')
			let loadableElementsCount = loadableElements.length

			const loadCallback = () => {
				loadableElementsCount--

				if (loadableElementsCount === 0) {
					this.checkElementHeight()
				}
			}

			if (loadableElementsCount > 0) {
				loadableElements.forEach(element => {
					element.addEventListener('load', loadCallback)
					element.addEventListener('error', loadCallback)
				})
			} else {
				this.checkElementHeight()
			}
		},
		checkElementHeight (event) {
			const { height } = this.$refs.elementContent.getBoundingClientRect()
			this.elementNotSelectable = height < 2
		}
	},
	watch: {
		'options' (newValue, oldValue) {
			let { '_styles': newMedia, '_advanced_options': newAdvanced, ...remainingNewProperties } = newValue
			let { '_styles': oldMedia, '_advanced_options': oldAdvanced, ...remainingOldProperties } = oldValue

			// Stringify the values so we can only compare values
			if (JSON.stringify(remainingNewProperties) !== JSON.stringify(remainingOldProperties)) {
				this.debouncedGetElementFromServer()
			}
		}
	},
	created () {
		this.getElementFromServer()
	},
	mounted () {
		this.$refs.elementContent.addEventListener('load', this.checkElementHeight)
	}
}
</script>

<style lang="scss">
.znpb__server-element {
	&--loading {
		min-height: 35px;
		opacity: .3;
	}

	&--empty {
		display: flex;
		justify-content: center;
		height: 70px;
		padding: 10px;
		background: $surface-variant;
	}

	&-loader--loading {
		position: absolute;
		top: 50%;
		left: 50%;
		width: 24px;
		height: 24px;
		margin: 0 auto;
		text-align: center;
		transform: translate(-50%, -50%);
		transition: none;

		&:before, &:after {
			@extend %loading;
			box-sizing: border-box;
			border: 2px solid $surface-variant;
		}

		&:after {
			border-right-color: lighten($surface-headings-color, 5%);
			animation: Rotate .6s linear infinite;
		}
	}
}
</style>
