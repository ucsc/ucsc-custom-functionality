/**
 * @module magazineBlock
 */


const el = {
	blocks: null,
}

/**
 * Get a block's tab elements.
 * 
 * @param {HTMLElement} block 
 */
const getBlockTabs = (block) => {
  return Array.from(block.querySelectorAll('[role="tab"]'))
}

/**
 * Get tab's panel element.
 * 
 * @param {HTMLButtonElement} tab 
 * @param {HTMLElement} block 
 */
const getTabPanel = (tab, block) => {
  return document.getElementById(tab.getAttribute('aria-controls'))
}

/**
 * Get the tab that is offset from the given tab by the given amount.	Loop at
 * the beginning and end if necessary.
 * 
 * @param {HTMLButtonElement} fromTab
 * @param {HTMLButtonElement[]} tabs
 * @param {number} offset
 * @return {HTMLButtonElement}
 */
const getRelativeTab = ( fromTab, tabs, offset ) => {
	const currentIndex = tabs.indexOf( fromTab )
	let newIndex = ( currentIndex + offset ) % tabs.length
	if ( newIndex < 0 ) {
		newIndex = tabs.length + newIndex
	}
	return tabs[ newIndex ]
}

/**
 * Make the given tab the active one.
 * 
 * @param {HTMLButtonElement} tab 
 * @param {HTMLElement} block 
 */
const activateTab = (tab, block) => {
  // Deactivate other tabs.
  for (const otherTab of getBlockTabs(block)) {
    if (otherTab === tab) {
      continue
    }
    
    otherTab.tabIndex = -1
    otherTab.setAttribute('aria-selected', 'false')
    const panel = getTabPanel(otherTab, block)
    if (panel) {
      panel.setAttribute('aria-hidden', 'true')
      panel.setAttribute('inert', '')
    }
  }

  // Activate this tab.
  tab.tabIndex = 0
  tab.setAttribute('aria-selected', 'true')
  tab.focus()
  const panel = getTabPanel(tab, block)
  if (panel) {
    panel.setAttribute('aria-hidden', 'false')
    panel.removeAttribute('inert')
  }

  // Scroll to the current tab (only affects mobile).
  const tabList = tab.closest('[role="tablist"]')
  if (tabList) {
    tabList.scrollTo({
      left: tab.offsetLeft,
      behavior: 'smooth',
    })
  }
}

/**
 * Callback for when the keydown event on a tab toggle.
 * 
 * @param {HTMLButtonElement} tab
 * @param {HTMLElement} block
 * @param {KeyboardEvent} event
 */
const onTabKeydown = ( tab, block, event ) => {
  const allTabs = getBlockTabs(block)
  if (!allTabs.length) {
    return
  }

	let newTab = null
	switch ( event.key ) {
		case 'ArrowUp':
			newTab = getRelativeTab( tab, allTabs, -1 )
			break
		case 'ArrowDown':
			newTab = getRelativeTab( tab, allTabs, 1 )
			break
		case 'Home':
			newTab = allTabs[ 0 ]
			break
		case 'End':
			newTab = allTabs[ allTabs.length - 1 ]
			break
	}

	if ( newTab ) {
    activateTab(newTab, block)
		event.stopPropagation()
		event.preventDefault()
	}
}

const bindEvents = () => {
  for (const block of el.blocks) {
    for (const tab of getBlockTabs(block)) {
      // Toggle tabs on click.
      tab.addEventListener('click', () => {
        activateTab(tab, block)
      })

      // Provide keyboard support.
      tab.addEventListener( 'keydown', event => {
				onTabKeydown( tab, block, event )
			} )
    }
  }
}

const cacheElements = () => {
	el.blocks = Array.from(document.querySelectorAll( '.ucsc-magazine-block' ))
}

const init = () => {
	cacheElements()
	bindEvents()

  // Activate the first tab in each block.
  for (const block of el.blocks) {
    const tabs = getBlockTabs(block)
    if (tabs.length > 0) {
      activateTab(tabs[0], block)
    }
  }
}

document.addEventListener( 'DOMContentLoaded', init )