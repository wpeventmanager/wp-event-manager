import vcCake from 'vc-cake'
import singleEventListingElement from './component'

const vcvAddElement = vcCake.getService('cook').add
/**
 * 
 * Add element component and json file
 *
 * @since 3.1.8
 * 
 */
vcvAddElement(
  require('./settings.json'),

  // Component callback
  function (component) {
    component.add(singleEventListingElement)
  }
)
