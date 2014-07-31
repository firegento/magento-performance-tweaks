<?php
/**
 * Quafzi_PerformanceTweaks_Model_Observer
 *
 * @see http://de.slideshare.net/ivanchepurnyi/magento-performance
 *
 * @category Mage
 * @package  Quafzi_PerformanceTweaks
 * @author   Thomas Birke <tbirke@netextreme.de>
 * @license  MIT
 */
class Quafzi_PerformanceTweaks_Model_Observer
{
    /**
     * Magento adds on every category and product page a layout handle with its entity id,
     * so you have layout cache unique for each product or category
     *
     * CAUTION: Widgets based on category id and product id handles WILL NOT WORK
     * SOLUTION: Use "custom layout update xml" attribute on entity level instead!
     */
    public function removeEntityIdFromLayout($observer)
    {
        $update = $observer->getLayout()->getUpdate();
        foreach ($update->getHandles() as $handle) {
            if (0 === strpos($handle, 'CATEGORY_')
                || (0 === strpos($handle, 'PRODUCT_') && false === strpos($handle, 'PRODUCT_TYPE_'))
            ) {
                $update->removeHandle($handle);
            }
        }
    }

    /**
     * Magento does not cache CMS blocks by default.
     */
    public function enableCmsBlockCaching($observer)
    {
        $block = $observer->getBlock();
        if ($block instanceof Mage_Cms_Block_Widget_Block
            || $block instanceof Mage_Cms_Block_Block
        ) {
            $cacheKeyData = array(
                Mage_Cms_Model_Block::CACHE_TAG,
                $block->getBlockId(),
                Mage::app()->getStore()->getId(),
                intval(Mage::app()->getStore()->isCurrentlySecure())
            );
            $block->setCacheKey(implode('_', $cacheKeyData));
            $block->setCacheTags(array(Mage_Cms_Model_Block::CACHE_TAG));
            $block->setCacheLifetime(false);
        }
    }
}
