<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Plazathemes\Themeoptions\Observer;

use Magento\Framework\Event\ObserverInterface;

class SavePlazathemesSettings implements ObserverInterface
{
	/**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;
	
	/**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
	
    protected $_themeCollectionFactory;
	
	
    protected $_messageManager;
    
    /**
     * @param \Magento\Backend\Helper\Data $backendData
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Framework\App\ResponseInterface $response
	 * @param \Magento\Theme\Model\ResourceModel\Theme\Collection $resourceCollection
     */
    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Theme\Model\ThemeFactory $themeFactory
    ) {
        $this->_messageManager = $messageManager;
		$this->_scopeConfig = $scopeConfig;
		$this->_storeManager = $storeManager;
		$this->_themeFactory = $themeFactory;
    }

    /**
     * Log out user and redirect to new admin custom url
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.ExitExpression)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {		
		$websiteId = $observer->getData("website");
		
		$storeId = $observer->getData("store");
		
		 if(!$websiteId && !$storeId) {
            $websites = $this->_storeManager->getWebsites(false, false);
            foreach ($websites as $id => $value) {
                $this->generateWebsiteCss($id);
            }
        } else {
            if($storeId) {
                $this->generateStoreCss($storeId);
            } else {
                $this->generateWebsiteCss($websiteId);
            }
        }
    }
	
	 protected function generateWebsiteCss($websiteId) {
        $website = $this->_storeManager->getWebsite($websiteId);
        foreach($website->getStoreIds() as $storeId){
            $this->generateStoreCss($storeId);
        }
    }
	
	protected function generateStoreCss($storeId)
	{
		$store = $this->_storeManager->getStore($storeId);
		
		$themes = $this->_themeFactory
			->create()
			->getCollection()->addFieldToFilter('theme_id', $this->getDesign($storeId));
			
		foreach($themes as $theme)
		$themepath = $theme->getThemePath();

		/*general*/
		$css = "";
		if($this->getConfig('general/custom',$storeId))
		{
			$css .= $this->getCss('backgroundcolor','general/bg_color',$store);
			$css .= $this->getCss('textcolor','general/text_color',$store);
			$css .= $this->getCss('linkcolor','general/link_color',$store);
			$css .= $this->getCss('linkhovercolor','general/link_hover_color',$store);
			$css .= $this->getCss('primarycolor','general/primary_color',$store);
			$css .= $this->getCss('bordercolor','general/border_color',$store);
			$css .= $this->getCss('headingcolor','general/heading_color',$store);
			$css .= $this->getCss('menucolor','general/menu_color',$store);
			$css .= $this->getCss('labelbg','general/bg_label',$store);
		}
		
		/*header*/
		if($this->getConfig('header/custom',$storeId))
		{
			$css .= $this->getCss('headerbg','header/header_bg',$store);
			$css .= $this->getCss('headercolor','header/header_color',$store); 
		}
		 
		/*footer*/
		if($this->getConfig('footer/custom',$storeId))
		{
			$css .= $this->getCss('footerbg','footer/footer_bg',$store);
			$css .= $this->getCss('footercolor','footer/footer_color',$store);
			$css .= $this->getCss('footerlinkcolor','footer/footer_link_color',$store);
			$css .= $this->getCss('footerlinkhovercolor','footer/footer_link_hover_color',$store);
		} 
		
		$dirPath = BP.'/app/design/frontend/'.$themepath.'/web/css/source/';
		$filePath = $dirPath.'_options.less';
		
		try {
			if(!file_exists($dirPath)) {
				@mkdir($dirPath, 0777);
			}
			$file = @fopen($filePath,"w+");
			@flock($file, LOCK_EX);
			@fwrite($file,$css);
			@flock($file, LOCK_UN);
			@fclose($file);
		} catch (\Exception $e) {
			$this->_messageManager->addError(__($e->getMessage()));
		}
	}
    
	private function getCss($name,$value,$store)
	{
		if($this->getConfig($value,$store->getId()))
			return "@$name:#". $this->getConfig($value,$store->getId()).";\n";
		return "";
	}
	
	public function getConfig($field,$storeId)
	{
		return $this->_scopeConfig->getValue('plazathemes_design/'.$field, \Magento\Store\Model\ScopeInterface::SCOPE_STORE,$storeId);
	}
	
	public function getDesign($storeId)
	{
		return $this->_scopeConfig->getValue('design/theme/theme_id', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,$storeId);
	}
}
