<template>
	<div>
		<slot name="start" />

		<ul class="zb-el-tabs-nav">
			<TabLink
				v-for="{uid, title, active} in tabs"
				:key="uid"
				:title="title"
				:active="active"
			/>
		</ul>

		<div
			class="zb-el-tabs-content"
		>
			<Element
				v-for="(element, i) in element.content"
				:key="element.uid"
				:element="element"
				:class="{'zb-el-tabs-nav--active': i === 0}"
			/>
		</div>

		<slot name="end" />
	</div>
</template>

<script>
import { ref, computed, onBeforeUpdate } from 'vue'
import { useElements } from '@zb/editor'
import TabLink from './TabLink.vue'

export default {
	name: 'tabs',
	props: ['options', 'element', 'api'],
	components: {
		TabLink
	},
	setup (props) {
		// Check to see if we need to add some tabs
		if (props.element.content.length === 0 && props.options.tabs) {
			props.element.addChildren(props.options.tabs)
		}

		const tabs = computed(() => {
			return props.element.content.map((tabsElement, i) => {
				return {
					title: tabsElement.options.title,
					active: i === 0,
					uid: tabsElement.uid
				}

			})
		})

		return {
			tabs
		}
	}
}
</script>
