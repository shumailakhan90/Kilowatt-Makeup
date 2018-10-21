<?php

namespace Plazathemes\Quickview\Controller\Product;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class View extends \Magento\Catalog\Controller\Product\View
{
    /**
     * @var \Magento\Catalog\Helper\Product\View
     */
    protected $viewHelper;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * Catalog session
     *
     * @var \Magento\Catalog\Model\Session
     */
    protected $_catalogSession;

    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Catalog product
     *
     * @var \Magento\Catalog\Helper\Product
     */
    protected $_catalogProduct = null;

    /**
     * Constructor
     *
     * @param Context $context
     * @param \Magento\Catalog\Helper\Product $catalogProduct
     * @param \Magento\Catalog\Helper\Product\View $viewHelper
     * @param \Magento\Catalog\Model\Session $catalogSession
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Magento\Catalog\Helper\Product\View $viewHelper,
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Catalog\Helper\Product $catalogProduct,
        \Magento\Catalog\Model\Design $catalogDesign,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        PageFactory $resultPageFactory
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_catalogSession = $catalogSession;
        $this->_catalogProduct = $catalogProduct;
        $this->_catalogDesign = $catalogDesign;
        parent::__construct($context, $viewHelper, $resultForwardFactory, $resultPageFactory);
    }

    public function execute()
    {
        $quickview_action = $this->getRequest()->getParam('qv_action');

        if($quickview_action) {

            $json = array();

            // Get initial data from request
            $categoryId = (int) $this->getRequest()->getParam('category', false);
            $productId = (int) $this->getRequest()->getParam('id');
            $specifyOptions = $this->getRequest()->getParam('options');



            // Prepare helper and params
            $params = new \Magento\Framework\DataObject();
            $params->setCategoryId($categoryId);
            $params->setSpecifyOptions($specifyOptions);

            try {
                $product = $this->_initProduct();
                $page = $this->resultPageFactory->create(false, ['isIsolated' => true]);

                $settings = $this->_catalogDesign->getDesignSettings($product);
                $pageConfig = $page->getConfig();

                if ($settings->getCustomDesign()) {
                    $this->_catalogDesign->applyCustomDesign($settings->getCustomDesign());
                }

                // Apply custom page layout
                if ($settings->getPageLayout()) {
                    $pageConfig->setPageLayout($settings->getPageLayout());
                }

                $urlSafeSku = rawurlencode($product->getSku());

                // Load default page handles and page configurations
                if ($params && $params->getBeforeHandles()) {
                    foreach ($params->getBeforeHandles() as $handle) {
                        $page->addPageLayoutHandles(
                            ['id' => $product->getId(), 'sku' => $urlSafeSku, 'type' => $product->getTypeId()],
                            $handle
                        );
                    }
                }

                $page->addPageLayoutHandles(
                    ['id' => $product->getId(), 'sku' => $urlSafeSku, 'type' => $product->getTypeId()]
                );

                if ($params && $params->getAfterHandles()) {
                    foreach ($params->getAfterHandles() as $handle) {
                        $page->addPageLayoutHandles(
                            ['id' => $product->getId(), 'sku' => $urlSafeSku, 'type' => $product->getTypeId()],
                            $handle
                        );
                    }
                }

                // Apply custom layout update once layout is loaded
                $update = $page->getLayout()->getUpdate();
                $layoutUpdates = $settings->getLayoutUpdates();
                if ($layoutUpdates) {
                    if (is_array($layoutUpdates)) {
                        foreach ($layoutUpdates as $layoutUpdate) {
                            $update->addUpdate($layoutUpdate);
                        }
                    }
                }

                $controllerClass = $this->_request->getFullActionName();
                if ($controllerClass != 'catalog-product-view') {
                    $pageConfig->addBodyClass('catalog-product-view');
                }
                $pageConfig->addBodyClass('product-' . $product->getUrlKey());

//                $this->viewHelper->prepareAndRender($page, $productId, $this, $params);
                $page->getLayout()->addOutputElement('content')->removeOutputElement('root');

                $product_info = $page->getLayout()->getOutput();

                $json['product_info'] = $product_info;

                $this->getResponse()->representJson(
                    $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($json)
                );

//                $json['html'] = $page->getLayout()->getOutput();
//                return $page;
            } catch (\Exception $e) {
                return false;
            }
        } else {
            return parent::execute(); // TODO: Change the autogenerated stub
        }
    }
}