<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace XuTL\QCloud\Cmq\Queue;

use XuTL\QCloud\Cmq\Client;
use Illuminate\Queue\Connectors\ConnectorInterface;

class CMQConnector implements ConnectorInterface
{
    /**
     * Establish a queue connection.
     *
     * @param array $config
     *
     * @return \Illuminate\Contracts\Queue\Queue
     */
    public function connect(array $config)
    {
        return new CMQQueue($this->getAdaptor($config), $config['queue'], array_get($config, 'wait_seconds'));
    }

    /**
     * @param array $config
     *
     * @return mixed
     */
    protected function getEndpoint(array $config)
    {
        return str_replace('(s)', 's', $config['endpoint']);
    }

    /**
     * @param array $config
     *
     * @return Client
     */
    protected function getClient(array $config)
    {
        return new Client($this->getEndpoint($config), $config['secret_Id'], $config['secret_Key']);
    }

    /**
     * @param array $config
     *
     * @return CMQAdapter
     */
    protected function getAdaptor(array $config)
    {
        return new CMQAdapter($this->getClient($config));
    }
}
