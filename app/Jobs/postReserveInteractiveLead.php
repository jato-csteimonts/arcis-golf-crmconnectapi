<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Illuminate\Support\Facades\Log;

use App\ReserveInteractive;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7;


class postReserveInteractiveLead implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $requestName;
    protected $json;
    protected $lead_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($requestName, $json, $lead_id)
    {
        $this->requestName = $requestName;
        $this->json = $json;
        $this->lead_id = $lead_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // create a guzzle client
        $client = new Client([
            'base_uri' => env('RESERVE_INTERACTIVE_BASE_URI'),
            'timeout' => 30.0,
        ]);

        // builds an array as expected by the Reserve Interactive API
        $arr = [
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
        ];

        print_r($arr);

        // fires the POST request to store the data on the Reserve Interactive API CRM
        $ri = new ReserveInteractive();

        try {
            $r = $client->request('POST', '', $arr);
            $ri->lead_id = $this->lead_id;
            $ri->request_name = $this->requestName;
            $ri->request_json = (json_encode($this->json)) ? json_encode($this->json) : json_encode(['error']);
            $ri->response = $r->getBody();
            print_r($r->getBody());
            $ri->save();

        } catch (\Exception $e) {
            $ri->lead_id = 0;
            $ri->request_name = 'error';
            $ri->request_json = (json_encode($this->json)) ? json_encode($this->json) : json_encode(['error']);
            $ri->response = $e->getResponse()->getBody()->getContents();
            $ri->save();
        }


    }
}
