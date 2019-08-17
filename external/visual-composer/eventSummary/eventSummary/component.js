import React from 'react'
import vcCake from 'vc-cake'
const vcvAPI = vcCake.getService('api')
const renderProcessor = vcCake.getService('renderProcessor')
import { renderToString } from 'react-dom/server'


/**
 * 
 * Visual composer element for event summary.
 *
 * @since 3.1.8
 * 
 */
export default class eventSummaryElement extends vcvAPI.elementComponent {
  constructor(props) {
    super(props);
    this.eventid = '';
    this.width = '';
    this.align = '';
    this.featured = '';
    this.limit = '';
  }
  componentDidMount () {
    super.updateShortcodeToHtml(`[event_summary]`, this.ref)
  }
  componentDidUpdate (prevProps) {
    const { eventid , width, align, featured, limit} = this.props.atts
    if (eventid && eventid !== '' && eventid !== prevProps.atts.eventid) 
      this.eventid = 'id='+eventid    
    
    if (width && width !== '' && width !== prevProps.atts.width) 
      this.width = 'width='+width
    
    if (align  && align !== prevProps.atts.align) 
      this.align = 'align='+align
    
    if (featured && featured!=='' && featured !== prevProps.atts.featured) 
      this.featured = 'featured='+featured
    
    if (limit && limit !== '0' && limit !== prevProps.atts.limit) 
      this.limit = 'limit='+limit
    
    super.updateShortcodeToHtml(`[event_summary ${this.eventid} ${this.width} ${this.align} ${this.featured} ${this.limit}]`, this.ref)
    
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
              <div className='vcvhelper' ref={(ref) => { this.ref = ref }} data-vcvs-html={`[event_summary ${this.eventid} ${this.width} ${this.align} ${this.featured} ${this.limit}]`} />
            </div>
          </div>
  }
}
