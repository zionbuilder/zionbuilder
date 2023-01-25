<template>
	<div>
		<slot name="start" />

		<div ref="elementContentRef" :class="{ 'znpb__server-element--loading': loading }" />

		<div v-if="!loading && elementContent.length === 0" class="znpb__server-element--empty">
			<img :src="logoUrl" />
		</div>

		<div v-if="loading" class="znpb__server-element-loader--loading" />

		<slot name="end" />
	</div>
</template>

<script lang="ts" setup>
import { ref, computed, watch, nextTick, onBeforeUnmount, onMounted } from 'vue';

// Utils
import { debounce } from 'lodash-es';
import { ScriptsLoader } from '../modules/ScriptsLoader';

const { applyFilters, addAction, removeAction, doAction } = window.zb.hooks;

const props = withDefaults(
	defineProps<{ element: ZionElement; options: Record<string, unknown>; api: Record<string, unknown> }>(),
	{
		element: null,
		options: null,
		api: null,
	},
);

const logoUrl = window.ZnPbInitialData.urls.logo;
const elementContentRef = ref(null);
const elementContent = ref('');
const loading = ref(true);
const elementNotSelectable = ref(false);

const elementDataForRender = computed(() => {
	const elementOptions = props.element.elementDefinition.options;

	// Check for options that don't require a server render
	if (!elementOptions) {
		return {};
	}

	const { _styles: newMedia, _advanced_options: newAdvanced, ...remainingNewProperties } = props.options;
	const optionsThatRequireServerRequest = {};

	Object.keys(remainingNewProperties).forEach(optionID => {
		const optionSchema = elementOptions[optionID];
		if (typeof optionSchema !== 'undefined') {
			if (!optionSchema.css_style || optionSchema.rerender) {
				optionsThatRequireServerRequest[optionID] = remainingNewProperties[optionID];
			}
		} else {
			optionsThatRequireServerRequest[optionID] = remainingNewProperties[optionID];
		}
	});

	return JSON.stringify(optionsThatRequireServerRequest);
});

watch(elementDataForRender, (newValue, oldValue) => {
	if (newValue !== oldValue) {
		// Stringify the values so we can only compare values
		debouncedGetElementFromServer();
	}
});

function setInnerHTML(content) {
	const elm = elementContentRef.value;
	elm.innerHTML = content;

	Array.from(elm.querySelectorAll('script')).forEach(oldScript => {
		const newScript = document.createElement('script');
		Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
		newScript.appendChild(document.createTextNode(oldScript.innerHTML));
		oldScript.parentNode.replaceChild(newScript, oldScript);
	});

	elm.addEventListener('load', checkElementHeight);
}

const serverComponentRenderData = applyFilters('zionbuilder/server_component/data', {
	element_data: props.element,
});

function loadScripts(scripts) {
	const { loadScript } = ScriptsLoader(window.document.getElementById('znpb-editor-iframe').contentWindow);

	return new Promise((resolve, reject) => {
		Object.keys(scripts).map(scriptHandle => {
			// Script can sometimes be false
			const scriptConfig = scripts[scriptHandle];

			// Set the handle if it was not provided
			scriptConfig.handle = scriptConfig.handle ? scriptConfig.handle : scriptHandle;

			if (scriptConfig.src) {
				return loadScript(scriptConfig);
			}
		});

		resolve();
	});
}

function getElementFromServer() {
	loading.value = true;

	props.element.serverRequester.request(
		{
			type: 'render_element',
			config: serverComponentRenderData,
		},
		response => {
			// Send back the image
			elementContent.value = response.data.element;
			doAction('zionbuilder/server_component/before_rendered', elementContentRef.value, props.element, props.options);

			setInnerHTML(elementContent.value);

			// Set body classes
			setBodyClasses(response.data.body_classes);

			loadScripts(response.data.scripts).then(() => {
				loading.value = false;

				nextTick(() => {
					setTimeout(() => {
						checkForContentHeight();
						doAction('zionbuilder/server_component/rendered', elementContentRef.value, props.element, props.options);
					}, 20);
				});
			});
		},
		function (message) {
			loading.value = false;

			// eslint-disable-next-line
			console.log('server Request fail', message)
		},
	);
}

const debouncedGetElementFromServer = debounce(function () {
	getElementFromServer();
}, 500);

/**
 * Add body classes required by elements
 */
function setBodyClasses(classes: string[]) {
	const body = window.document.getElementById('znpb-editor-iframe')?.contentWindow?.document?.body;
	classes.forEach(cssClass => {
		body.classList.add(cssClass);
	});
}

function checkForContentHeight() {
	const loadableElements = elementContentRef.value.querySelectorAll('img, iframe, video');
	let loadableElementsCount = loadableElements.length;

	const loadCallback = () => {
		loadableElementsCount--;

		if (loadableElementsCount === 0) {
			checkElementHeight();
		}
	};

	if (loadableElementsCount > 0) {
		loadableElements.forEach(element => {
			element.addEventListener('load', loadCallback);
			element.addEventListener('error', loadCallback);
		});
	} else {
		checkElementHeight();
	}
}

function checkElementHeight() {
	const { height } = elementContentRef.value.getBoundingClientRect();

	elementNotSelectable.value = height < 2;
}

onMounted(() => {
	// Get the element from server on setup
	getElementFromServer();
});

addAction('zionbuilder/server_component/refresh', debouncedGetElementFromServer);
onBeforeUnmount(() => {
	removeAction('zionbuilder/server_component/refresh', debouncedGetElementFromServer);
});
</script>

<style lang="scss">
.znpb__server-element {
	&--loading {
		min-height: 35px;
		opacity: 0.3;
	}

	&--empty {
		display: flex;
		justify-content: center;
		height: 70px;
		padding: 10px;
		background: var(--zb-surface-lighter-color);
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

		&:before,
		&:after {
			content: '';
			position: absolute;
			top: 0;
			left: 0;
			box-sizing: border-box;
			width: 100%;
			height: 100%;
			border: 2px solid rgba(220, 220, 220, 0.2);
			border-radius: 50%;
		}

		&:after {
			border-right-color: #dedede;
			animation: Rotate 0.6s linear infinite;
		}
	}
}
</style>
