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
        'Title' => 'Varchar(255)',
        'SortOrder' => 'Int'
    );

    private static $has_one = array(
        'Parent' => 'SiteTree'
    );

    private static $extensions = array(
        "Versioned('Stage', 'Live')"
    );

    private static $summary_fields = array(
        'Title' => 'Title',
        'ClassName' => 'Type',
        'Parent.Title' => 'Belongs to'
    );

    private static $default_sort = 'SortOrder';

    public function getCMSFields()
    {
        $fields = FieldList::create();
        $fields->push(TabSet::create('Root', $mainTab = new Tab('Main')));

        $mainTab->setTitle(_t('SiteTree.TABMAIN', "Main"));

        $fields->addFieldsToTab('Root.Main', array(
            TextField::create('Title', _t('Block.TITLE', 'Title'))
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
        if (!$this->IsPublished()) {
            return _t('Block.UNPUBLISHED', 'Unpublished');
        }
        if ($this->stagesDiffer('Stage', 'Live') || $this->isSortingChanged()) {
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
        if (!$this->ID) {
            return false;
        }

        return (DB::query('SELECT "ID" FROM "Block_Live" WHERE "ID" = ' . $this->ID)->value())
            ? true
            : false;
    }

    /**
     * Prevent creation of non-specific Blocks
     * @see DataObject::canCreate()
     */
    public function canCreate($member = null)
    {
        if ($this->ClassName == 'Block') {
            return false;
        }

        return parent::canCreate($member);
    }

    /**
     * Class name to use in CSS
     * @return string
     */
    public function CSSClass()
    {
        return $this->ClassName;
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

    /**
     * Get the full title of this block, including title of the parent (eg. the Page it belongs to)
     * @return string
     */
    public function FullTitle()
    {
        if ($this->ParentID) {
            return $this->Parent()->Title . ' > ' . $this->Title;
        }
        return $this->Title;
    }

    /**
     * Get the title to show in the CMS
     * @return string
     */
    public function getCMSTitle()
    {
        return $this->Title;
    }

    /**
     * Whether or not sorting of this block has changed.
     * This has to be checked separately, because the gridfield-extensions that deal with
     * sorting directly modify DB entries and thus don't update version numbers in the stage tables.
     * @param bool $checkPublished whether or not the published status of the block should also be checked
     * @return bool true if sorting is different between Stage and Live
     */
    protected function isSortingChanged($checkPublished = false)
    {
        if ($checkPublished && !$this->IsPublished()) {
            return false; // unpublished blocks obviously don't have different sortings
        }

        $sortLive = DB::query('SELECT "SortOrder" FROM "Block_Live" WHERE "ID" = ' . $this->ID)->value();
        return $sortLive != $this->SortOrder;
    }

    protected function onBeforeWrite()
    {
        parent::onBeforeWrite();

        if (!$this->exists() && !$this->SortOrder) {
            // upon initial write, determine the max sort order and apply it
            $maxSort = DB::query('SELECT MAX("SortOrder") FROM "Block" WHERE "ParentID" = ' . $this->ParentID)->value();
            $this->SortOrder = is_numeric($maxSort) ? $maxSort + 1 : 1;
        }
    }
}
