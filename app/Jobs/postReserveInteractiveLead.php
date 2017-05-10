<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class postReserveInteractiveLead implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $requestName;
    protected $json;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($requestName, $json)
    {
        $this->requestName = $requestName;
        $this->json = $json;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $r = $this->client->request('POST', '', [
                'auth' => [
                    env('RESERVE_INTERACTIVE_USERNAME'),
                    env('RESERVE_INTERACTIVE_PASSWORD')
                ],
                'query' => [
                    'requestName' => $this->requestName,
                    'requestGuid' => md5(date('YmdHis')),
                    'mode' => 'apply'
                ],
                'json' => $this->json
            ]);
            $body = json_decode($r->getBody());
            print_r($body);
            die();
        } catch (\Exception $e) {
            Log::info('guzzle error: ' . $e->getMessage());
            echo($e->getMessage());
            die();
        }
    }
}
