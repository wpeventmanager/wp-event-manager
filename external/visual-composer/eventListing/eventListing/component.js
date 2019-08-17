import React from 'react'
import vcCake from 'vc-cake'
const vcvAPI = vcCake.getService('api')
const renderProcessor = vcCake.getService('renderProcessor')
import { renderToString } from 'react-dom/server'

/**
 * 
 * Visual composer element for event listing.
 *
 * @since 3.1.8
 * 
 */
export default class eventListingElement extends vcvAPI.elementComponent {
  constructor(props) {
    super(props);
    this.cancelled = '';
    this.featured = '';
    this.show_pagination = '';
    this.per_page = '';
    this.orderby = '';
    this.order = '';
    this.show_filters = '';
    this.show_categories = '';
    this.show_types = '';
    this.show_ticket_prices = '';
    this.categories = '';
    this.event_types = '';
    this.ticket_prices = '';
    this.location = '';
    this.keywords = '';
    this.selected_datetime = '';
    this.selected_category = '';
    this.selected_event_type = '';
    this.selected_ticket_price = '';
  }
  
  componentDidMount () {
    super.updateShortcodeToHtml(`[events]`, this.ref)
  }
  
  //update shortcode on change attributes
  componentDidUpdate (prevProps) {
    const { featured , cancelled, show_pagination, per_page, orderby, order, show_filters, show_categories, show_types, show_ticket_prices, categories, event_types, ticket_prices, location, keywords, selected_datetime, selected_category, selected_event_type, selected_ticket_price} = this.props.atts
    if (featured && featured !== '' && featured !== prevProps.atts.featured) 
      this.featured = 'featured='+featured
    else if(featured=='')
      this.featured = ''

    if(cancelled  && cancelled !== prevProps.atts.cancelled)
      this.cancelled = 'cancelled='+cancelled
    
    if (show_pagination && show_pagination !== prevProps.atts.show_pagination) 
      this.show_pagination = 'show_pagination='+show_pagination
    
    if (per_page && per_page !== '0' && per_page !== prevProps.atts.per_page) 
      this.per_page = 'per_page='+per_page
    else if(per_page=='0')
      this.per_page = ''

    if (orderby && orderby !== prevProps.atts.orderby) 
      this.orderby = 'orderby='+orderby
    
    if (order && order !== prevProps.atts.order) 
      this.order = 'order='+order
    
    if (show_filters && show_filters !== prevProps.atts.show_filters) 
      this.show_filters = 'show_filters='+show_filters
    
    if (show_categories && show_categories !== prevProps.atts.show_categories) 
      this.show_categories = 'show_categories='+show_categories
    
    if (show_types && show_types !== prevProps.atts.show_types) 
      this.show_types = 'show_types='+show_types    
    
    if (show_ticket_prices && show_ticket_prices !== prevProps.atts.show_ticket_prices) 
      this.show_ticket_prices = 'show_ticket_prices='+show_ticket_prices
    
    if (categories && categories !== prevProps.atts.categories) 
      this.categories = 'categories='+categories

    if (event_types && event_types !== prevProps.atts.event_types) 
      this.event_types = 'event_types='+event_types

    if (ticket_prices && ticket_prices !== prevProps.atts.ticket_prices) 
      this.ticket_prices = 'ticket_prices='+ticket_prices

    if (location && location !== prevProps.atts.location) 
      this.location = 'location='+location

    if (keywords && keywords !== prevProps.atts.keywords) 
      this.keywords = 'keywords='+keywords

    if (selected_datetime && selected_datetime !== '0' && selected_datetime !== prevProps.atts.selected_datetime) 
      this.selected_datetime = 'selected_datetime='+selected_datetime
    else if(selected_datetime==0)
      this.selected_datetime = ''

    if (selected_category && selected_category !== '0' && selected_category !== prevProps.atts.selected_category) 
      this.selected_category = 'selected_category='+selected_category
    else if(selected_category==0)
      this.selected_category = ''

    if (selected_event_type && selected_event_type !== '0' && selected_event_type !== prevProps.atts.selected_event_type) 
      this.selected_event_type = 'selected_event_type='+selected_event_type
    else if(selected_event_type==0)
      this.selected_event_type = ''

    if (selected_ticket_price && selected_category !== '0' && selected_ticket_price !== prevProps.atts.selected_ticket_price) 
      this.selected_ticket_price = 'selected_ticket_price='+selected_ticket_price
    else if(selected_ticket_price==0)
      this.selected_ticket_price = ''
    
    super.updateShortcodeToHtml(`[events ${this.featured} ${this.cancelled} ${this.show_pagination} ${this.per_page} ${this.orderby} ${this.order} ${this.show_filters} ${this.show_categories} ${this.show_types} ${this.show_ticket_prices} ${this.categories} ${this.event_types} ${this.ticket_prices} ${this.location} ${this.keywords} ${this.selected_datetime} ${this.selected_category} ${this.selected_event_type} ${this.selected_ticket_price}]`, this.ref)
    
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
                <div className='vcvhelper' ref={(ref) => { this.ref = ref }} data-vcvs-html={`[events ${this.featured} ${this.cancelled} ${this.show_pagination} ${this.per_page} ${this.orderby} ${this.order} ${this.show_filters} ${this.show_categories} ${this.show_types} ${this.show_ticket_prices} ${this.categories} ${this.event_types} ${this.ticket_prices} ${this.location} ${this.keywords} ${this.selected_datetime} ${this.selected_category} ${this.selected_event_type} ${this.selected_ticket_price}]`} />
              </div>
            </div>
  }
}
