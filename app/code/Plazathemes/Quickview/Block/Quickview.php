<?php

namespace Plazathemes\Quickview\Block;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;

class Quickview extends \Magento\Framework\View\Element\Template
{
    const XML_PATH_CONFIG_ENABLED_MODULE        = "quickview/general/enabled";
    const XML_PATH_CONFIG_INSERTION             = "quickview/general/insertion";
    const XML_PATH_CONFIG_LOADING_IMAGE         = "quickview/general/loading_image";
    const XML_PATH_CONFIG_QUICKVIEW_INIT        = "quickview/general/quickview_init";

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
		\Magento\Framework\Serialize\Serializer\Json $serialize,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
		$this->serialize = $serialize;
    }

    /**
     * Check status of module
     *
     * @return bool
     */
    public function isEnabledModule() {
        $isEnabled = false;
        $config = $this->_scopeConfig->getValue(self::XML_PATH_CONFIG_ENABLED_MODULE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if($config == 1) $isEnabled = true;
        return $isEnabled;
    }

    /**
     * Get Insert Button Direction
     *
     * @return string
     */
    public function getInsertionPosition() {
        $insertion = "after";
        $config = $this->_scopeConfig->getValue(self::XML_PATH_CONFIG_INSERTION, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if($config) $insertion = $config;
        return $insertion;
    }

    /**
     * Get Ajax loading image
     *
     * @return string
     */
    public function getLoadingImage() {
        $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $loadingImgFolder = "catalog/quickview/images/";
        $loadingImgValue = $this->_scopeConfig->getValue(self::XML_PATH_CONFIG_LOADING_IMAGE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $loadingImgUrl = $mediaUrl . $loadingImgFolder .$loadingImgValue;
        return $loadingImgUrl;
    }

    public function getQuickviewInit() {
        $config = $this->_scopeConfig->getValue(self::XML_PATH_CONFIG_QUICKVIEW_INIT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$value = $this->serialize->unserialize($config);
        return $value;
    }
}