<template>
	<PageTemplate class="znpb-admin-colors__wrapper">
		<h3>{{ i18n.__('Color Presets', 'zionbuilder') }}</h3>

		<Tabs tab-style="minimal">
			<Tab name="Local">
				<Sortable v-model="computedLocalColors" class="znpb-admin-colors__container" :revert="true" axis="horizontal">
					<ColorBox
						v-for="(color, i) in computedLocalColors"
						:key="color + i"
						:color="color"
						@option-updated="editLocalColor(color, $event)"
						@delete-color="deleteLocalColor(color)"
					/>

					<template #end>
						<ColorBox type="addcolor" @option-updated="addLocalColor" />
					</template>
				</Sortable>
			</Tab>
			<Tab name="Global">
				<UpgradeToPro
					v-if="!isProActive"
					:message_title="i18n.__('Meet Global Colors', 'zionbuilder')"
					:message_description="
						i18n.__(
							'Global colors allows you to define a color that you can use in builder, and every time this color changes it will be updated automatically in all locations where it was used. ',
							'zionbuilder',
						)
					"
				/>

				<template v-else>
					<Sortable
						v-model="computedGlobalColors"
						class="znpb-admin-colors__container"
						:revert="true"
						axis="horizontal"
					>
						<ColorBox
							v-for="(color, i) in computedGlobalColors"
							:key="color.color + i"
							:color="color.color"
							@option-updated="editGlobalColor(i, $event)"
							@delete-color="deleteGlobalColor(color)"
						/>

						<template #end>
							<ColorBox type="addcolor" @option-updated="addGlobal" />
						</template>
					</Sortable>
				</template>
			</Tab>
		</Tabs>
		<template #right>
			<p class="znpb-admin-info-p">
				{{ i18n.__('Create your color pallette to use locally or globally', 'zionbuilder') }}
			</p>
		</template>
	</PageTemplate>
</template>

<script lang="ts" setup>
import * as i18n from '@wordpress/i18n';
import { computed } from 'vue';
import { generateUID } from '@zb/utils';
import { Sortable } from '@zb/components';
import { useBuilderOptionsStore, useEnvironmentStore } from '@zb/store';

// Components
import ColorBox from './ColorBox.vue';

const { isProActive } = useEnvironmentStore();
const {
	addLocalColor,
	getOptionValue,
	deleteLocalColor,
	editLocalColor,
	addGlobalColor,
	deleteGlobalColor,
	editGlobalColor,
	updateOptionValue,
} = useBuilderOptionsStore();

const computedLocalColors = computed({
	get() {
		return getOptionValue('local_colors');
	},
	set(newValue) {
		updateOptionValue('local_colors', newValue);
	},
});
const computedGlobalColors = computed({
	get() {
		return getOptionValue('global_colors');
	},
	set(newValue) {
		updateOptionValue('global_colors', newValue);
	},
});

function addGlobal(color: string) {
	const colorId = generateUID();
	const globalColor = {
		id: colorId,
		color: color,
		name: colorId,
	};

	addGlobalColor(globalColor);
}
</script>
<style lang="scss">
.znpb-admin-colors__wrapper {
	.znpb-tabs__header {
		margin: 0 auto;
		&-item {
			padding: 15px 20px 30px 0;

			&--active,
			&:hover {
				color: var(--zb-primary-color);
			}
		}
	}
}
</style>
