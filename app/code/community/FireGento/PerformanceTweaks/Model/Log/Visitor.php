<?php


/**
 * FireGento_PerformanceTweaks_Model_Log_Visitor
 *
 * @category FireGento
 * @package  FireGento_PerformanceTweaks
 * @author   Thomas Hampe <github@hampe.co>
 * @license  http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 */
class FireGento_PerformanceTweaks_Model_Log_Visitor extends Mage_Log_Model_Visitor
{

    /**
     * Returns a "fake" visitor id for product compare
     *
     * @return int
     */
    public function getId()
    {
        if (!$this->getData('visitor_id')) {
            /** @var FireGento_PerformanceTweaks_Model_Session $session */
            $session = Mage::getModel('firegento_performancetweaks/session');
            $this->setId($session->getVisitorId());
        }
        return parent::getId();
    }

    public function getVisitorId()
    {
        return $this->getId();
    }


}