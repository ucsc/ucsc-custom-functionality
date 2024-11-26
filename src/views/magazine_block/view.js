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
  return block.querySelectorAll('[role="tab"]')
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
 * Make the given tab the active one.
 * 
 * @param {HTMLButtonElement} tab 
 * @param {HTMLElement} block 
 */
const activateTab = (tab, block) => {
  // Deactivate other tabs.
  getBlockTabs(block).forEach(otherTab => {
    if (otherTab === tab) {
      return
    }
    
    otherTab.setAttribute('aria-selected', 'false')
    const panel = getTabPanel(otherTab, block)
    if (panel) {
      panel.setAttribute('inert', '')
    }
  })

  // Activate this tab.
  tab.setAttribute('aria-selected', 'true')
  const panel = getTabPanel(tab, block)
  if (panel) {
    panel.removeAttribute('inert')
  }
}

const bindEvents = () => {
  el.blocks.forEach(block => {
    getBlockTabs(block).forEach(tab => {
      // Toggle tabs on click.
      tab.addEventListener('click', () => {
        activateTab(tab, block)
      })
    })
  })
}

const cacheElements = () => {
	el.blocks = document.querySelectorAll( '.ucsc-magazine-block' )
}

const init = () => {
	cacheElements()
	bindEvents()

  // Activate the first tab in each block.
  el.blocks.forEach(block => {
    const tabs = getBlockTabs(block)
    if (tabs.length > 0) {
      activateTab(tabs[0], block)
    }
  })
}

document.addEventListener( 'DOMContentLoaded', init )