<template>
	<span v-if="iconConfig" :data-znpbiconfam="iconConfig.family" :data-znpbicon="iconUnicode" />
</template>

<script lang="ts" setup>
import { computed } from 'vue';

const props = withDefaults(
	defineProps<{
		iconConfig: {
			family: string;
			unicode: string;
			name: string;
		};
	}>(),
	{
		iconConfig: () => {
			return {
				family: 'Font Awesome 5 Brands Regular',
				name: 'wordpress-simple',
				unicode: 'uf411',
			};
		},
	},
);

const iconUnicode = computed(() => {
	return JSON.parse(`"\\${props.iconConfig.unicode}"`).trim();
});
</script>
