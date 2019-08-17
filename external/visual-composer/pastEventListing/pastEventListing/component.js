import React from 'react'
import vcCake from 'vc-cake'
const vcvAPI = vcCake.getService('api')
const renderProcessor = vcCake.getService('renderProcessor')
import { renderToString } from 'react-dom/server'

/**
 * 
 * Visual composer element for all past event listing.
 *
 * @since 3.1.8
 * 
 */
export default class pastEventListingElement extends vcvAPI.elementComponent {
  constructor(props) {
    super(props);
  }
  componentDidMount () {
    super.updateShortcodeToHtml(`[past_events]`, this.ref)
  }
  componentDidUpdate (prevProps) {    
    super.updateShortcodeToHtml(`[past_events]`, this.ref)    
  }
  render () {
    let { id, atts, editor } = this.props
    let { customClass, metaCustomId } = atts // destructuring assignment for attributes from settings.json with access public
    let textBlockClasses = 'vce-text-test-block'
    let wrapperClasses = 'vce-text-block-test-wrapper vce'
    let customProps = {}
    if (typeof customClass === 'string' && customClass) {
      textBlockClasses = textBlockClasses.concat(' ' + customClass)
    }

    if (metaCustomId) {
      customProps.id = metaCustomId
    }

    let doAll = this.applyDO('all')

    return <div className={textBlockClasses} {...editor} {...customProps}>
              <div className={wrapperClasses} id={'el-' + id} {...doAll}>
                <div className='vcvhelper' ref={(ref) => { this.ref = ref }} data-vcvs-html={`[past_events]`} />
              </div>
            </div>
  }
}
