<?php
// don't load directly
if (!defined('ABSPATH')) {
    die('-1');
}
/**
 * 
 * Action for visual composer custom element bind
 * 
 * @since 3.1.8
 */

add_action(
/**
 * @param $api \VisualComposer\Modules\Api\Factory
 */
    'vcv:api',
    function ($api) {
        $elementsToRegister = [
            'submitEventForm',
            'eventListing',
            'eventSummary',
            'singleEventListing',
            'eventRegistration',
            'eventDashboard',
            'pastEventListing'     
        ];
        $pluginBaseUrl = rtrim(plugins_url(basename(EVENT_MANAGER_PLUGIN_DIR)), '\\/');
        /** @var \VisualComposer\Modules\Elements\ApiController $elementsApi */
        $elementsApi = $api->elements;
        foreach ($elementsToRegister as $tag) {
            $manifestPath = EVENT_MANAGER_PLUGIN_DIR . '/external/visual-composer/' . $tag . '/manifest.json';
            $elementBaseUrl = $pluginBaseUrl . '/external/visual-composer/' . $tag;
            $elementsApi->add($manifestPath, $elementBaseUrl);
        }
    }
);
