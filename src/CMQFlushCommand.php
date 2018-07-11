<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace XuTL\QCloud\Cmq\Queue;

use Exception;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class CMQFlushCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'queue:cmq:flush';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Flush CMQ Queue';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $queue = $this->argument('queue');
        $connection = $this->option('connection');
        $config = config("queue.connections.{$connection}");
        if (!$queue) {
            $queue = $config['queue'];
        }
        $client = new Client($config['endpoint'], $config['key'], $config['secret']);
        $queue = $client->getQueueRef($queue);
        $hasMessage = true;
        while ($hasMessage) {
            $this->info('Peeking messages (Polling...)');
            try {
                $response = $queue->batchPeekMessage(15);
                if ($response->getMessages()) {
                    $hasMessage = true;
                } else {
                    $hasMessage = false;
                }
            } catch (Exception $e) {
                $this->info('no messages');
                break;
            }
            $response = $queue->batchReceiveMessage(new BatchReceiveMessageRequest(15, 30));
            $handles = [];
            /**
             * @var  \AliyunMNS\Model\Message $message
             */
            foreach ($response->getMessages() as $message) {
                $handles[] = $message->getReceiptHandle();
            }
            $response = $queue->batchDeleteMessage($handles);
            if ($response->isSucceed()) {
                foreach ($handles as $handle) {
                    $this->info(sprintf("The message: %s deleted success", $handle));
                }
            }
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['queue', InputArgument::OPTIONAL, 'The queue name'],
        ];
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['connection', 'c', InputOption::VALUE_OPTIONAL, 'The Queue connection name', 'cmq']
        ];
    }
}
