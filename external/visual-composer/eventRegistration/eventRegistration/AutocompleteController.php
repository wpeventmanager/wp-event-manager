<?php

namespace eventRegistrationcd\eventId;

use VisualComposer\Framework\Container;
use VisualComposer\Framework\Illuminate\Support\Module;
use VisualComposer\Helpers\Traits\EventsFilters;

class AutocompleteController extends Container implements Module
{
    use EventsFilters;

    public function __construct()
    {
        if (!defined('VCV_EVENT_ID_AUTOCOMPLETE_CATEGORY')) {
            $this->addFilter('vcv:autocomplete:eventId:render', 'eventIdAutocompleteSuggester');
            define('VCV_EVENT_ID_AUTOCOMPLETE_CATEGORY', true);
        }
    }
    /**
     * This function is return autocomplete id of event listing
     * @param $payload, $response
     *
     * @return array
     * @since 3.1.8
     */
    protected function eventIdAutocompleteSuggester($response, $payload)
    {
        global $wpdb;
        $searchValue = $payload['searchValue'];
        $returnValue = $payload['returnValue'];
        $eventId1 = (int)$searchValue;
        $searchValue = trim($searchValue);
        $postMetaInfos = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT ID AS id 
						FROM {$wpdb->posts} 
						WHERE ID LIKE '%d%' ",
                $eventId1 > 0 ? $eventId1 : -1,
                stripslashes($searchValue),
                stripslashes($searchValue)
            ),
            ARRAY_A
        );

        $response['results'] = [];
        if (is_array($postMetaInfos) && !empty($postMetaInfos)) {
            foreach ($postMetaInfos as $value) {
                $data = [];
                $data['value'] = $returnValue ? $value[$returnValue] : $value['id'];
                $data['label'] = $value['id'];
                $response['results'][] = $data;
            }
        }

        return $response;
    }
}