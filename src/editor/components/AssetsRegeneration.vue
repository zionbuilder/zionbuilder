<template>
	<div v-if="AssetsStore.isLoading" class="znpb-assetsRegenerationWrapper">
		{{ translate('regenerating_assets') }}
		<template v-if="AssetsStore.filesCount > 0">{{ AssetsStore.currentIndex }}/{{ AssetsStore.filesCount }} </template>
	</div>
</template>

<script lang="ts" setup>
// Common API
const { translate } = window.zb.i18n;
const { useAssetsStore } = window.zb.store;

const AssetsStore = useAssetsStore();
</script>

<style lang="scss">
.znpb-assetsRegenerationWrapper {
	position: fixed;
	right: 30px;
	bottom: 30px;
	background-color: var(--zb-surface-color);
	max-width: 230px;
	padding: 15px 25px;
	color: var(--zb-surface-text-color);
}
</style>
