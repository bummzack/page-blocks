<?php
/**
 * Extension that applies blocks to a page.
 * 
 * @author bummzack
 */
class BlocksExtension extends DataExtension
{
	private static $has_many = array(
		'Blocks' => 'Block'
	);
	
	public function updateCMSFields(FieldList $fields) {
		$gridConfig = singleton('Block')->has_extension('Sortable') 
			? GridFieldConfig_BlockEditor::create('SortOrder')
			: GridFieldConfig_BlockEditor::create();
		
		$gridField = new GridField('Blocks', 'Block', $this->Blocks(), $gridConfig);
		$gridField->setModelClass('Block');
		
		$fields->addFieldsToTab('Root.Main', array(
			$gridField
		), 'Metadata');
	}
}


/**
 * GridFieldConfig that supplies the config needed to edit content blocks within a page
 */
class GridFieldConfig_BlockEditor extends GridFieldConfig_RelationEditor {

	/**
	 * @param string $sortField - Field to sort the blocks on. If this is set, it will also make the
	 * 	blocks sortable in the CMS
	 * @param int $itemsPerPage - How many items per page should show up
	 */
	public function __construct($sortField = null, $itemsPerPage=null) {
		parent::__construct($itemsPerPage);
		
		// setup a bulk manager for block management
		$bulkManager = new GridFieldBulkManager();
		// remove the default actions
		$bulkManager->removeBulkAction('bulkedit')->removeBulkAction('delete')->removeBulkAction('unlink');
		// add the actions in desired order
		$bulkManager
			->addBulkAction('publish')
			->addBulkAction('unpublish')
			->addBulkAction('bulkedit', 'Edit', 'GridFieldBulkActionEditHandler')
			->addBulkAction('versioneddelete', 'Delete', 'GridFieldBulkActionVersionedDeleteHandler');
		
		if($sortField){
			$this->addComponent(new GridFieldSortableRows('SortOrder'));
		}
		
		// remove the delete action, since unlinking is not required
		$this->removeComponent($this->getComponentByType('GridFieldDeleteAction'));
		
		$this->addComponent(new GridFieldAddNewMultiClass(), 'GridFieldToolbarHeader');
		$this->addComponent($bulkManager);
		$this->getComponentByType('GridFieldDataColumns')->setDisplayFields(array(
			'Title' => 'Title',
			'ClassName' => 'Type',
			'VersionStatus' => 'Status'
		));
	}
}