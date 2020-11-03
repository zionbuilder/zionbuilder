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
				v-for="(elementUid, i) in data.content"
				:key="elementUid"
				:uid="elementUid"
				:element="getElement(elementUid)"
				:data="getElement(elementUid)"
				:parentUid="data.uid"
				:class="{'zb-el-tabs-nav--active': i === 0}"
				ref="tabs"
			/>
		</div>

		<slot name="end" />
	</div>
</template>

<script>
import { useElements } from '@zb/editor'
import TabLink from './TabLink.vue'

export default {
	name: 'tabs',
	props: ['options', 'data', 'api'],
	components: {
		TabLink
	},
	setup () {
		const { getElement } = useElements()

		return {
			getElement
		}
	},
	data () {
		return {
			mounted: 0
		}
	},
	computed: {
		tabs () {
			let i = 0

			if (this.mounted === 0) {
				return []
			}

			const tabsContent = this.data.content
			const items = this.$refs.tabs || []

			return items.map(tab => {
				i++

				return {
					title: tab.options.title,
					active: i === 1,
					data: tab.data,
					uid: tab.data.uid
				}
			})
		}
	},
	watch: {
		'data.content': {
			handler: function () {
				this.$nextTick(() => {
					this.mounted++
				})
			},
			immediate: true
		}
	}
}
</script>
