<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LogBarierGate;
use Illuminate\Support\Facades\Http;

class ApiTimbanganController extends Controller
{
    public function getBearier1(Request $request)
    {
        try {
            $action = !empty($request->action) ? $request->action : "";
            $endpoint = env('ENDPOINT_URL');

            if ($request->from) {
                try {
                    if ($request->tipe == 'close') {
                        $response = Http::get("{$endpoint}?action_close={$action}");
                    } else {
                        $response = Http::get("{$endpoint}?Action={$action}");
                    }

                    $data = $response->body();

                    return $data;
                } finally {
                    $name = auth()->user()->fullname;
                    $log = new LogBarierGate();
                    $log->plant = "-";
                    $log->sequence = "-";
                    $log->arrival_date = date('Y-m-d');
                    $log->date_at = date('Y-m-d');
                    if ($request->tipe == 'close') {
                        $log->status = $action == 'BG1' ? "close gate 1 - by Dashboard Web | {$name}" : ($action == 'BG2' ? "close gate 2 - by Dashboard Web | {$name}" : "");
                        $log->next_status = $action == 'BG1' ? "close gate 1 - by Dashboard Web | {$name}" : ($action == 'BG2' ? "close gate 2 - by Dashboard Web | {$name}" : "");
                    } else {
                        $log->status = $action == 'BG1' ? "open gate 1 - by Dashboard Web | {$name}" : ($action == 'BG2' ? "open gate 2 - by Dashboard Web | {$name}" : "");
                        $log->next_status = $action == 'BG1' ? "open gate 1 - by Dashboard Web | {$name}" : ($action == 'BG2' ? "open gate 2 - by Dashboard Web | {$name}" : "");
                    }
                    $log->code_bg = 0;
                    $log->save();
                }
            } else {
                if ($request->tipe == 'close') {
                    $response = Http::get("{$endpoint}?action_close={$action}");
                } else {
                    $response = Http::get("{$endpoint}?Action={$action}");
                }

                $data = $response->body();

                return $data;
            }
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
