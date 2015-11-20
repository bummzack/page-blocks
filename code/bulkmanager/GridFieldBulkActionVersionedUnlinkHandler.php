<?php

/**
 * Bulk action handler to publish records
 *
 * @author bummzack
 */
class GridFieldBulkActionVersionedUnlinkHandler extends GridFieldBulkActionHandler
{
    /**
     * RequestHandler allowed actions
     * @var array
     */
    private static $allowed_actions = array(
        'versionedunlink'
    );


    /**
     * RequestHandler url => action map
     * @var array
     */
    private static $url_handlers = array(
        'versionedunlink' => 'versionedunlink'
    );


    /**
     * Unlink the selected records by setting the foreign key to zero
     * in both Stage and Live tables.
     *
     * @param SS_HTTPRequest $request
     * @return SS_HTTPResponse List of published record IDs
     */
    public function versionedunlink(SS_HTTPRequest $request)
    {
        $ids = $this->getRecordIDList();
        // remove the selected entries from Stage.
        $this->gridField->list->removeMany($ids);

        // Unpublish the unlinked records.
        // This is potentially destructive, but there's no other "good" way to do this.
        // When a unlinked record gets added to another page, the only way to "activate" the
        // record is to publish it.. so the published version will be overwritten anyway!
        foreach ($this->getRecords() as $record) {
            if ($record->hasExtension('Versioned')) {
                $record->deleteFromStage(Versioned::get_live_stage());
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