<template>
	<SortableContent class="zb-column" :element="element" :tag="htmlTag" v-bind="extraAttributes">
		<template #start>
			<slot name="start" />
			<SvgMask
				v-if="topMask !== undefined && topMask.shape"
				:shape-path="topMask['shape']"
				:color="topMask['color']"
				:flip="topMask['flip']"
				position="top"
			/>

			<SvgMask
				v-if="bottomMask !== undefined && bottomMask.shape"
				:shape-path="bottomMask['shape']"
				:color="bottomMask['color']"
				:flip="bottomMask['flip']"
				position="bottom"
			/>
		</template>

		<template #end>
			<slot name="end" />
		</template>
	</SortableContent>
</template>

<script>
export default {
	name: 'ZionColumn',
	props: ['options', 'api', 'element'],
	computed: {
		htmlTag() {
			if (this.options.link && this.options.link.link) {
				return 'a';
			}

			return /^[a-z0-9]+$/i.test(this.options.tag) ? this.options.tag : 'div';
		},
		extraAttributes() {
			return window.zb.utils.getLinkAttributes(this.options.link);
		},
		topMask() {
			return this.shapes['top'];
		},
		bottomMask() {
			return this.shapes['bottom'];
		},
		shapes() {
			return this.options.shapes || {};
		},
	},
};
</script>
