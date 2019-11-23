<?php

declare(strict_types = 1);

namespace Light\Form\Element;

/**
 * Class TrumbowygResponsive
 * @package Light\Form\Element
 */
class TrumbowygResponsive extends ElementAbstract
{
    /**
     * @var string
     */
    public $elementTemplate = 'element/trumbowyg-responsive';

    /**
     * @return array
     */
    public function getValue()
    {
        $value = parent::getValue();

        if (isset($value['content'])) {

            $contentCompleted = [];
            $contentMeta = $value['meta'];
            $indexMeta = 0;

            $contentData = (!isset($value['content']) || empty($value['content']))?[]:$value['content'];

            foreach ($contentData as $index => $content) {

                if (!isset($contentCompleted[$indexMeta])) {
                    $contentCompleted[$indexMeta] = [];
                }

                $contentCompleted[$indexMeta][] = $content;

                if ($contentMeta[$index] == 'full' || $contentMeta[$index] == 'right') {
                    $indexMeta ++ ;
                }
            }

            $value = $contentCompleted;
        }

        return $value;
    }
}