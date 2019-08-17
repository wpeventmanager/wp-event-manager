import React from 'react'
import vcCake from 'vc-cake'
const vcvAPI = vcCake.getService('api')
const renderProcessor = vcCake.getService('renderProcessor')
import { renderToString } from 'react-dom/server'

/**
 * 
 * Visual composer element for event dashboard.
 *
 * @since 3.1.8
 * 
 */
export default class eventDashboardElement extends vcvAPI.elementComponent {
  constructor(props) {
    super(props);
    this.posts_per_page = '';
  }
  componentDidMount () {
    super.updateShortcodeToHtml(`[event_dashboard]`, this.ref)
  }
  componentDidUpdate (prevProps) {
    const { posts_per_page } = this.props.atts
    if (posts_per_page && posts_per_page !== '0' && posts_per_page !== prevProps.atts.posts_per_page) 
      this.posts_per_page = 'posts_per_page='+posts_per_page    
    
    super.updateShortcodeToHtml(`[event_dashboard ${this.posts_per_page}]`, this.ref)
    
  }
  render () {

    let { id, atts, editor } = this.props
    let { customClass, metaCustomId } = atts 
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
                <div className='vcvhelper' ref={(ref) => { this.ref = ref }} data-vcvs-html={`[event_dashboard ${this.posts_per_page}]`} />
              </div>
            </div>
  }
}
