// Utils
import AccordionMenu from './AccordionMenu'
import PseudoGroup from './PseudoGroup'
import Background from './Background'
import BackgroundColor from './BackgroundColor'
import BackgroundGradient from './BackgroundGradient'
import Typography from './Typography'
import Group from './Group'
import PanelAccordion from './PanelAccordion'
import ResponsiveGroup from './ResponsiveGroup'
import Link from './Link'
import ColumnSize from './ColumnSize'
import WPWidget from './WPWidget'
import Repeater from './Repeater'
import TabGroup from './TabGroup'
import ElementStyles from './ElementStyles'
import CSSSelector from './CSSSelector'
import Gallery from './Gallery'
import HTMLComponent from './HTML'
import UpgradeToPro from './UpgradeToPro'
// Custom options just for buidler
import GlobalClasses from './GlobalCssClasses'
import ChildAdder from './ChildAdder'
import Dimensions from './Dimensions'
import ElementEventButton from './ElementEventButton'

import {
	useOptions
} from '@zb/components'

export const registerEditorOptions = () => {
	const {
		registerOption
	} = useOptions()

	// register custom options
	registerOption(AccordionMenu)
	registerOption(PseudoGroup)
	registerOption(Background)
	registerOption(BackgroundColor)
	registerOption(BackgroundGradient)
	registerOption(Typography)
	registerOption(Group)
	registerOption(PanelAccordion)
	registerOption(ResponsiveGroup)
	registerOption(Link)
	registerOption(ColumnSize)
	registerOption(WPWidget)
	registerOption(Repeater)
	registerOption(TabGroup)
	registerOption(ElementStyles)
	registerOption(CSSSelector)
	registerOption(Gallery)
	registerOption(HTMLComponent)
	registerOption(UpgradeToPro)
	registerOption(Dimensions)

	// options just for builder
	registerOption(GlobalClasses)
	registerOption(ChildAdder)
	registerOption(ElementEventButton)
}
