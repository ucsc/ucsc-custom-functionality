/**
 * @module pressInquiriesBlock
 */

import './style.scss';
import './editor.scss';

const el = {
	blocks: null,
}

/**
 * Callback for when the toggle button is actioned.
 * 
 * @param {HTMLButtonElement} button
 * @param {HTMLElement} block
 */
const onToggleClick = ( button, block ) => {
  const content = block.querySelector( '.ucsc-press-inquiries-block__container' )
  if (!content) {
    return
  }

  if (button.getAttribute('aria-expanded') === 'true') {
    button.setAttribute('aria-expanded', 'false')
    content.setAttribute('inert', '')
  }
  else {
    button.setAttribute('aria-expanded', 'true')
    content.removeAttribute('inert')
  }
}

const bindEvents = () => {
  for (const block of el.blocks) {
    const button = block.querySelector('button[aria-controls]')
    if (button) {
      button.addEventListener('click', () => {
        onToggleClick(button, block)
      })
    }
  }
}

const cacheElements = () => {
	el.blocks = Array.from(document.querySelectorAll( '.ucsc-press-inquiries-block' ))
}

const init = () => {
	cacheElements()
	bindEvents()
}

document.addEventListener( 'DOMContentLoaded', init )