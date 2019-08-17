import React from 'react'
import vcCake from 'vc-cake'
const vcvAPI = vcCake.getService('api')
const renderProcessor = vcCake.getService('renderProcessor')
import { renderToString } from 'react-dom/server'

/**
 * 
 * Visual composer element for single event listing.
 *
 * @since 3.1.8
 * 
 */
export default class singleEventListingElement extends vcvAPI.elementComponent {
  constructor(props) {
    super(props);
    this.eventid = '';
  }
  componentDidMount () {
    super.updateShortcodeToHtml(`[event]`, this.ref)
  }
  componentDidUpdate (prevProps) {
    const { eventid } = this.props.atts
    if (eventid && eventid !== '' && eventid !== prevProps.atts.eventid) 
      this.eventid = 'id='+eventid    
    
    super.updateShortcodeToHtml(`[event ${this.eventid}]`, this.ref)
    
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
                <div className='vcvhelper' ref={(ref) => { this.ref = ref }} data-vcvs-html={`[event ${this.eventid}]`} />
              </div>
            </div>
  }
}
