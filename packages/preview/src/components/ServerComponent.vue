<template>
	<div>
		<slot name="start" />

		<div
			:class="{'znpb__server-element--loading': loading}"
			ref="elementContentRef"
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
import { ref, computed, watch, nextTick, inject } from 'vue'
import { applyFilters } from '@zb/hooks'

// Utils
import { debounce } from '@zb/utils'
import { useEditorData, serverRequest } from '@zb/editor'

export default {
	name: 'ServerComponent',

	props: {
		element: Object,
		options: {
			type: Object
		},
		api: {
			type: Object
		}
	},
	setup (props) {
		const { editorData } = useEditorData()
		const contentModel = props.element.elementTypeModel
		const logoUrl = editorData.value.urls.logo
		const elementContentRef = ref(null)
		const elementContent = ref('')
		const loading = ref(true)
		const elementNotSelectable = ref(false)

		const requiresDataForRender = computed(() => {
			const elementModel = contentModel
			const { _styles, _advanced_options: advancedOptions, ...options } = props.options
			return elementModel.requires_data_for_render && Object.keys(options).length === 0
		})

		watch(() => props.options, (newValue, oldValue) => {
			let { '_styles': newMedia, '_advanced_options': newAdvanced, ...remainingNewProperties } = newValue
			let { '_styles': oldMedia, '_advanced_options': oldAdvanced, ...remainingOldProperties } = oldValue

			// Stringify the values so we can only compare values
			if (JSON.stringify(remainingNewProperties) !== JSON.stringify(remainingOldProperties)) {
				debouncedGetElementFromServer()
			}
		}, {
			deep: true
		})

		function setInnerHTML (content) {
			const elm = elementContentRef.value
			elm.innerHTML = content;
			Array.from(elm.querySelectorAll("script")).forEach(oldScript => {
				const newScript = document.createElement("script");
				Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
				newScript.appendChild(document.createTextNode(oldScript.innerHTML));
				oldScript.parentNode.replaceChild(newScript, oldScript);
			});

			elm.addEventListener('load', checkElementHeight)
		}

		const serverComponentRenderData = applyFilters('zionbuilder/server_component/data', {
			element_data: props.element
		})

		function getElementFromServer () {
			loading.value = true

			serverRequest.request({
				type: 'render_element',
				config: serverComponentRenderData
			}, (response) => {
				// Send back the image
				elementContent.value = response.data.element
				setInnerHTML(response.data.element)
				loading.value = false

				nextTick(() => {
					checkForContentHeight()
				})
			}, function (message) {
				loading.value = false
				// eslint-disable-next-line
				console.log('server Request fail', message)
			})
		}

		const debouncedGetElementFromServer = debounce(function () {
			getElementFromServer()
		}, 500)


		function checkForContentHeight () {
			const loadableElements = elementContentRef.value.querySelectorAll('img, iframe, video')
			let loadableElementsCount = loadableElements.length

			const loadCallback = () => {
				loadableElementsCount--

				if (loadableElementsCount === 0) {
					checkElementHeight()
				}
			}

			if (loadableElementsCount > 0) {
				loadableElements.forEach(element => {
					element.addEventListener('load', loadCallback)
					element.addEventListener('error', loadCallback)
				})
			} else {
				checkElementHeight()
			}
		}

		function checkElementHeight () {
			const { height } = elementContentRef.value.getBoundingClientRect()

			elementNotSelectable.value = height < 2
		}

		// Get the element from server on setup
		getElementFromServer()

		return {
			contentModel,
			logoUrl,
			elementContentRef,
			setInnerHTML,
			requiresDataForRender,
			elementContent,
			loading,
			elementNotSelectable
		}
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
