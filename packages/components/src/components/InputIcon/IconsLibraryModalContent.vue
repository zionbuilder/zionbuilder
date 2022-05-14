<template>
	<div class="znpb-icon-pack-modal" :class="{ ['znpb-icon-pack-modal--has-special-filter']: specialFilterPack }">
		<div class="znpb-icon-pack-modal__search">
			<InputSelect
				:modelValue="activeCategory"
				:options="packsOptions"
				class="znpb-icons-category-select"
				placement="bottom-start"
				@update:modelValue="activeCategory = $event"
			/>
			<BaseInput v-model="searchModel" :placeholder="getPlaceholder" :clearable="true" icon="search" />
		</div>
		<div class="znpb-icon-pack-modal-scroll znpb-fancy-scrollbar">
			<IconPackGrid
				v-for="(pack, i) in filteredList"
				:key="i"
				:icon-list="pack.icons"
				:family="pack.name"
				:active-icon="modelValue?.name"
				:active-family="modelValue?.family"
				@icon-selected="selectIcon($event, pack.name)"
				@update:modelValue="insertIcon($event, pack.name)"
			/>
		</div>
	</div>
</template>

<script lang="ts">
export default {
	name: 'IconsLibraryModalContent',
};
</script>

<script lang="ts" setup>
import { ref, computed, Ref } from 'vue';
import { useDataSets, DataSets, Icons } from '@composables';
import IconPackGrid from './IconPackGrid.vue';
import { translate } from '@zb/i18n';

type Icon = { family?: string; name: string; unicode: string };

const props = defineProps<{
	modelValue?: Icon | null;
	specialFilterPack?: Icons[];
}>();

const emit = defineEmits<{
	(e: 'update:modelValue', value: Icon | null | undefined): void;
	(e: 'selected', value: Icon): void;
}>();

const { dataSets }: { dataSets: Ref<DataSets> } = useDataSets();

const keyword = ref('');
const activeIcon: Ref<Icon | null> = ref(null);
const activeCategory = ref('all');

const getPacks = computed(() => {
	return dataSets.value.icons ?? [];
});

const searchModel = computed({
	get() {
		return keyword.value;
	},
	set(newVal: string) {
		keyword.value = newVal;
	},
});

const filteredList = computed(() => {
	if (keyword.value.length > 0) {
		const filtered = [];
		for (const pack of packList.value) {
			const copyPack = { ...pack };
			const b = copyPack.icons.filter(icon => icon.name.includes(keyword.value.toLowerCase()));
			copyPack.icons = [...b];
			filtered.push(copyPack);
		}
		return filtered;
	} else return packList.value;
});

const getPlaceholder = computed(() => {
	return `${translate('search_for_icons')} ${getIconNumber.value} ${translate('icons')}`;
});

const getIconNumber = computed(() => {
	let iconNumber = 0;
	for (const pack of packList.value) {
		let packNumber = pack.icons.length;
		iconNumber = iconNumber + packNumber;
	}
	return iconNumber;
});

const packList = computed(() => {
	if (props.specialFilterPack !== undefined && props.specialFilterPack.length) {
		return props.specialFilterPack;
	}
	if (activeCategory.value === 'all') {
		return getPacks.value;
	} else {
		let newArray = [];
		for (const pack of getPacks.value) {
			if (pack.id === activeCategory.value) {
				newArray.push(pack);
			}
		}
		return newArray;
	}
});

const packsOptions = computed(() => {
	const options = [
		{
			name: 'All',
			id: 'all',
		},
	];
	if (props.specialFilterPack === undefined || !props.specialFilterPack.length) {
		getPacks.value.forEach(pack => {
			let a = {
				name: pack.name,
				id: pack.id,
			};
			options.push(a);
		});
	}

	return options;
});

function selectIcon(event: Icon, name: string) {
	activeIcon.value = event;
	const icon = {
		family: name,
		name: activeIcon.value.name,
		unicode: activeIcon.value.unicode,
	};
	emit('update:modelValue', icon);
}

function insertIcon(event: Icon, name: string) {
	activeIcon.value = event;
	const icon = {
		family: name,
		name: activeIcon.value.name,
		unicode: activeIcon.value.unicode,
	};
	emit('selected', icon);
}
</script>
<style lang="scss">
.znpb-icon-pack-modal {
	display: flex;
	flex-direction: column;
	min-height: 500px;
	max-height: 470px;
	margin: 10px;

	&__search {
		display: flex;
		padding: 10px 10px 0;
		margin-bottom: 20px;

		.znpb-baseselect-overwrite {
			margin-right: 10px;
		}

		& > .znpb-editor-icon-wrapper {
			padding: 0 14px;
			cursor: pointer;
		}
	}
	&--has-special-filter {
		.znpb-icon-pack-modal__icons {
			min-height: 500px;
		}
	}
}
.znpb-icons-category-select {
	ul.znpb-baseselect-list {
		padding: 8px 0;
	}

	.znpb-baseselect-list__option {
		white-space: nowrap;
	}
}
</style>
