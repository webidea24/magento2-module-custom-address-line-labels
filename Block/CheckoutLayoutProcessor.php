<?php declare(strict_types=1);


namespace Webidea24\CustomAddressLineLabels\Block;


use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Store\Model\ScopeInterface;

class CheckoutLayoutProcessor implements LayoutProcessorInterface
{
    /**
     * @var \Magento\Framework\Stdlib\ArrayManager
     */
    private $arrayManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(ArrayManager $arrayManager, ScopeConfigInterface $scopeConfig)
    {
        $this->arrayManager = $arrayManager;
        $this->scopeConfig = $scopeConfig;
    }

    public function process($jsLayout)
    {
        $streetPaths = $this->arrayManager->findPaths('street', $jsLayout);

        $config = $this->scopeConfig->getValue('wi24_custom_address_line_labels/general', ScopeInterface::SCOPE_STORE);
        foreach ($streetPaths as $streetPath) {
            $jsLayout = $this->arrayManager->remove($streetPath . '/label', $jsLayout);
            $childrenCount = count($this->arrayManager->get($streetPath . '/children', $jsLayout));

            for ($i = 0; $i < $childrenCount; $i++) {
                $key = 'line_' . ($i + 1);
                if (isset($config[$key]) && !empty($config[$key])) {
                    if ($i === 0) {
                        // main field
                        $path = $streetPath . '/label';
                    } else {
                        // additional fields
                        $path = $streetPath . '/children/' . $i . '/label';
                    }

                    $jsLayout = $this->arrayManager->set($path, $jsLayout, __($config[$key]));
                }
            }
        }

        return $jsLayout;
    }
}
