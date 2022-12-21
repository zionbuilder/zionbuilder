<template>
	<div class="znpb-element--not-found">
		<slot name="start" />

		{{ __('element not found', 'zionbuilder') }}

		<slot name="end" />
	</div>
</template>

<script lang="ts" setup>
import { __ } from '@wordpress/i18n';
</script>

<style lang="scss">
.znpb-element--not-found {
	padding: 20px;
	color: #fff;
	text-align: center;
	background: #e84655;
}
</style>
