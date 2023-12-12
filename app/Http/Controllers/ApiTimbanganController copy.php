<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;

class ApiTimbanganController extends Controller
{
    public function getBearier1(Request $request)
    {
        try {
            $action = !empty($request->action) ? $request->action : "";
            $endpoint = env('ENDPOINT_URL');
            $client = new Client();

            if ($request->tipe == 'close') {
                // $response = Http::get("{$endpoint}?action_close={$action}");
                $response = new \GuzzleHttp\Psr7\Request('GET', "{$endpoint}?action_close={$action}");
            } else {
                // $response = Http::get("{$endpoint}?Action={$action}");
                $response = new \GuzzleHttp\Psr7\Request('GET', "{$endpoint}?Action={$action}");
            }

            $promise = $client->sendAsync($response)->then(function ($res) {
                echo $res->getBody();
            });
            $promise->wait();

            // $status_code = $client->getStatusCode();

            // $data = $client->body();


        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function getTestChart1()
    {
        $dataTest = [
            ["2023-04-01",  254722.1],
            ["2023-04-08",  292175.1],
            ["2023-04-15",  369565],
            ["2023-04-20",  284918.9],
            ["2023-04-21",  325574.7],
            ["2023-04-23",  254689.8],
            ["2023-04-25",  303909],
            ["2023-04-28",  335092.9],
            ["2023-04-30",  408128],
            ["2023-05-01",  300992.2],
            ["2023-05-02",  401911.5],
            ["2023-05-05",  299009.2],
            ["2023-05-08",  319814.4],
            ["2023-05-10",  357303.9],
            ["2023-05-12",  353838.9],
            ["2023-05-28",  288386.5],
            ["2023-06-01",  485058.4],
            ["2023-06-05",  326794.4],
            ["2023-06-20",  483812.3],
            ["2023-06-26",  254484]
        ];

        return json_encode($dataTest);
    }
}
