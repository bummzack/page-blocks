<?php

/**
 * Bulk action handler to remove record from live and stage!
 *
 * @author bummzack
 */
class GridFieldBulkActionVersionedDeleteHandler extends GridFieldBulkActionHandler
{
    /**
     * RequestHandler allowed actions
     * @var array
     */
    private static $allowed_actions = array(
        'versioneddelete'
    );


    /**
     * RequestHandler url => action map
     * @var array
     */
    private static $url_handlers = array(
        'versioneddelete' => 'versioneddelete'
    );


    /**
     * @param SS_HTTPRequest $request
     * @return SS_HTTPResponse List of published record IDs
     */
    public function versioneddelete(SS_HTTPRequest $request)
    {
        $ids = array();

        foreach ($this->getRecords() as $record) {
            if ($record->hasExtension('Versioned')) {
                $record->deleteFromStage(Versioned::get_live_stage());
            }
            array_push($ids, $record->ID);
            $record->delete();
        }

        $response = new SS_HTTPResponse(Convert::raw2json(array(
            'done' => true,
            'records' => $ids
        )));
        $response->addHeader('Content-Type', 'text/json');
        return $response;
    }
}
