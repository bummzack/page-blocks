<?php

/**
 * Extension that enables publishing of a page and all blocks.
 * Must be added to LeftAndMain
 *
 * @author bummzack
 */
class PublishAllExtension extends LeftAndMainExtension
{
    private static $allowed_actions = array(
        'publishblocks'
    );

    /**
     * Publish all blocks and the page itself
     */
    public function publishblocks($data, $form)
    {
        $className = $this->owner->stat('tree_class');
        $SQL_id = Convert::raw2sql($data['ID']);

        $record = DataObject::get_by_id($className, $SQL_id);

        if ($record && !$record->canPublish()) {
            return Security::permissionFailure($this->owner);
        }
        if (!$record || !$record->ID) {
            throw new SS_HTTPResponse_Exception("Bad record ID #" . (int)$data['ID'], 404);
        }

        $record->doPublish();

        $blocks = $record->Blocks();
        if ($blocks) {
            foreach ($blocks as $block) {
                $block->publish('Stage', 'Live');
            }
        }

        $this->owner->response->addHeader(
            'X-Status',
            rawurlencode(_t('PageBlocks.PUBLISH_ALL_SUCCESS', 'Successfully published page and all blocks'))
        );

        return $this->owner->getResponseNegotiator()->respond($this->owner->request);
    }
}
