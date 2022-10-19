<template>
	<div class="znpb-element--not-found">
		<slot name="start" />

		{{ translate('element_not_found') }}

		<slot name="end" />
	</div>
</template>

<script lang="ts" setup>
import { translate } from '/@/common/modules/i18n';
</script>

<style lang="scss">
.znpb-element--not-found {
	padding: 20px;
	color: #fff;
	text-align: center;
	background: #e84655;
}
</style>
