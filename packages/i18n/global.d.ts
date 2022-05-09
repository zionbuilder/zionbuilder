declare module '@vue/runtime-core' {
	interface ComponentCustomProperties {
		$translate: typeof import('./')['translate'];
	}
}
