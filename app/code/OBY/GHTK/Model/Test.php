<?php

namespace OBY\GHTK\Model;

use OBY\GHTK\Api\CustomInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Test implements CustomInterface
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    protected $logger;

    public function __construct(
        LoggerInterface $logger,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @inheritdoc
     */
    public function getData($value)
    {
        $response = ['success' => false];
        try {
            // Implement Your Code here
            $response = ['success' => true, 'message' => $value, 'config' => $this->getConfigValue('speedsms/secret_key')];
        } catch (\Exception $e) {
            $response = ['success' => false, 'message' => $e->getMessage()];
            $this->logger->info($e->getMessage());
        }
        $returnArray = json_encode($response);
        return $returnArray;
    }

    /**
     * Get the value of a configuration option.
     *
     * @return mixed
     */
    public function getConfigValue($path)
    {
        return $this->scopeConfig->getValue($path);
    }
}
