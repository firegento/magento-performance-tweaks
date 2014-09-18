<?php

class Quafzi_PerformanceTweaks_Test_Model_Catalog_Resource_Product_Type_Configurable_Product_CollectionTest
    extends EcomDev_PHPUnit_Test_Case_Config
{
    public function setUp()
    {
        $this->orig = new Mage_Catalog_Model_Resource_Product_Type_Configurable_Product_Collection();
        $this->sut  = new Quafzi_PerformanceTweaks_Model_Catalog_Resource_Product_Type_Configurable_Product_Collection();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(
            'Mage_Catalog_Model_Resource_Product_Type_Configurable_Product_Collection',
            $this->sut
        );
    }

    public function testRewrite()
    {
        $this->assertInstanceOf(
            'Mage_Catalog_Model_Resource_Product_Type_Configurable_Product_Collection',
            Mage::getResourceModel('catalog/product_type_configurable_product_collection')
        );
        $this->assertInstanceOf(
            'Quafzi_PerformanceTweaks_Model_Catalog_Resource_Product_Type_Configurable_Product_Collection',
            Mage::getResourceModel('catalog/product_type_configurable_product_collection')
        );
    }
}
