<?php
    namespace WPEMVisualComposer\eventListing;
   
    use VisualComposer\Framework\Container;
    use VisualComposer\Framework\Illuminate\Support\Module;
    use VisualComposer\Helpers\Traits\EventsFilters;
    use VisualComposer\Helpers\Traits\WpFiltersActions;
    
    class EventListingController extends Container implements Module
    {
        use EventsFilters;
        use WpFiltersActions;
        public function __construct()
        {
            if (!defined('VCV_WP_CAT_CONTROLLER')) {
                $this->addFilter(
                    'vcv:editor:variables vcv:editor:variables/eventListing',
                    'getCategory'
                );
                $this->wpAddAction(
                    'template_redirect',
                    'checkCategory'
                );
                define('VCV_WP_CAT_CONTROLLER', true);
            }
            if (!defined('VCV_WP_TYPE_CONTROLLER')) {
                $this->addFilter(
                    'vcv:editor:variables vcv:editor:variables/eventListing',
                    'getType'
                );
                $this->wpAddAction(
                    'template_redirect',
                    'checkType'
                );
                define('VCV_WP_TYPE_CONTROLLER', true);
            }
            if (!defined('VCV_WP_DATE_CONTROLLER')) {
                $this->addFilter(
                    'vcv:editor:variables vcv:editor:variables/eventListing',
                    'getDateFilter'
                );
                define('VCV_WP_DATE_CONTROLLER', true);
            }
            if (!defined('VCV_WP_TICKET_CONTROLLER')) {
                $this->addFilter(
                    'vcv:editor:variables vcv:editor:variables/eventListing',
                    'getTicketType'
                );
                define('VCV_WP_TICKET_CONTROLLER', true);
            }
            if (!defined('VCV_EVENT_AUTOCOMPLETE_CATEGORY')) {
                $this->addFilter('vcv:autocomplete:listCategory:render', 'getCategoryAutocompleteSuggester');
                define('VCV_EVENT_AUTOCOMPLETE_CATEGORY', true);
            }
            if (!defined('VCV_EVENT_AUTOCOMPLETE_TYPE')) {
                $this->addFilter('vcv:autocomplete:listType:render', 'getTypeAutocompleteSuggester');
                define('VCV_EVENT_AUTOCOMPLETE_TYPE', true);
            }
        }
        /**
         * This function is return autocomplete type of event listing 
         * @param $payload, $response
         *
         * @return array
         * @since 3.1.8
         */
        protected function getTypeAutocompleteSuggester($response, $payload)
        {
            global $wpdb;
            $searchValue = $payload['searchValue'];
            $returnValue = $payload['returnValue'];
            $carId = (int)$searchValue;
            $searchValue = trim($searchValue);

            $postMetaInfos = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT term.name as name
                            FROM {$wpdb->term_taxonomy} AS tax
                            INNER JOIN {$wpdb->terms} AS term ON term.term_id = tax.term_id
                            WHERE tax.taxonomy = 'event_listing_type' AND (term.slug LIKE '%%%s%%' OR term.name LIKE '%%%s%%' )",
                    $carId > 0 ? $carId : -1,
                    stripslashes($searchValue),
                    stripslashes($searchValue)
                ),
                ARRAY_A
            );
    
            $response['results'] = [];
            if (is_array($postMetaInfos) && !empty($postMetaInfos)) {
                foreach ($postMetaInfos as $value) {
                    $data = [];
                    $data['value'] = $returnValue ? $value[$returnValue] : $value['name'];
                    $data['label'] =  $value['name'] ;
                    $response['results'][] = $data;
                }
            }
    
            return $response;
        }
        /**
         * This function is return autocomplete category of event listing 
         * @param $payload, $response
         *
         * @return array
         * @since 3.1.8
         */
        protected function getCategoryAutocompleteSuggester($response, $payload)
        {
            global $wpdb;
            $searchValue = $payload['searchValue'];
            $returnValue = $payload['returnValue'];
            $carId = (int)$searchValue;
            $searchValue = trim($searchValue);

            $postMetaInfos = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT term.name as name
                            FROM {$wpdb->term_taxonomy} AS tax
                            INNER JOIN {$wpdb->terms} AS term ON term.term_id = tax.term_id
                            WHERE tax.taxonomy = 'event_listing_category' AND (term.slug LIKE '%%%s%%' OR term.name LIKE '%%%s%%' )",
                    $carId > 0 ? $carId : -1,
                    stripslashes($searchValue),
                    stripslashes($searchValue)
                ),
                ARRAY_A
            );
    
            $response['results'] = [];
            if (is_array($postMetaInfos) && !empty($postMetaInfos)) {
                foreach ($postMetaInfos as $value) {
                    $data = [];
                    $data['value'] = $returnValue ? $value[$returnValue] : $value['name'];
                    $data['label'] =  $value['name'] ;
                    $response['results'][] = $data;
                }
            }
    
            return $response;
        }
        /**
         * This function is return all category of event listing
         * @param $variables
         *
         * @return array
         * @since 3.1.8
         */
        protected function getCategory($variables)
        {
            $args = array(
                'orderby'                  => 'name',
                'order'                    => 'ASC',
                'hide_empty'               => false,
                'taxonomy'                 => 'event_listing_category'
            );
            
            $cats = get_categories( $args );
            $categories = [];
            $categories[] = ['label' => __('Select category', 'wp-event-manager'), 'value' => 0];
            if ($cats) {
                foreach ($cats as $cat) {
                    $categories[] = [
                        'label' => $cat->name,
                        'value' => $cat->name,
                    ];
                }
            } else {
                $categories = [
                    ['label' => __('No category found', 'wp-event-manager'), 'value' => 0],
                ];
            }
            $variables[] = [
                'key' => 'vcvWPEMCatList',
                'value' => $categories,
            ];
            return $variables;
        }
        /**
         * This function is check for event listing category is available or not
         *
         * @return string
         * @since 3.1.8
         */
        protected function checkCategory()
        {
            $args = array(
                'orderby'                  => 'name',
                'order'                    => 'ASC',
                'hide_empty'               => false,
                'taxonomy'                 => 'event_listing_category'
            );
            if(! get_option( 'event_manager_enable_categories' )){
                return __('The Category is disabled', 'wp-event-manager'); 
            }
            else if (count(get_categories($args))==0) {
                return __('The Category is not available', 'wp-event-manager'); 
            }
        }
        /**
         * This function is return all type of event listing
         * @param $variables
         *
         * @return array
         * @since 3.1.8
         */
        protected function getType($variables)
        {
            $args = array(
                'orderby'                  => 'name',
                'order'                    => 'ASC',
                'hide_empty'               => false,
                'taxonomy'                 => 'event_listing_type'
            );
            
            $cats = get_categories( $args );
            $types = [];
            $types[] = ['label' => __('Select Event Type', 'wp-event-manager'), 'value' => 0];
            if ($cats) {
                foreach ($cats as $cat) {
                    $types[] = [
                        'label' => $cat->name,
                        'value' => $cat->name,
                    ];
                }
            } else {
                $types = [
                    ['label' => __('Event-type not found', 'wp-event-manager'), 'value' => 0],
                ];
            }
            $variables[] = [
                'key' => 'vcvWPEMTypeList',
                'value' => $types,
            ];
            return $variables;
        }
        /**
         * This function is check for event listing type is available or not
         *
         * @return string
         * @since 3.1.8
         */
        protected function checkType()
        {
            $args = array(
                'orderby'                  => 'name',
                'order'                    => 'ASC',
                'hide_empty'               => false,
                'taxonomy'                 => 'event_listing_type'
            );
            if(! get_option( 'event_manager_enable_event_types' )){
                return __('Event-Type is disabled', 'wp-event-manager'); 
            }
            else if (count(get_categories($args))==0) {
                return __('Event-Type is not available', 'wp-event-manager'); 
            }
        }
        /**
         * This function is return date filter values
         * @param $variables
         *
         * @return array
         * @since 3.1.8
         */
        protected function getDateFilter($variables)
        {
            
            $dates = [];
            $dates[] = ['label' => __('Select Date', 'wp-event-manager'), 'value' => 0];

            $dates[] = ['label' => __('Any Date', 'wp-event-manager'), 'value' => 'datetime_any'];
            $dates[] = ['label' => __('Today', 'wp-event-manager'), 'value' => 'datetime_today'];
            $dates[] = ['label' => __('Tomorrow', 'wp-event-manager'), 'value' => 'datetime_tomorrow'];
            $dates[] = ['label' => __('This Week', 'wp-event-manager'), 'value' => 'datetime_thisweek'];
            $dates[] = ['label' => __('This Weekend', 'wp-event-manager'), 'value' => 'datetime_thisweekend'];
            $dates[] = ['label' => __('This Month', 'wp-event-manager'), 'value' => 'datetime_thismonth'];
            $dates[] = ['label' => __('This Year', 'wp-event-manager'), 'value' => 'datetime_thisyear'];
            $dates[] = ['label' => __('Next Week', 'wp-event-manager'), 'value' => 'datetime_nextweek'];
            $dates[] = ['label' => __('Next Weekend', 'wp-event-manager'), 'value' => 'datetime_nextweekend'];
            $dates[] = ['label' => __('Next Month', 'wp-event-manager'), 'value' => 'datetime_nextmonth'];
            $dates[] = ['label' => __('Next Year', 'wp-event-manager'), 'value' => 'datetime_nextyear'];
           
            $variables[] = [
                'key' => 'vcvWPEMDateList',
                'value' => $dates,
            ];
            return $variables;
        }
        /**
         * This function is return date filter values
         * @param $variables
         *
         * @return array
         * @since 3.1.8
         */
        protected function getTicketType($variables)
        {
           
            $tickets = [];
            $tickets[] = ['label' => __('Select Ticket Type', 'wp-event-manager'), 'value' => 0];

            $tickets[] = ['label' => __('Any Ticket Price', 'wp-event-manager'), 'value' => 'ticket_price_any'];
            $tickets[] = ['label' => __('Paid', 'wp-event-manager'), 'value' => 'ticket_price_paid'];
            $tickets[] = ['label' => __('Free', 'wp-event-manager'), 'value' => 'ticket_price_free'];
            
            $variables[] = [
                'key' => 'vcvWPEMTicketType',
                'value' => $tickets,
            ];
            return $variables;
        }
    }