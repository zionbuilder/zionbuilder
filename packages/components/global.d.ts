// GlobalComponents for Volar
import * as components from '@zionbuilder/components';
declare module 'vue' {
	export interface GlobalComponents {
		ListScroll: typeof components.ListScroll;
		Button: typeof components.Button;
		UpgradeToPro: typeof components.UpgradeToPro;
		Label: typeof components.Label;
		EmptyList: typeof components.EmptyList;
		Menu: typeof components.Menu;
		HiddenMenu: typeof components.HiddenMenu;

		// General
		Modal: typeof components.Modal;
		ModalConfirm: typeof components.ModalConfirm;
		ModalTemplateSaveButton: typeof components.ModalTemplateSaveButton;
		Icon: typeof components.Icon;
		Tooltip: typeof components.Tooltip;
		Loader: typeof components.Loader;
		Accordion: typeof components.Accordion;
		Notice: typeof components.Notice;
		Tabs: typeof components.Tabs;
		Tab: typeof components.Tab;
		ColorPicker: typeof components.ColorPicker;
		Color: typeof components.Color;
		Injection: typeof components.Injection;
		InputRadioImage: typeof components.InputRadioImage;

		// Specific
		ActionsOverlay: typeof components.ActionsOverlay;
		GradientPreview: typeof components.GradientPreview;
		GradientGenerator: typeof components.GradientGenerator;
		GradientLibrary: typeof components.GradientLibrary;
		HorizontalAccordion: typeof components.HorizontalAccordion;
		IconPackGrid: typeof components.IconPackGrid;
		// Forms
		Sortable: typeof components.Sortable;
		ChangesBullet: typeof components.ChangesBullet;

		// Inputs
		InputBackgroundImage: typeof components.InputBackgroundImage;
		InputBackgroundVideo: typeof components.InputBackgroundVideo;
		InputCheckboxGroup: typeof components.InputCheckboxGroup;
		InputCheckbox: typeof components.InputCheckbox;
		InputCheckboxSwitch: typeof components.InputCheckboxSwitch;
		InputLabel: typeof components.InputLabel;
		InputCode: typeof components.InputCode;
		InputRadio: typeof components.InputRadio;
		InputRadioGroup: typeof components.InputRadioGroup;
		InputRadioIcon: typeof components.InputRadioIcon;
		InputDatePicker: typeof components.InputDatePicker;
		InputColorPicker: typeof components.InputColorPicker;
		InputCustomSelector: typeof components.InputCustomSelector;
		InputShapeDividers: typeof components.InputShapeDividers;
		ShapeDividerComponent: typeof components.ShapeDividerComponent;
		SvgMask: typeof components.SvgMask;
		InputBorderControl: typeof components.InputBorderControl;
		InputBorderTabs: typeof components.InputBorderTabs;
		InputBorderRadius: typeof components.InputBorderRadius;
		InputBorderRadiusTabs: typeof components.InputBorderRadiusTabs;
		InputMedia: typeof components.InputMedia;
		InputFile: typeof components.InputFile;
		InputImage: typeof components.InputImage;
		InputEditor: typeof components.InputEditor;
		BaseInput: typeof components.BaseInput;
		InputWrapper: typeof components.InputWrapper;
		InputSelect: typeof components.InputSelect;
		InputRange: typeof components.InputRange;
		InputRangeDynamic: typeof components.InputRangeDynamic;
		InputTextShadow: typeof components.InputTextShadow;
		InputNumber: typeof components.InputNumber;
		InputNumberUnit: typeof components.InputNumberUnit;
		InputTextAlign: typeof components.InputTextAlign;
		OptionsForm: typeof components.OptionsForm;
		OptionWrapper: typeof components.OptionWrapper;
		InputSpacing: typeof components.InputSpacing;
		InputRepeater: typeof components.InputRepeater;
	}
}
