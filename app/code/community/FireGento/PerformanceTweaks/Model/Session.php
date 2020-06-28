<?php
/**
 * Created by PhpStorm.
 * User: thomas
 * Date: 05.03.17
 * Time: 10:49
 */

/**
 * FireGento_PerformanceTweaks_Model_Session
 *
 * @category FireGento
 * @package  FireGento_PerformanceTweaks
 * @author   Thomas Hampe <github@hampe.co>
 * @license  http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 */
class FireGento_PerformanceTweaks_Model_Session extends Mage_Core_Model_Session_Abstract
{


    public function __construct()
    {
        $this->init('firegento_performancetweaks');
    }

    public function getVisitorId()
    {
        if (!$this->hasData('visitor_id')) {
            $this->setData('visitor_id', $this->_generateVisitorId());
        }
        return $this->getData('visitor_id');
    }

    protected function _generateVisitorId()
    {
        return $this->getSessionId() ? crc32($this->getSessionId()) : null;
    }

}