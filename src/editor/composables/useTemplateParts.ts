import { ref, Ref } from 'vue';
import { TemplatePartConfig, TemplatePart } from '../models';
import { useEditorData } from './useEditorData';

// Global template parts store
const templateParts: Ref<{ [key: string]: TemplatePart }> = ref({});

export function useTemplateParts() {
	const registerTemplatePart = (areaConfig: TemplatePartConfig): TemplatePart => {
		const templatePartInstance = new TemplatePart(areaConfig);
		templateParts.value[areaConfig.id] = templatePartInstance;

		return templatePartInstance;
	};

	const getTemplatePart = (id: string): TemplatePartConfig => {
		return templateParts.value[id];
	};

	const getActivePostTemplatePart = (): TemplatePartConfig => {
		const { editorData } = useEditorData();
		return getTemplatePart(editorData.value.page_id);
	};

	return {
		templateParts,
		registerTemplatePart,
		getTemplatePart,
		getActivePostTemplatePart,
	};
}
