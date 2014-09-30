<?php
/**
 * Content block with text
 * @author bummzack
 */
class TextBlock extends Block
{
	private static $db = array(
		'Content' => 'HTMLText'
	);
	
	public function getCMSFields()
	{
		$fields = parent::getCMSFields();
		
		$tinyMce = HtmlEditorField::create('Content', _t('TextBlock.CONTENT', 'Content'));
		$fields->addFieldToTab('Root.Main', $tinyMce);
		
		$this->extend('updateCMSFields', $fields);
		return $fields;
	}
}