<template>
	<div>
		<slot name="start" />

		<ul class="zb-el-tabs-nav">
			<TabLink
				v-for="(tab, i) in tabs"
				:key="tab.uid"
				:title="tab.title"
				:active="activeTab ? tab.uid === activeTab : i === 0"
				v-bind="api.getAttributesForTag('inner_content_styles_title')"
				:class="api.getStyleClasses('inner_content_styles_title')"
				@click="activeTab = tab.uid"
			/>
		</ul>

		<div
			class="zb-el-tabs-content"
			v-bind="api.getAttributesForTag('inner_content_styles_content')"
			:class="api.getStyleClasses('inner_content_styles_content')"
		>
			<Element
				v-for="(childElement, i) in children"
				:key="childElement.uid"
				:element="childElement"
				:class="{ 'zb-el-tabs-nav--active': activeTab ? childElement.uid === activeTab : i === 0 }"
			/>
		</div>

		<slot name="end" />
	</div>
</template>

<script>
import { ref, computed } from 'vue';
import TabLink from './TabLink.vue';

export default {
	name: 'Tabs',
	components: {
		TabLink,
	},
	props: ['options', 'element', 'api'],
	setup(props) {
		const activeTab = ref(null);

		// Check to see if we need to add some tabs
		if (props.element.content.length === 0 && props.options.tabs) {
			props.element.addChildren(props.options.tabs);
		}

		const children = computed(() => {
			return props.element.content.map(childUID => {
				const contentStore = window.zb.editor.useContentStore();
				return contentStore.getElement(childUID);
			});
		});

		const tabs = computed(() => {
			return props.element.content.map(childUID => {
				const contentStore = window.zb.editor.useContentStore();
				const element = contentStore.getElement(childUID);
				return {
					title: element.options.title,
					uid: element.uid,
				};
			});
		});

		return {
			tabs,
			activeTab,
			children,
		};
	},
};
</script>
