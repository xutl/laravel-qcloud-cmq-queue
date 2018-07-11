<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace XuTL\QCloud\Cmq\Queue;

use XuTL\QCloud\Cmq\AsyncCallback;
use XuTL\QCloud\Cmq\Client;
use XuTL\QCloud\Cmq\Http\Promise;
use XuTL\QCloud\Cmq\Queue;
use XuTL\QCloud\Cmq\Requests\BatchDeleteMessageRequest;
use XuTL\QCloud\Cmq\Requests\BatchReceiveMessageRequest;
use XuTL\QCloud\Cmq\Requests\BatchSendMessageRequest;
use XuTL\QCloud\Cmq\Requests\SendMessageRequest;
use XuTL\QCloud\Cmq\Responses\BatchDeleteMessageResponse;
use XuTL\QCloud\Cmq\Responses\BatchReceiveMessageResponse;
use XuTL\QCloud\Cmq\Responses\BatchSendMessageResponse;
use XuTL\QCloud\Cmq\Responses\SendMessageResponse;

/**
 * Class CMQAdapter
 *
 * @method string getUsing()
 * @method SendMessageResponse sendMessage( SendMessageRequest $request )
 * @method Promise sendMessageAsync( SendMessageRequest $request, AsyncCallback $callback = null )
 * @method Promise peekMessageAsync( AsyncCallback $callback = null )
 * @method Promise receiveMessageAsync( AsyncCallback $callback = null )
 * @method deleteMessage( string $receiptHandle)
 * @method Promise deleteMessageAsync( string $receiptHandle, AsyncCallback $callback = null )
 * @method BatchSendMessageResponse batchSendMessage( BatchSendMessageRequest $request )
 * @method Promise batchSendMessageAsync( BatchSendMessageRequest $request, AsyncCallback $callback = null )
 * @method BatchReceiveMessageResponse batchReceiveMessage( BatchReceiveMessageRequest $request )
 * @method Promise batchReceiveMessageAsync( BatchReceiveMessageRequest $request, AsyncCallback $callback = null )
 * @method BatchDeleteMessageResponse batchDeleteMessage( BatchDeleteMessageRequest $request )
 * @method Promise batchDeleteMessageAsync( BatchDeleteMessageRequest $request, AsyncCallback $callback = null )
 */
class CMQAdapter
{
    /**
     * QCloud CMQ Client
     *
     * @var Client
     */
    private $client;

    /**
     * QCloud CMQ SDK Queue.
     *
     * @var Queue
     */
    private $queue;

    /**
     * @var string
     */
    private $using;

    /**
     * CMQAdapter constructor.
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param $method
     * @param $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array([ $this->queue, $method ], $parameters);
    }

    /**
     * @param string $queue
     *
     * @return self
     */
    public function useQueue($queue)
    {
        if ($this->using != $queue) {
            $this->using = $queue;
            $this->queue = $this->client->getQueueRef($queue);
        }
        return $this;
    }
}
