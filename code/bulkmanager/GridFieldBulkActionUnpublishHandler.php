<?php
/**
 * Bulk action handler to unpublish records
 * 
 * @author bummzack
 */
class GridFieldBulkActionUnpublishHandler extends GridFieldBulkActionHandler
{	
	/**
	 * RequestHandler allowed actions
	 * @var array
	 */
	private static $allowed_actions = array(
		'unpublish'
	);


	/**
	 * RequestHandler url => action map
	 * @var array
	 */
	private static $url_handlers = array(
		'unpublish' => 'unpublish'
	);
	

	/**
	 * Unpublish the selected records passed from the unpublish bulk action
	 * 
	 * @param SS_HTTPRequest $request
	 * @return SS_HTTPResponse List of published record IDs
	 */
	public function unpublish(SS_HTTPRequest $request)
	{
		$ids = array();
		
		foreach ( $this->getRecords() as $record )
		{				
			if($record->hasExtension('Versioned')){
				array_push($ids, $record->ID);
				$record->deleteFromStage('Live');
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