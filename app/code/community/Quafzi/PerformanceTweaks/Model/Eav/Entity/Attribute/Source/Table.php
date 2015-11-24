<?php
/**
 * see http://bit.ly/mageoption
 * see http://de.slideshare.net/ivanchepurnyi/magento-performance
 *
 * refers to Layered Navigation
 *
 * For each attribute you have and that is marked as filterable,
 * Magento will call getAllOptions() of attribute source model.
 * Even if there is no filter result for it, it will invoke
 * attribute option collection load.
 * For merchants with lots of attributes, this is a huge performance bottleneck.
 *
 * Optimized version of attribute source options model
 * That allows to preload options once and reuse them instead of doing calls to db all the time
 *
 * PHP version 5
 *
 * @category Mage
 * @package  Quafzi_PerformanceTweaks
 * @author   Thomas Birke <tbirke@netextreme.de>
 * @license  MIT http://opensource.org/licenses/MIT
 */

/**
 * Quafzi_PerformanceTweaks_Model_Eav_Entity_Attribute_Source_Table
 *
 * @category Mage
 * @package  Quafzi_PerformanceTweaks
 * @author   Thomas Birke <tbirke@netextreme.de>
 * @license  MIT http://opensource.org/licenses/MIT
 */
class Quafzi_PerformanceTweaks_Model_Eav_Entity_Attribute_Source_Table
    extends Mage_Eav_Model_Entity_Attribute_Source_Table
{
    /**
     * List of preloaded options per attribute
     *
     * @var array
     */
    protected static $_my_preloadedOptions = array();

    /**
     * List of stores where default values already preloaded
     *
     * @var array
     */
    protected static $_my_preloadedOptionsStores = array();

    /**
     * List of preloaded options for each option id
     *
     * @var array
     */
    protected static $_my_preloadedOptionHash = array();

    /**
     * Retrieve store options from preloaded hashes
     *
     * @param  int    $storeId     Store ID
     * @param  int    $attributeId Attribute ID
     * @param  string $type        Type
     *
     * @return array
     */
    protected function _getPreloadedOptions($storeId, $attributeId, $type)
    {
        $this->_preloadOptions($storeId);

        $key = $this->_getCombinedKey($storeId, $attributeId, 'store');

        if (isset(self::$_my_preloadedOptions[$key])) {
            return self::$_my_preloadedOptions[$key];
        }

        return array();
    }

    /**
     * Preloads values for option values on the first call
     *
     * @param int $storeId Store ID
     */
    protected function _preloadOptions($storeId)
    {
        if (isset(self::$_my_preloadedOptionsStores[$storeId])) {
            return;
        }
        self::$_my_preloadedOptionsStores[$storeId] = true;
        $collection = Mage::getResourceModel('eav/entity_attribute_option_collection')
            ->setPositionOrder('asc')
            ->setStoreFilter($storeId);
        // This one allows to limit selection of options, based on frontend criteria.
        // E.g. if not all the attribute options are needed for the current page
        Mage::dispatchEvent('eav_entity_attribute_source_table_preload_options', array(
            'collection' => $collection,
            'store_id' => $storeId
        ));
        $options = $collection->getData();

        foreach ($options as $option) {
            $optionKey = $this->_getCombinedKey($storeId, $option['option_id'], 'store');
            $storeKey = $this->_getCombinedKey($storeId, $option['attribute_id'], 'store');
            $defaultKey = $this->_getCombinedKey($storeId, $option['attribute_id'], 'default');

            self::$_my_preloadedOptionHash[$optionKey] = $option['value'];
            self::$_my_preloadedOptions[$storeKey][] = array(
                'value' => $option['option_id'],
                'label' => $option['value']
            );
            self::$_my_preloadedOptions[$defaultKey][] = array(
                'value' => $option['option_id'],
                'label' => $option['default_value']
            );
        }
    }

    /**
     * Overridden to manipulate options
     *
     * @param  bool $withEmpty     Allow empty ones
     * @param  bool $defaultValues Use default values
     *
     * @return array
     */
    public function getAllOptions($withEmpty = true, $defaultValues = false)
    {
        $storeId = $this->getAttribute()->getStoreId();
        if (!is_array($this->_options)) {
            $this->_options = array();
        }
        if (!is_array($this->_optionsDefault)) {
            $this->_optionsDefault = array();
        }
        if (!isset($this->_options[$storeId])) {
            $this->_options[$storeId] = self::_getPreloadedOptions(
                $storeId,
                $this->getAttribute()->getId(),
                'store'
            );
            $this->_optionsDefault[$storeId] = self::_getPreloadedOptions(
                $storeId,
                $this->getAttribute()->getId(),
                'default'
            );
        }
        $options = ($defaultValues ? $this->_optionsDefault[$storeId] : $this->_options[$storeId]);
        if ($withEmpty) {
            array_unshift($options, array('label' => '', 'value' => ''));
        }
        return $options;
    }

    /**
     * Returns option key for hash generation
     *
     * @param  int    $storeId  Store ID
     * @param  int    $optionId Option ID
     * @param  string $type     Type
     *
     * @return string
     */
    protected function _getCombinedKey($storeId, $optionId, $type)
    {
        return $storeId . '|' . $optionId . '|' . $type;
    }

    /**
     * Retrieves option label from preloaded options hash array
     *
     * @param  int|string $value Option value
     *
     * @return array|bool|string Option text
     */
    public function getOptionText($value)
    {
        $storeId = $this->getAttribute()->getStoreId();
        $this->_preloadOptions($storeId);
        $isMultiple = false;
        if (strpos($value, ',')) {
            $isMultiple = true;
            $value = explode(',', $value);
        }


        if ($isMultiple) {
            $values = array();
            foreach ($value as $item) {
                $key = self::_getCombinedKey($storeId, $item, 'store');
                if (isset(self::$_my_preloadedOptionHash[$key])) {
                    $values[] = self::$_my_preloadedOptionHash[$key];
                }
            }
            return $values;
        }

        $key = self::_getCombinedKey($storeId, $value, 'store');

        if (isset(self::$_my_preloadedOptionHash[$key])) {
            return self::$_my_preloadedOptionHash[$key];
        }
        return false;
    }
}
