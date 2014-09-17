<?php
/**
 * A page content-block.
 * 
 * This is the base-class for all blocks. 
 * Subclass this to create custom blocks.
 * @author bummzack
 */
class Block extends DataObject
{
	private static $db = array(
		'Title'	=> 'Varchar(255)'
	);
	
	private static $has_one = array(
		'Parent' => 'SiteTree'
	);
	
	private static $extensions = array(
		"Versioned('Stage', 'Live')"
	);
	
	public function getCMSFields()
	{
		$fields = new FieldList();
		$fields->push(new TabSet('Root', $mainTab = new Tab('Main')));
		
		$mainTab->setTitle(_t('SiteTree.TABMAIN', "Main"));
		
		$fields->addFieldsToTab('Root.Main', array(
			new TextField('Title', _t('Block.TITLE', 'Title'))
		));
		$this->extend('updateCMSFields', $fields);
		return $fields;
	}
	
	/**
	 * Get the current published status.
	 * @return string
	 */
	public function PublishedStatus()
	{
		if(!$this->IsPublished()){
			return _t('Block.UNPUBLISHED', 'Unpublished');
		}
		if($this->stagesDiffer('Stage', 'Live')){
			return _t('Block.MODIFIED', 'Modified');
		}
		return _t('Block.PUBLISHED', 'Published');
	}
	
	/**
	 * Whether or not this block has been published
	 * @return boolean
	 */
	public function IsPublished()
	{
		if(!$this->ID)
			return false;
		
		return (DB::query('SELECT "ID" FROM "Block_Live" WHERE "ID" = '. $this->ID)->value())
			? true
			: false;
	}
	
	/**
	 * Prevent creation of non-specific Blocks 
	 * @see DataObject::canCreate()
	 */
	public function canCreate($member = null)
	{
		if($this->ClassName == 'Block'){
			return false;
		}
		
		return parent::canCreate($member);
	}
	
	/**
	 * Render this block to HTML
	 * @return HTMLText
	 */
	public function HTML()
	{
		// render with a template that has the same classname or fall back to "Block"
		return $this->renderWith(array($this->ClassName, 'Block'));
	}
}