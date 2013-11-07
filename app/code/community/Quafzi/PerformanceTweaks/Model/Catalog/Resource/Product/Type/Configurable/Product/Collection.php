<?php
/**
 * @see http://de.slideshare.net/ivanchepurnyi/magento-performance
 *
 * @category Catalog
 *
 * Magento is not using flat version of products for configurable products children retrieval.
 * So every configurable product page is a bottleneck.
 */
class Quafzi_PerformanceTweaks_Model_Catalog_Resource_Product_Type_Configurable_Product_Collection
    extends Mage_Catalog_Model_Resource_Product_Type_Configurable_Product_Collection
{
    public function isEnabledFlat()
    {
        return Mage_Catalog_Model_Resource_Product_Collection::isEnabledFlat();
    }

    public function addAttributeToFilter($attribute, $condition = null, $joinType = 'inner')
    {
        if ($this->isEnabledFlat() && is_numeric($attribute)) {
            $attribute = $this->getEntity()->getAttribute($attribute)->getAttributeCode();
        }

        return parent::addAttributeToFilter($attribute, $condition, $joinType);
    }
}
