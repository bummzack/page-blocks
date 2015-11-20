<?php

/**
 * A content-block that contains N images
 * @author bummzack
 */
class ImageBlock extends Block
{
    private static $many_many = array(
        'Images' => 'Image'
    );

    private static $many_many_extraFields = array(
        'Images' => array('SortOrder' => 'Int')
    );

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $uploadField = null;
        if (class_exists('SortableUploadField')) {
            $uploadField = SortableUploadField::create('Images', _t('ImageBlock.IMAGES', 'Images'));
        } else {
            $uploadField = UploadField::create('Images', _t('ImageBlock.IMAGES', 'Images'));
        }

        $fields->addFieldToTab('Root.Main', $uploadField);
        $this->extend('updateCMSFields', $fields);
        return $fields;
    }
}