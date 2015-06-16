<?php
/**
 * see http://de.slideshare.net/ivanchepurnyi/magento-performance
 * see http://bit.ly/magerule
 *
 * refers to CatalogRule
 *
 * Each time when Magento calls ``collectTotals()`` on a quote object,
 * it walks through all items in the quote and invokes ``getFinalPrice()``
 * on the product. This method dispatches "catalog_product_get_final_price"
 * event, that is observed by Mage_CatalogRule module.
 *
 * PHP version 5
 *
 * @category FireGento
 * @package  FireGento_PerformanceTweaks
 * @author   FireGento Team <team@firegento.com>
 * @license  http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 */

/**
 * FireGento_PerformanceTweaks_Model_Eav_Entity_Attribute_Source_Table
 *
 * @category FireGento
 * @package  FireGento_PerformanceTweaks
 * @author   FireGento Team <team@firegento.com>
 * @license  http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 */
class FireGento_PerformanceTweaks_Model_CatalogRule_Observer
    extends Mage_CatalogRule_Model_Observer
{
    protected $_preloadedPrices = array();

    /**
     * preload price rules
     *
     * @param  Varien_Event_Observer $observer Observer
     *
     * @return void
     */
    public function preloadPriceRules(Varien_Event_Observer $observer)
    {
        $quote = $observer->getQuote();
        $date = Mage::app()->getLocale()->storeTimeStamp($quote->getStoreId());
        $websiteId = $quote->getStore()->getWebsiteId();
        $groupId = $quote->getCustomerGroupId();

        $productIds = array();
        foreach ($quote->getAllItems() as $item) {
            $productIds[] = $item->getProductId();
        }

        $cacheKey = spl_object_hash($quote);

        if (!isset($this->_preloadedPrices[$cacheKey])) {
            $this->_preloadedPrices[$cacheKey] = Mage::getResourceSingleton('catalogrule/rule')
                ->getRulePrices($date, $websiteId, $groupId, $productIds);
        }

        foreach ($this->_preloadedPrices[$cacheKey] as $productId => $price) {
            $key = implode('|', array($date, $websiteId, $groupId, $productId));
            $this->_rulePrices[$key] = $price;
        }
    }
}
