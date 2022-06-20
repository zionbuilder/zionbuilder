<template>
	<SortableContent :element="element" :tag="htmlTag" v-bind="extraAttributes">
		<template #start>
			<slot name="start" />
		</template>

		<template #end>
			<slot name="end" />
		</template>
	</SortableContent>
</template>

<script>
import { getLinkAttributes } from '@zb/utils';

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
			return getLinkAttributes(this.options.link);
		},
	},
};
</script>
