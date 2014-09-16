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
		
		$tinyMce = new HtmlEditorField('Content', 'Text');
		
		$fields->addFieldsToTab('Root.Main', array(
			$tinyMce
		));
		
		$this->extend('updateCMSFields', $fields);
		return $fields;
	}
}