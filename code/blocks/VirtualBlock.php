<?php
/**
 * The Virtual Block follows the same logic as a "Virtual Page" in the CMS.
 * It can be used to copy a blocks content to another page (or even multiple times within the same page).
 */
class VirtualBlock extends Block {
	private static $has_one = array(
		'OriginalBlock' => 'Block'
	);

	public function getTitle(){
		if($this->OriginalBlockID){
			return _t('VirtualBlock.COPYOF', '(copy of)') . ' ' . $this->OriginalBlock()->Title;
		}
		return _t('VirtualBlock.NO_ORIGINAL', '(no original set)');
	}

	public function getCMSFields()
	{
		$fields = FieldList::create();
		$fields->push(TabSet::create('Root', $mainTab = new Tab('Main')));

		$mainTab->setTitle(_t('SiteTree.TABMAIN', "Main"));

		$source = Block::get()->exclude('ClassName', 'VirtualBlock')->sort('ParentID, SortOrder')->map('ID', 'FullTitle');

		$fields->addFieldsToTab('Root.Main', array(
			ReadonlyField::create('Title', _t('Block.TITLE', 'Title'), $this->getTitle()),
			DropdownField::create('OriginalBlockID', _t('VirtualBlock.SELECT_ORIGINAL', 'Select original'), $source)
		));

		$this->extend('updateCMSFields', $fields);
		return $fields;
	}

	public function CSSClass(){
		if($this->OriginalBlockID){
			return $this->OriginalBlock()->CSSClass();
		}
		return parent::CSSClass();
	}

	public function HTML()
	{
		if($this->OriginalBlockID){
			return $this->OriginalBlock()->HTML();
		}
		return '';
	}
}