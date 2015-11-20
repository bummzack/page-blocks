<?php

/**
 * Extension that applies blocks to a page.
 *
 * @author bummzack
 */
class PageBlocks extends DataExtension
{
    private static $has_many = array(
        'Blocks' => 'Block'
    );

    /**
     * Whether or not the page should have a button to publish page & blocks with one click.
     * Set this via config.yml
     * @var bool
     */
    private static $allow_publish_all = true;

    public function updateCMSFields(FieldList $fields)
    {
        $gridConfig = GridFieldConfig_BlockEditor::create('SortOrder', $this->owner->config()->get('allowed_blocks'));

        $gridField = GridField::create('Blocks', _t('PageBlocks.BLOCK', 'Block', 'GridField Title'),
            $this->owner->Blocks(), $gridConfig);
        $gridField->setModelClass('Block');

        $fields->addFieldsToTab('Root.Main', array(
            $gridField
        ), 'Metadata');
    }

    /**
     * Add the publish all button
     */
    public function updateCMSActions(FieldList $actions)
    {
        if (Config::inst()->get('PageBlocks', 'allow_publish_all') == false) {
            return;
        }
        $button = FormAction::create('publishblocks',
            _t('PageBlocks.PUBLISH_ALL', 'Publish Page & Blocks', 'Button label to publish page and all blocks'))
            ->setAttribute('data-icon', 'accept');

        if ($majorActions = $actions->fieldByName('MajorActions')) {
            $majorActions->push($button);
        } else {
            $actions->push($button);
        }
    }
}


/**
 * GridFieldConfig that supplies the config needed to edit content blocks within a page
 */
class GridFieldConfig_BlockEditor extends GridFieldConfig_RelationEditor
{

    /**
     * @param string $sortField - Field to sort the blocks on. If this is set, it will also make the
     *    blocks sortable in the CMS (requires SortableGridField module!)
     * @param array $allowedBlocks - a set of allowed class names, optionally mapped to titles
     * @param int $itemsPerPage - How many items per page should show up
     */
    public function __construct($sortField = null, $allowedBlocks = null, $itemsPerPage = null)
    {
        parent::__construct($itemsPerPage);

        // setup a bulk manager for block management
        $bulkManager = new GridFieldBulkManager();
        // remove the default actions
        $toRemove = array('bulkedit', 'bulkEdit', 'delete', 'unlink', 'unLink');
        $validActions = array_keys($bulkManager->getConfig('actions'));
        foreach ($toRemove as $key) {
            if (in_array($key, $validActions)) {
                $bulkManager->removeBulkAction($key);
            }
        }

        // add the actions in desired order
        $bulkManager
            ->addBulkAction('publish', _t('PageBlock.PUBLISH', 'Publish'))
            ->addBulkAction('unpublish', _t('PageBlock.UNPUBLISH', 'Unpublish'))
            ->addBulkAction('versionedunlink', _t('PageBlock.UNLINK', 'Unlink'),
                'GridFieldBulkActionVersionedUnlinkHandler', array(
                    'icon' => 'chain--minus'
                )
            )->addBulkAction('versioneddelete', _t('PageBlock.DELETE', 'Delete'),
                'GridFieldBulkActionVersionedDeleteHandler', array(
                    'isDestructive' => true,
                    'icon' => 'decline'
                )
            );

        if ($sortField && class_exists('GridFieldOrderableRows')) {
            $this->addComponent(new GridFieldOrderableRows($sortField));
        }

        // remove the delete action, since unlinking is not required
        $this->removeComponent($this->getComponentByType('GridFieldDeleteAction'));

        // remove the search field since it doesn't make sense (cannot add existing, unless "stealing" from another page)
        $this->removeComponentsByType('GridFieldAddExistingAutocompleter');

        $this->addComponent(new GridFieldAddExistingSearchButton('buttons-before-right'));

        $this->addComponent(new GridFieldAddNewMultiClass(), 'GridFieldToolbarHeader');
        $this->addComponent($bulkManager);
        $this->getComponentByType('GridFieldDataColumns')->setDisplayFields(array(
            'CMSTitle' => _t('Block.TITLE', 'Title'),
            'i18n_singular_name' => _t('Block.TYPE', 'Type'),
            'PublishedStatus' => _t('Block.STATUS', 'Status')
        ));

        if (is_array($allowedBlocks)) {
            $this->setAllowedBlocks($allowedBlocks);
        }

    }

    /**
     * Get the allowed blocks for this gridfield
     * @return array|null
     */
    public function getAllowedBlocks()
    {
        if ($mc = $this->getComponentByType('GridFieldAddNewMultiClass')) {
            return $mc->getClasses();
        }
        return null;
    }

    /**
     * Set the allowed blocks for this gridfield
     * @param array $allowedBlocks a set of allowed class names, optionally mapped to titles
     */
    public function setAllowedBlocks(array $allowedBlocks)
    {
        $classes = null;

        if ($mc = $this->getComponentByType('GridFieldAddNewMultiClass')) {
            $search = $this->getComponentByType('GridFieldAddExistingSearchButton');
            $blockClasses = array_values(ClassInfo::subclassesFor('Block'));
            sort($blockClasses);

            if (is_array($allowedBlocks)) {
                $allowedClasses = array();

                foreach ($allowedBlocks as $class => $title) {
                    if (!is_string($class)) {
                        $class = $title;
                    }

                    if (in_array($class, $blockClasses)) {
                        $allowedClasses[] = $class;
                    }
                }

                $mc->setClasses($allowedClasses);
                if ($search) {
                    $search->setSearchList(Block::get()->filter(array(
                        'ParentID' => 0,
                        'ClassName' => $allowedClasses
                    )));
                }
            } else {
                if ($search) {
                    $search->setSearchList(Block::get()->filter(array('ParentID' => 0)));
                }
                $mc->setClasses(null);
            }

        }

        // for method chaining
        return $this;
    }
}