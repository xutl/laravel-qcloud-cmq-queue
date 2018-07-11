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
use XuTL\QCloud\Cmq\Client;
use XuTL\QCloud\Cmq\Requests\BatchReceiveMessageRequest;

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
        $client = new Client($config['endpoint'], $config['secret_Id'], $config['secret_Key']);
        $queue = $client->getQueueRef($queue);
        $hasMessage = true;
        while ($hasMessage) {
            $request = new BatchReceiveMessageRequest();
            $request->setNumOfMsg(16);
            $response = $queue->batchReceiveMessage($request);
            $handles = [];

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
