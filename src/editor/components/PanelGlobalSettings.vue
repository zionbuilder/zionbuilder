<template>
	<base-panel
		:panel-name="$translate('global_settings_panel')"
		panel-id="panel-global-settings"
		v-on:close-panel="closePanel('panel-global-settings')"
		class="znpb-general-options-panel-wrapper"
	>
		<div class="znpb-accordions-wrapper znpb-fancy-scrollbar">

			<OptionsForm
				:schema="schema"
				v-model="savedValues"
				:show-changes="false"
			/>

		</div>
	</base-panel>
</template>
<script>
import { mapGetters, mapActions } from 'vuex'
import OptionsForm from '@/editor/components/elementOptions/forms/OptionsForm.vue'

export default {
	name: 'PanelGlobalSettings',
	components: {
		OptionsForm
	},
	computed: {
		...mapGetters([
			'getPageSettings',
			'getPageSettingsSchema'
		]),
		savedValues: {
			get () {
				return this.getPageSettings
			},
			set (newValues) {
				this.updatePageSettings(newValues)
			}
		},
		filteredClasses () {
			if (this.keyword.length === 0) {
				return this.getClasses
			} else {
				return this.getClassesByFilter(this.keyword)
			}
		},
		schema () {
			const cssClasses = {
				'global_css': {
					type: 'accordion_menu',
					title: this.$translate('global_css_classes'),
					child_options: {
						'global_css_classes': {
							type: 'global_css_classes'
						}
					}
				}
			}
			return {
				...this.getPageSettingsSchema,
				...cssClasses
			}
		}
	},
	methods: {
		...mapActions([
			'closePanel',
			'updatePageSettings'
		])
	}
}
</script>
<style lang="scss">

.znpb-general-options-panel-wrapper {

	.znpb-panel__content_wrapper {
		position: relative;
		padding-top: 0;
	}

	.znpb-accordions-wrapper {
		display: flex;
		flex-grow: 1;
		flex-direction: column;
	}
}

</style>
