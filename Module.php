<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\payment;

use Yii;
use yii\base\InvalidParamException;

/**
 * Class Module
 * Example application configuration:
 *
 * ```php
 * 'modules' => [
 *     'payment' => [
 *         'class'         => 'yuncms\payment\Module',
 *         'notifyPage'    => '/my/notify/page',
 *         'collection'    => [
 *             'PayPal' => [
 *                 'purse'     => $params['paypal_purse'],
 *                 'secret'    => $params['paypal_secret'],   /// NEVER keep secret in source control
 *             ],
 *             'webmoney_usd' => [
 *                 'gateway'   => 'WebMoney',
 *                 'purse'     => $params['webmoney_purse'],
 *                 'secret'    => $params['webmoney_secret'], /// NEVER keep secret in source control
 *             ],
 *         ],
 *     ],
 * ],
 * ```
 * @package Payment
 */
class Module extends \yii\base\Module
{

    /**
     * Internal storage for all available gateways
     *
     * @var array
     */
    private $_gateways = [];

    /**
     * 设置网关
     * @param array $gateways list of gateways
     */
    public function setGateways(array $gateways)
    {
        $this->_gateways = $gateways;
    }

    /**
     * 获取网关列表
     * @return BaseGateway[] list of gateways.
     */
    public function getGateways()
    {
        $gateways = [];
        foreach ($this->_gateways as $id => $gateway) {
            $gateways[$id] = $this->getGateway($id);
        }
        return $gateways;
    }

    /**
     * 获取指定网关
     * @param string $id gateway id.
     * @return BaseGateway gateway instance.
     * @throws InvalidParamException on non existing gateway request.
     */
    public function getGateway($id)
    {
        if (!array_key_exists($id, $this->_gateways)) {
            throw new InvalidParamException("Unknown gateway '{$id}'.");
        }
        if (!is_object($this->_gateways[$id])) {
            $this->_gateways[$id] = $this->createGateway($id, $this->_gateways[$id]);
        }
        return $this->_gateways[$id];
    }

    /**
     * 检查指定网关是否存在
     * @param string $id gateway id.
     * @return boolean whether gateway exist.
     */
    public function hasGateway($id)
    {
        return array_key_exists($id, $this->_gateways);
    }

    /**
     * 从配置创建网关实例
     * @param string $id api gateway id.
     * @param array $config gateway instance configuration.
     * @return object|GatewayInterface gateway instance.
     */
    protected function createGateway($id, $config)
    {
        $config['id'] = $id;
        return Yii::createObject($config);
    }
}