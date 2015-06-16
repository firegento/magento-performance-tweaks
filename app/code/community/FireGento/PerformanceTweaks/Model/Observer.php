<?php
/**
 * see http://de.slideshare.net/ivanchepurnyi/magento-performance
 *
 * PHP version 5
 *
 * @category FireGento
 * @package  FireGento_PerformanceTweaks
 * @author   FireGento Team <team@firegento.com>
 * @license  http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 */

/**
 * FireGento_PerformanceTweaks_Model_Observer
 *
 * @category FireGento
 * @package  FireGento_PerformanceTweaks
 * @author   FireGento Team <team@firegento.com>
 * @license  http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 */
class FireGento_PerformanceTweaks_Model_Observer
{
    /**
     * Magento adds on every category and product page a layout handle with its
     * entity id, so you have layout cache unique for each product or category
     *
     * CAUTION: Widgets based on category id and product id handles WILL NOT WORK
     * SOLUTION: Use "custom layout update xml" attribute on entity level instead!
     *
     * @param  Varien_Observer $observer Observer
     *
     * @return null
     */
    public function removeEntityIdFromLayout($observer)
    {
        $update = $observer->getLayout()->getUpdate();
        foreach ($update->getHandles() as $handle) {
            if ($this->_isCategoryHandle($handle)
                || $this->_isProductHandle($handle)
            ) {
                $update->removeHandle($handle);
            }
        }
    }

    /**
     * Magento does not cache CMS blocks by default.
     *
     * @param  Varien_Observer $observer Observer
     *
     * @return null
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

    /**
     * if layout update handle is a category handle
     *
     * @param  string $handle Layout handle
     *
     * @return boolean
     */
    protected function _isCategoryHandle($handle)
    {
        return (0 === strpos($handle, 'CATEGORY_'));
    }

    /**
     * if layout update handle is a product handle
     *
     * @param  string $handle Layout handle
     *
     * @return boolean
     */
    protected function _isProductHandle($handle)
    {
        return (0 === strpos($handle, 'PRODUCT_')
            && false === strpos($handle, 'PRODUCT_TYPE_')
        );
    }
}
