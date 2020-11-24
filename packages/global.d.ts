type CallbackFunction = (...args: any[]) => void
interface Hook {
	[key: string]: CallbackFunction[]
}

// l10n
interface translateString {
	[key: string]: string
}