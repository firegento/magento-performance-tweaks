<?php
/**
 * basic rewrite test
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
class FireGento_PerformanceTweaks_Test_Model_Catalog_Resource_Product_Type_Configurable_Product_CollectionTest
    extends EcomDev_PHPUnit_Test_Case_Config
{
    public function setUp()
    {
        $this->orig = new Mage_Catalog_Model_Resource_Product_Type_Configurable_Product_Collection();
        $this->sut  = new FireGento_PerformanceTweaks_Model_Catalog_Resource_Product_Type_Configurable_Product_Collection();
    }

    /**
     * test if rewrites work
     */
    public function testRewrite()
    {
        $this->assertInstanceOf(
            'Mage_Catalog_Model_Resource_Product_Type_Configurable_Product_Collection',
            Mage::getResourceModel('catalog/product_type_configurable_product_collection')
        );
        $this->assertInstanceOf(
            'FireGento_PerformanceTweaks_Model_Catalog_Resource_Product_Type_Configurable_Product_Collection',
            Mage::getResourceModel('catalog/product_type_configurable_product_collection')
        );
    }
}
