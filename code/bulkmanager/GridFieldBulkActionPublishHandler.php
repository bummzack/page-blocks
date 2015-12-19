<?php

/**
 * Bulk action handler to publish records
 *
 * @author bummzack
 */
class GridFieldBulkActionPublishHandler extends GridFieldBulkActionHandler
{
    /**
     * RequestHandler allowed actions
     * @var array
     */
    private static $allowed_actions = array(
        'publish'
    );


    /**
     * RequestHandler url => action map
     * @var array
     */
    private static $url_handlers = array(
        'publish' => 'publish'
    );


    /**
     * Publish the selected records passed from the publish bulk action
     *
     * @param SS_HTTPRequest $request
     * @return SS_HTTPResponse List of published record IDs
     */
    public function publish(SS_HTTPRequest $request)
    {
        $ids = array();

        foreach ($this->getRecords() as $record) {
            if ($record->hasExtension('Versioned')) {
                array_push($ids, $record->ID);
                $record->publish('Stage', Versioned::get_live_stage());
            }
        }

        $response = new SS_HTTPResponse(Convert::raw2json(array(
            'done' => true,
            'records' => $ids
        )));
        $response->addHeader('Content-Type', 'text/json');
        return $response;
    }
}
