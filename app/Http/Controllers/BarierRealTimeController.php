<?php

namespace App\Http\Controllers;

use App\Events\BarierGateEvent;
use App\Helpers\CodeNumbering;
use App\Models\LogBarierGate;
use App\Models\RealBarier;
use App\Models\TrackStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;

class BarierRealTimeController extends Controller
{
    public function destroy(Request $request)
    {
        $plant = $request->plant;
        $sequence = $request->sequence;
        $arrival_date = $request->arrival_date;

        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'plant'     => 'required',
                'sequence'       => 'required',
                'arrival_date'     => 'required|date',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message'   => $validator->messages(),
                ], Response::HTTP_BAD_REQUEST);
            }

            $data = RealBarier::where('plant', $plant)->where('sequence', $sequence)->where('arrival_date', $arrival_date)->first();
            if (empty($data)) {
                return response()->json([
                    'status' => 'error',
                    'message'   => 'Data not found!',
                ], Response::HTTP_BAD_REQUEST);
            } else {
                $data->delete();

                TrackStatus::where('plant', $plant)->where('sequence', $sequence)->where('arrival_date', $arrival_date)->delete();

                $log = new LogBarierGate();
                $log->code_bg = 0;
                $log->plant = $plant;
                $log->sequence = $sequence;
                $log->arrival_date = $arrival_date;
                $log->status = 'deleted';
                $log->next_status = 'deleted';
                $log->date_at = date('Y-m-d');
                $log->save();

                DB::commit();

                try {
                    return response()->json([
                        'status' => 'success',
                        'message'   => 'Data has been successfully deleted!',
                    ], 200);
                } finally {
                    BarierGateEvent::dispatch([
                        'status' => $log->next_status,
                        'action' => null
                    ]);
                }
            }
        } catch (QueryException $th) {
            DB::rollback();

            return response()->json([
                'status' => 'error',
                'message'   => $th->getMessage(),
            ], 422);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message'   => $th->getMessage(),
            ], 422);
        }
    }

    public function registrasi(Request $request)
    {
        $plant = $request->plant;
        $sequence = $request->sequence;
        $truck_no = $request->truck_no;
        $arrival_date = $request->arrival_date;
        $arrival_time = $request->arrival_time;
        $type_scenario = strtolower($request->type_scenario);
        $vendor_do = $request->vendor_do;
        $jenis_kendaraan = $request->jenis_kendaraan;
        $next_status = strtolower($request->next_status);
        $ship_to_party = $request->ship_to_party;
        $delivery_order_no = $request->delivery_order_no;
        $from_storage_location = $request->from_storage_location;
        $upto_storage_location = $request->upto_storage_location;
        $truck_type = $request->truck_type;
        $scaling_date_1 = $request->scaling_date_1;
        $scaling_time_1 = $request->scaling_time_1;
        $qty_scaling_1 = preg_replace("/[^0-9]/", "", $request->qty_scaling_1);
        $scaling_date_2 = $request->scaling_date_2;
        $scaling_time_2 = $request->scaling_time_2;
        $qty_scaling_2 = preg_replace("/[^0-9]/", "", $request->qty_scaling_2);

        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'plant'     => 'required',
                'sequence'       => 'required',
                'truck_no'     => 'required',
                'arrival_date'     => 'required|date',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message'   => $validator->messages(),
                ], Response::HTTP_BAD_REQUEST);
            }


            $rb = new RealBarier();
            $code_bg = CodeNumbering::custom_code($rb, 'code_bg');

            $rb->code_bg = $code_bg;
            $rb->plant = $plant;
            $rb->sequence = $sequence;
            $rb->truck_no = $truck_no;
            $rb->arrival_date = $arrival_date;
            $rb->arrival_time = $arrival_time;
            $rb->type_scenario = $type_scenario;
            $rb->vendor_do = $vendor_do;
            $rb->jenis_kendaraan = $jenis_kendaraan;
            $rb->status = $type_scenario == 'scaling others' ? 'registration scale' : 'registration';
            $rb->next_status = $next_status;
            $rb->ship_to_party = $ship_to_party;
            $rb->scaling_date_1 = $scaling_date_1;
            $rb->scaling_time_1 = $scaling_time_1;
            $rb->qty_scaling_1 = $qty_scaling_1;
            $rb->scaling_date_2 = $scaling_date_2;
            $rb->scaling_time_2 = $scaling_time_2;
            $rb->qty_scaling_2 = $qty_scaling_2;
            $rb->delivery_order_no = $delivery_order_no;
            $rb->from_storage_location = $from_storage_location;
            $rb->upto_storage_location = $upto_storage_location;
            $rb->truck_type = $truck_type;
            $rb->save();

            $log = new LogBarierGate();
            $log->code_bg = $code_bg;
            $log->plant = $plant;
            $log->sequence = $sequence;
            $log->truck_no = $truck_no;
            $log->arrival_date = $arrival_date;
            $log->ship_to_party = $ship_to_party;
            $log->arrival_time = $arrival_time;
            $log->type_scenario = $type_scenario;
            $log->vendor_do = $vendor_do;
            $log->jenis_kendaraan = $jenis_kendaraan;
            $log->status = $type_scenario == 'scaling others' ? 'registration scale' : 'registration';
            $log->next_status = $next_status;
            $log->date_at = date('Y-m-d');
            $log->save();

            $ts = new TrackStatus();
            $ts->plant = $plant;
            $ts->sequence = $sequence;
            $ts->arrival_date = $arrival_date;
            $ts->status = $type_scenario == 'scaling others' ? 'registration scale' : 'registration';
            $ts->save();

            $ts = new TrackStatus();
            $ts->plant = $plant;
            $ts->sequence = $sequence;
            $ts->arrival_date = $arrival_date;
            $ts->status = $next_status;
            $ts->save();

            DB::commit();

            try {
                return response()->json([
                    'status' => 'success',
                    'message'   => 'Data has been successfully saved!',
                ], 202);
            } finally {
                BarierGateEvent::dispatch([
                    'status'  => $next_status,
                    'action'  => null,
                    'truck_no' => $truck_no
                ]);
            }
        } catch (QueryException $th) {
            DB::rollback();

            return response()->json([
                'status' => 'error',
                'message'   => $th->getMessage(),
            ], 422);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message'   => $th->getMessage(),
            ], 422);
        }
    }

    public function timbangan(Request $request)
    {
        $action = $request->action;

        $plant = $request->plant;
        $sequence = $request->sequence;
        $truck_no = $request->truck_no;
        $arrival_date = $request->arrival_date;
        $arrival_time = $request->arrival_time;
        $type_scenario = strtolower($request->type_scenario);
        $vendor_do = $request->vendor_do;
        $jenis_kendaraan = $request->jenis_kendaraan;
        $next_status = strtolower($request->next_status);
        $scaling_date_1 = $request->scaling_date_1;
        $scaling_time_1 = $request->scaling_time_1;
        $qty_scaling_1 = preg_replace("/[^0-9]/", "", $request->qty_scaling_1);
        $scaling_date_2 = $request->scaling_date_2;
        $scaling_time_2 = $request->scaling_time_2;
        $qty_scaling_2 = preg_replace("/[^0-9]/", "", $request->qty_scaling_2);
        $ship_to_party = $request->ship_to_party;
        $delivery_order_no = $request->delivery_order_no;
        $from_storage_location = $request->from_storage_location;
        $upto_storage_location = $request->upto_storage_location;
        $truck_type = $request->truck_type;

        $endpoint = env('ENDPOINT_URL');

        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'plant'     => 'required',
                'sequence'       => 'required',
                'arrival_date'     => 'required|date',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message'   => $validator->messages(),
                ], Response::HTTP_BAD_REQUEST);
            }

            $cek_rb = RealBarier::where('plant', $plant)->where('sequence', $sequence)->where('arrival_date', $arrival_date)->first();
            if (!empty($action)) {
                if ($cek_rb) {
                    if (!empty($next_status)) {
                        $cek_rb->plant = $plant;
                        $cek_rb->sequence = $sequence;
                        $cek_rb->arrival_date = $arrival_date;
                        $cek_rb->truck_no = !empty($truck_no) ? $truck_no : $cek_rb->truck_no;
                        $cek_rb->arrival_time = !empty($arrival_time) ? $arrival_time : $cek_rb->arrival_time;
                        $cek_rb->type_scenario = !empty($type_scenario) ? $type_scenario : $cek_rb->type_scenario;
                        $cek_rb->vendor_do = !empty($vendor_do) ? $vendor_do : $cek_rb->vendor_do;
                        $cek_rb->jenis_kendaraan = !empty($jenis_kendaraan) ? $jenis_kendaraan : $cek_rb->jenis_kendaraan;
                        $cek_rb->status = $cek_rb->next_status;
                        $cek_rb->next_status = $next_status;
                        $cek_rb->scaling_date_1 = !empty($scaling_date_1) ? $scaling_date_1 : $cek_rb->scaling_date_1;
                        $cek_rb->scaling_time_1 = !empty($scaling_time_1) ? $scaling_time_1 : $cek_rb->scaling_time_1;
                        $cek_rb->qty_scaling_1 = !empty($qty_scaling_1) ? $qty_scaling_1 : $cek_rb->qty_scaling_1;
                        $cek_rb->scaling_date_2 = !empty($scaling_date_2) ? $scaling_date_2 : $cek_rb->scaling_date_2;
                        $cek_rb->scaling_time_2 = !empty($scaling_time_2) ? $scaling_time_2 : $cek_rb->scaling_time_2;
                        $cek_rb->qty_scaling_2 = !empty($qty_scaling_2) ? $qty_scaling_2 : $cek_rb->qty_scaling_2;
                        $cek_rb->ship_to_party = !empty($ship_to_party) ? $ship_to_party : $cek_rb->ship_to_party;
                        $cek_rb->delivery_order_no = !empty($delivery_order_no) ? $delivery_order_no : $cek_rb->delivery_order_no;
                        $cek_rb->from_storage_location = !empty($from_storage_location) ? $from_storage_location : $cek_rb->from_storage_location;
                        $cek_rb->upto_storage_location = !empty($upto_storage_location) ? $upto_storage_location : $cek_rb->upto_storage_location;
                        $cek_rb->truck_type = !empty($truck_type) ? $truck_type : $cek_rb->truck_type;
                        $cek_rb->update();

                        $ts = new TrackStatus();
                        $ts_arr = [
                            [
                                'plant' => $plant,
                                'sequence' => $sequence,
                                'arrival_date' => $arrival_date,
                                'status'    => $action == 'BG1' ? 'open gate 1' : ($action == 'BG2' ? 'open gate 2' : ""),
                                'created_at' => now(),
                                'updated_at' => now()
                            ],
                            [
                                'plant' => $plant,
                                'sequence' => $sequence,
                                'arrival_date' => $arrival_date,
                                'status' => $next_status,
                                'created_at' => now(),
                                'updated_at' => now()
                            ]
                        ];
                        $ts->insert($ts_arr);

                        $log = new LogBarierGate();
                        $log_arr = [
                            [
                                'plant' => $plant,
                                'sequence' => $sequence,
                                'truck_no' => $truck_no,
                                'arrival_date' => $arrival_date,
                                'arrival_time' => $arrival_time,
                                'type_scenario' => $type_scenario,
                                'vendor_do' => $vendor_do,
                                'jenis_kendaraan' => $jenis_kendaraan,
                                'status' => $action == 'BG1' ? 'open gate 1 - by SAP' : ($action == 'BG2' ? 'open gate 2 - by SAP' : ""),
                                'next_status' => $action == 'BG1' ? 'open gate 1 - by SAP' : ($action == 'BG2' ? 'open gate 2 - by SAP' : ""),
                                'scaling_date_1' => $scaling_date_1,
                                'scaling_time_1' => $scaling_time_1,
                                'qty_scaling_1' => $qty_scaling_1,
                                'scaling_date_2' => $scaling_date_2,
                                'scaling_time_2' => $scaling_time_2,
                                'qty_scaling_2' => $qty_scaling_2,
                                'ship_to_party' => $ship_to_party,
                                'delivery_order_no' => $delivery_order_no,
                                'from_storage_location' => $from_storage_location,
                                'upto_storage_location' => $upto_storage_location,
                                'code_bg' => $cek_rb->code_bg,
                                'date_at' => date('Y-m-d'),
                                'created_at' => now(),
                                'updated_at' => now()
                            ],
                            [
                                'plant' => $plant,
                                'sequence' => $sequence,
                                'truck_no' => $truck_no,
                                'arrival_date' => $arrival_date,
                                'arrival_time' => $arrival_time,
                                'type_scenario' => $type_scenario,
                                'vendor_do' => $vendor_do,
                                'jenis_kendaraan' => $jenis_kendaraan,
                                'status' => $next_status,
                                'next_status' => $next_status,
                                'scaling_date_1' => $scaling_date_1,
                                'scaling_time_1' => $scaling_time_1,
                                'qty_scaling_1' => $qty_scaling_1,
                                'scaling_date_2' => $scaling_date_2,
                                'scaling_time_2' => $scaling_time_2,
                                'qty_scaling_2' => $qty_scaling_2,
                                'ship_to_party' => $ship_to_party,
                                'delivery_order_no' => $delivery_order_no,
                                'from_storage_location' => $from_storage_location,
                                'upto_storage_location' => $upto_storage_location,
                                'code_bg' => $cek_rb->code_bg,
                                'date_at' => date('Y-m-d'),
                                'created_at' => now(),
                                'updated_at' => now()
                            ]
                        ];
                        $log->insert($log_arr);

                        Http::get("{$endpoint}?Action={$action}");

                        if ($action == 'BG1') {
                            DB::commit();

                            try {
                                return response()->json([
                                    'status' => 'success',
                                    'message'   => 'Data has been successfully updated, barrier gate 1 is open!',
                                ], 200);
                            } finally {
                                BarierGateEvent::dispatch([
                                    'status'  => $next_status,
                                    'action'  => 'open gate 1',
                                    'truck_no' => $truck_no
                                ]);
                            }
                        } else if ($action == 'BG2') {
                            DB::commit();

                            try {
                                return response()->json([
                                    'status' => 'success',
                                    'message'   => 'Data has been successfully updated, barrier gate 2 is open!',
                                ], 200);
                            } finally {
                                BarierGateEvent::dispatch([
                                    'status'  => $next_status,
                                    'action'  => 'open gate 2',
                                    'truck_no' => $truck_no
                                ]);
                            }
                        } else {
                            DB::rollBack();
                            return response()->json([
                                'status' => 'error',
                                'message'   => 'Barier gate is not found!',
                            ], 404);
                        }
                    } else {
                        $cek_rb->plant = $plant;
                        $cek_rb->sequence = $sequence;
                        $cek_rb->arrival_date = $arrival_date;
                        $cek_rb->truck_no = !empty($truck_no) ? $truck_no : $cek_rb->truck_no;
                        $cek_rb->arrival_time = !empty($arrival_time) ? $arrival_time : $cek_rb->arrival_time;
                        $cek_rb->type_scenario = !empty($type_scenario) ? $type_scenario : $cek_rb->type_scenario;
                        $cek_rb->vendor_do = !empty($vendor_do) ? $vendor_do : $cek_rb->vendor_do;
                        $cek_rb->jenis_kendaraan = !empty($jenis_kendaraan) ? $jenis_kendaraan : $cek_rb->jenis_kendaraan;
                        $cek_rb->status = $cek_rb->status;
                        $cek_rb->next_status = $cek_rb->next_status;
                        $cek_rb->scaling_date_1 = !empty($scaling_date_1) ? $scaling_date_1 : $cek_rb->scaling_date_1;
                        $cek_rb->scaling_time_1 = !empty($scaling_time_1) ? $scaling_time_1 : $cek_rb->scaling_time_1;
                        $cek_rb->qty_scaling_1 = !empty($qty_scaling_1) ? $qty_scaling_1 : $cek_rb->qty_scaling_1;
                        $cek_rb->scaling_date_2 = !empty($scaling_date_2) ? $scaling_date_2 : $cek_rb->scaling_date_2;
                        $cek_rb->scaling_time_2 = !empty($scaling_time_2) ? $scaling_time_2 : $cek_rb->scaling_time_2;
                        $cek_rb->qty_scaling_2 = !empty($qty_scaling_2) ? $qty_scaling_2 : $cek_rb->qty_scaling_2;
                        $cek_rb->ship_to_party = !empty($ship_to_party) ? $ship_to_party : $cek_rb->ship_to_party;
                        $cek_rb->delivery_order_no = !empty($delivery_order_no) ? $delivery_order_no : $cek_rb->delivery_order_no;
                        $cek_rb->from_storage_location = !empty($from_storage_location) ? $from_storage_location : $cek_rb->from_storage_location;
                        $cek_rb->upto_storage_location = !empty($upto_storage_location) ? $upto_storage_location : $cek_rb->upto_storage_location;
                        $cek_rb->truck_type = !empty($truck_type) ? $truck_type : $cek_rb->truck_type;
                        $cek_rb->update();

                        $ts_old = TrackStatus::where('plant', $plant)->where('sequence', $sequence)->where('arrival_date', $arrival_date)->orderBy('id', 'DESC')->first();

                        if (!empty($ts_old)) {
                            $status_ts_old = $ts_old->status;

                            $ts_limit_2 = TrackStatus::where('plant', $plant)->where('sequence', $sequence)->where('arrival_date', $arrival_date)->orderBy('id', 'DESC')->limit('2')->select('plant', 'sequence', 'arrival_date', 'status')->get();


                            $ts_data_old = [
                                [
                                    'plant' => $plant,
                                    'sequence' => $sequence,
                                    'arrival_date' => $arrival_date,
                                    'status' => $status_ts_old,
                                ],
                                [
                                    'plant' => $plant,
                                    'sequence' => $sequence,
                                    'arrival_date' => $arrival_date,
                                    'status' => $action == 'BG1' ? 'open gate 1' : ($action == 'BG2' ? 'open gate 2' : $next_status),
                                ],
                            ];

                            $ts_bool = $ts_limit_2->toArray() == $ts_data_old;
                            if (!$ts_bool) {
                                $ts_old->delete();
                                $ts_data = [
                                    [
                                        'plant' => $plant,
                                        'sequence' => $sequence,
                                        'arrival_date' => $arrival_date,
                                        'status' => $action == 'BG1' ? 'open gate 1' : ($action == 'BG2' ? 'open gate 2' : $next_status),
                                        'created_at' => now(),
                                        'updated_at' => now()
                                    ],
                                    [
                                        'plant' => $plant,
                                        'sequence' => $sequence,
                                        'arrival_date' => $arrival_date,
                                        'status' => $status_ts_old,
                                        'created_at' => now(),
                                        'updated_at' => now()
                                    ],
                                ];
                                TrackStatus::insert($ts_data);
                            } else {
                                // CEK KONDISI BOLEH MENAMPILKAN DATA OPEN GATE TRACK STATUS 2X, JIKA TYPE SCENARIONYA SCALLING OTHERS
                                if ($action == 'BG1') {
                                    $count_get = TrackStatus::where('plant', $plant)->where('sequence', $sequence)->where('arrival_date', $arrival_date)->where('status', 'open gate 1')->limit('3')->orderBy('id', 'DESC')->get();

                                    // if ($cek_rb->type_scenario == 'scaling others') {
                                    if (count($count_get) == 1) {
                                        $ts_old->delete();
                                        $ts_data = [

                                            [
                                                'plant' => $plant,
                                                'sequence' => $sequence,
                                                'arrival_date' => $arrival_date,
                                                'status' => $action == 'BG1' ? 'open gate 1' : ($action == 'BG2' ? 'open gate 2' : $next_status),
                                                'created_at' => now(),
                                                'updated_at' => now()
                                            ],
                                            [
                                                'plant' => $plant,
                                                'sequence' => $sequence,
                                                'arrival_date' => $arrival_date,
                                                'status' => $ts_old->status,
                                                'created_at' => now(),
                                                'updated_at' => now()
                                            ],
                                        ];
                                        TrackStatus::insert($ts_data);
                                    }
                                    // }
                                } else if ($action == 'BG2') {
                                    $count_get = TrackStatus::where('plant', $plant)->where('sequence', $sequence)->where('arrival_date', $arrival_date)->where('status', 'open gate 2')->limit('3')->orderBy('id', 'DESC')->get();

                                    // if ($cek_rb->type_scenario == 'scaling others') {
                                    if (count($count_get) == 1) {
                                        $ts_old->delete();
                                        $ts_data = [

                                            [
                                                'plant' => $plant,
                                                'sequence' => $sequence,
                                                'arrival_date' => $arrival_date,
                                                'status' => $action == 'BG1' ? 'open gate 1' : ($action == 'BG2' ? 'open gate 2' : $next_status),
                                                'created_at' => now(),
                                                'updated_at' => now()
                                            ],
                                            [
                                                'plant' => $plant,
                                                'sequence' => $sequence,
                                                'arrival_date' => $arrival_date,
                                                'status' => $ts_old->status,
                                                'created_at' => now(),
                                                'updated_at' => now()
                                            ],
                                        ];
                                        TrackStatus::insert($ts_data);
                                    }
                                    // }
                                } else {
                                    DB::rollBack();
                                    return response()->json([
                                        'status' => 'error',
                                        'message'   => 'Barier gate is not found!',
                                    ], 404);
                                }
                            }
                        }

                        $log = new LogBarierGate();
                        $log->plant = $plant;
                        $log->sequence = $sequence;
                        $log->truck_no = $truck_no;
                        $log->arrival_date = $arrival_date;
                        $log->arrival_time = $arrival_time;
                        $log->type_scenario = $type_scenario;
                        $log->vendor_do = $vendor_do;
                        $log->jenis_kendaraan = $jenis_kendaraan;
                        $log->status = $action == 'BG1' ? 'open gate 1 - by SAP' : ($action == 'BG2' ? 'open gate 2 - by SAP' : $next_status);
                        $log->next_status = $action == 'BG1' ? 'open gate 1 - by SAP' : ($action == 'BG2' ? 'open gate 2 - by SAP' : $next_status);
                        $log->scaling_date_1 = $scaling_date_1;
                        $log->scaling_time_1 = $scaling_time_1;
                        $log->qty_scaling_1 = $qty_scaling_1;
                        $log->scaling_date_2 = $scaling_date_2;
                        $log->scaling_time_2 = $scaling_time_2;
                        $log->qty_scaling_2 = $qty_scaling_2;
                        $log->ship_to_party = $ship_to_party;
                        $log->delivery_order_no = $delivery_order_no;
                        $log->from_storage_location = $from_storage_location;
                        $log->upto_storage_location = $upto_storage_location;
                        $log->code_bg = $cek_rb->code_bg;
                        $log->date_at = date('Y-m-d');
                        $log->save();

                        Http::get("{$endpoint}?Action={$action}");

                        if ($action == 'BG1') {
                            DB::commit();
                            try {
                                return response()->json([
                                    'status' => 'success',
                                    'message'   => 'Log data has been successfully saved, barrier gate 1 is open!',
                                ], 202);
                            } finally {
                                BarierGateEvent::dispatch([
                                    'status'  => $next_status,
                                    'action'  => 'open gate 1',
                                    'truck_no' => $truck_no
                                ]);
                            }
                        } else if ($action == 'BG2') {
                            DB::commit();

                            try {
                                return response()->json([
                                    'status' => 'success',
                                    'message'   => 'Log data has been successfully saved, barrier gate 2 is open!',
                                ], 202);
                            } finally {
                                BarierGateEvent::dispatch([
                                    'status'  => $next_status,
                                    'action'  => 'open gate 2',
                                    'truck_no' => $truck_no
                                ]);
                            }
                        } else {
                            DB::rollBack();
                            return response()->json([
                                'status' => 'error',
                                'message'   => 'Barier gate is not found!',
                            ], 404);
                        }
                    }
                } else {
                    if (!empty($next_status)) {
                        $rb = new RealBarier();
                        $code_bg = CodeNumbering::custom_code($rb, 'code_bg');

                        $rb->code_bg = $code_bg;
                        $rb->plant = $plant;
                        $rb->sequence = $sequence;
                        $rb->truck_no = $truck_no;
                        $rb->arrival_date = $arrival_date;
                        $rb->arrival_time = $arrival_time;
                        $rb->type_scenario = $type_scenario;
                        $rb->vendor_do = $vendor_do;
                        $rb->jenis_kendaraan = $jenis_kendaraan;
                        $rb->status = $next_status;
                        $rb->next_status = $next_status;
                        $rb->scaling_date_1 = $scaling_date_1;
                        $rb->scaling_time_1 = $scaling_time_1;
                        $rb->qty_scaling_1 = $qty_scaling_1;
                        $rb->scaling_date_2 = $scaling_date_2;
                        $rb->scaling_time_2 = $scaling_time_2;
                        $rb->qty_scaling_2 = $qty_scaling_2;
                        $rb->ship_to_party = $ship_to_party;
                        $rb->delivery_order_no = $delivery_order_no;
                        $rb->from_storage_location = $from_storage_location;
                        $rb->upto_storage_location = $upto_storage_location;
                        $rb->truck_type = $truck_type;
                        $rb->save();

                        $ts = new TrackStatus();
                        $ts_arr = [
                            [
                                'plant' => $plant,
                                'sequence' => $sequence,
                                'arrival_date' => $arrival_date,
                                'status'    => $action == 'BG1' ? 'open gate 1' : ($action == 'BG2' ? 'open gate 2' : ""),
                                'created_at' => now(),
                                'updated_at' => now()
                            ],
                            [
                                'plant' => $plant,
                                'sequence' => $sequence,
                                'arrival_date' => $arrival_date,
                                'status' => $next_status,
                                'created_at' => now(),
                                'updated_at' => now()
                            ]
                        ];
                        $ts->insert($ts_arr);

                        $log = new LogBarierGate();
                        $log_arr = [
                            [
                                'plant' => $plant,
                                'sequence' => $sequence,
                                'truck_no' => $truck_no,
                                'arrival_date' => $arrival_date,
                                'arrival_time' => $arrival_time,
                                'type_scenario' => $type_scenario,
                                'vendor_do' => $vendor_do,
                                'jenis_kendaraan' => $jenis_kendaraan,
                                'status' => $action == 'BG1' ? 'open gate 1 - by SAP' : ($action == 'BG2' ? 'open gate 2 - by SAP' : ""),
                                'next_status' => $action == 'BG1' ? 'open gate 1 - by SAP' : ($action == 'BG2' ? 'open gate 2 - by SAP' : ""),
                                'scaling_date_1' => $scaling_date_1,
                                'scaling_time_1' => $scaling_time_1,
                                'qty_scaling_1' => $qty_scaling_1,
                                'scaling_date_2' => $scaling_date_2,
                                'scaling_time_2' => $scaling_time_2,
                                'qty_scaling_2' => $qty_scaling_2,
                                'ship_to_party' => $ship_to_party,
                                'delivery_order_no' => $delivery_order_no,
                                'from_storage_location' => $from_storage_location,
                                'upto_storage_location' => $upto_storage_location,
                                'code_bg' => $cek_rb->code_bg,
                                'date_at' => date('Y-m-d'),
                                'created_at' => now(),
                                'updated_at' => now()
                            ],
                            [
                                'plant' => $plant,
                                'sequence' => $sequence,
                                'truck_no' => $truck_no,
                                'arrival_date' => $arrival_date,
                                'arrival_time' => $arrival_time,
                                'type_scenario' => $type_scenario,
                                'vendor_do' => $vendor_do,
                                'jenis_kendaraan' => $jenis_kendaraan,
                                'status' => $next_status,
                                'next_status' => $next_status,
                                'scaling_date_1' => $scaling_date_1,
                                'scaling_time_1' => $scaling_time_1,
                                'qty_scaling_1' => $qty_scaling_1,
                                'scaling_date_2' => $scaling_date_2,
                                'scaling_time_2' => $scaling_time_2,
                                'qty_scaling_2' => $qty_scaling_2,
                                'ship_to_party' => $ship_to_party,
                                'delivery_order_no' => $delivery_order_no,
                                'from_storage_location' => $from_storage_location,
                                'upto_storage_location' => $upto_storage_location,
                                'code_bg' => $cek_rb->code_bg,
                                'date_at' => date('Y-m-d'),
                                'created_at' => now(),
                                'updated_at' => now()
                            ]
                        ];
                        $log->insert($log_arr);

                        Http::get("{$endpoint}?Action={$action}");

                        if ($action == 'BG1') {
                            DB::commit();

                            try {
                                return response()->json([
                                    'status' => 'success',
                                    'message'   => 'Data has been successfully saved, barrier gate 1 is open!',
                                ], 202);
                            } finally {
                                BarierGateEvent::dispatch([
                                    'status'  => $next_status,
                                    'action'  => 'open gate 1',
                                    'truck_no' => $truck_no
                                ]);
                            }
                        } else if ($action == 'BG2') {
                            DB::commit();

                            try {
                                return response()->json([
                                    'status' => 'success',
                                    'message'   => 'Data has been successfully saved, barrier gate 2 is open!',
                                ], 202);
                            } finally {
                                BarierGateEvent::dispatch([
                                    'status'  => $next_status,
                                    'action'  => 'open gate 2',
                                    'truck_no' => $truck_no
                                ]);
                            }
                        } else {
                            DB::rollBack();
                            return response()->json([
                                'status' => 'error',
                                'message'   => 'Barier gate is not found!',
                            ], 404);
                        }
                    } else {
                        $ts_old = TrackStatus::where('plant', $plant)->where('sequence', $sequence)->where('arrival_date', $arrival_date)->orderBy('id', 'DESC')->first();

                        if (!empty($ts_old)) {
                            $status_ts_old = $ts_old->status;

                            TrackStatus::insert([
                                [
                                    'plant' => $plant,
                                    'sequence' => $sequence,
                                    'arrival_date' => $arrival_date,
                                    'status' => $action == 'BG1' ? 'open gate 1' : ($action == 'BG2' ? 'open gate 2' : $next_status),
                                    'created_at' => now(),
                                    'updated_at' => now()
                                ],
                                [
                                    'plant' => $plant,
                                    'sequence' => $sequence,
                                    'arrival_date' => $arrival_date,
                                    'status' => $status_ts_old,
                                    'created_at' => now(),
                                    'updated_at' => now()
                                ],
                            ]);

                            $ts_old->delete();
                        } else {
                            TrackStatus::insert([
                                [
                                    'plant' => $plant,
                                    'sequence' => $sequence,
                                    'arrival_date' => $arrival_date,
                                    'status' => $action == 'BG1' ? 'open gate 1' : ($action == 'BG2' ? 'open gate 2' : $next_status),
                                    'created_at' => now(),
                                    'updated_at' => now()
                                ],
                            ]);
                        }


                        $log = new LogBarierGate();
                        $log->plant = $plant;
                        $log->sequence = $sequence;
                        $log->truck_no = $truck_no;
                        $log->arrival_date = $arrival_date;
                        $log->arrival_time = $arrival_time;
                        $log->type_scenario = $type_scenario;
                        $log->vendor_do = $vendor_do;
                        $log->jenis_kendaraan = $jenis_kendaraan;
                        $log->status = $action == 'BG1' ? 'open gate 1 - by SAP' : ($action == 'BG2' ? 'open gate 2 - by SAP' : $next_status);
                        $log->next_status = $action == 'BG1' ? 'open gate 1 - by SAP' : ($action == 'BG2' ? 'open gate 2 - by SAP' : $next_status);
                        $log->scaling_date_1 = $scaling_date_1;
                        $log->scaling_time_1 = $scaling_time_1;
                        $log->qty_scaling_1 = $qty_scaling_1;
                        $log->scaling_date_2 = $scaling_date_2;
                        $log->scaling_time_2 = $scaling_time_2;
                        $log->qty_scaling_2 = $qty_scaling_2;
                        $log->ship_to_party = $ship_to_party;
                        $log->delivery_order_no = $delivery_order_no;
                        $log->from_storage_location = $from_storage_location;
                        $log->upto_storage_location = $upto_storage_location;
                        $log->code_bg = 0;
                        $log->date_at = date('Y-m-d');
                        $log->save();

                        Http::get("{$endpoint}?Action={$action}");

                        if ($action == 'BG1') {
                            DB::commit();

                            try {
                                return response()->json([
                                    'status' => 'success',
                                    'message'   => 'Log data has been successfully saved, barrier gate 1 is open!',
                                ], 202);
                            } finally {
                                BarierGateEvent::dispatch([
                                    'status'  => $action == 'BG1' ? 'open gate 1' : ($action == 'BG2' ? 'open gate 2' : $next_status),
                                    'action'  => 'open gate 1',
                                    'truck_no' => $truck_no
                                ]);
                            }
                        } else if ($action == 'BG2') {
                            DB::commit();
                            try {
                                return response()->json([
                                    'status' => 'success',
                                    'message'   => 'Log data has been successfully saved, barrier gate 2 is open!',
                                ], 202);
                            } finally {
                                BarierGateEvent::dispatch([
                                    'status'  => $action == 'BG1' ? 'open gate 1' : ($action == 'BG2' ? 'open gate 2' : $next_status),
                                    'action'  => 'open gate 2',
                                    'truck_no' => $truck_no
                                ]);
                            }
                        } else {
                            DB::rollBack();
                            return response()->json([
                                'status' => 'error',
                                'message'   => 'Barier gate is not found!',
                            ], 404);
                        }
                    }
                }
            } else {
                if ($cek_rb) {
                    if (!empty($next_status)) {
                        $cek_rb->plant = $plant;
                        $cek_rb->sequence = $sequence;
                        $cek_rb->arrival_date = $arrival_date;
                        $cek_rb->truck_no = !empty($truck_no) ? $truck_no : $cek_rb->truck_no;
                        $cek_rb->arrival_time = !empty($arrival_time) ? $arrival_time : $cek_rb->arrival_time;
                        $cek_rb->type_scenario = !empty($type_scenario) ? $type_scenario : $cek_rb->type_scenario;
                        $cek_rb->vendor_do = !empty($vendor_do) ? $vendor_do : $cek_rb->vendor_do;
                        $cek_rb->jenis_kendaraan = !empty($jenis_kendaraan) ? $jenis_kendaraan : $cek_rb->jenis_kendaraan;
                        $cek_rb->status = $cek_rb->next_status;
                        $cek_rb->next_status = $next_status;
                        $cek_rb->scaling_date_1 = !empty($scaling_date_1) ? $scaling_date_1 : $cek_rb->scaling_date_1;
                        $cek_rb->scaling_time_1 = !empty($scaling_time_1) ? $scaling_time_1 : $cek_rb->scaling_time_1;
                        $cek_rb->qty_scaling_1 = !empty($qty_scaling_1) ? $qty_scaling_1 : $cek_rb->qty_scaling_1;
                        $cek_rb->scaling_date_2 = !empty($scaling_date_2) ? $scaling_date_2 : $cek_rb->scaling_date_2;
                        $cek_rb->scaling_time_2 = !empty($scaling_time_2) ? $scaling_time_2 : $cek_rb->scaling_time_2;
                        $cek_rb->qty_scaling_2 = !empty($qty_scaling_2) ? $qty_scaling_2 : $cek_rb->qty_scaling_2;
                        $cek_rb->ship_to_party = !empty($ship_to_party) ? $ship_to_party : $cek_rb->ship_to_party;
                        $cek_rb->delivery_order_no = !empty($delivery_order_no) ? $delivery_order_no : $cek_rb->delivery_order_no;
                        $cek_rb->from_storage_location = !empty($from_storage_location) ? $from_storage_location : $cek_rb->from_storage_location;
                        $cek_rb->upto_storage_location = !empty($upto_storage_location) ? $upto_storage_location : $cek_rb->upto_storage_location;
                        $cek_rb->truck_type = !empty($truck_type) ? $truck_type : $cek_rb->truck_type;
                        $cek_rb->update();

                        $ts = new TrackStatus();
                        $ts->plant = $plant;
                        $ts->sequence = $sequence;
                        $ts->arrival_date = $arrival_date;
                        $ts->status = $next_status;
                        $ts->save();

                        $log = new LogBarierGate();
                        $log->plant = $plant;
                        $log->sequence = $sequence;
                        $log->truck_no = $truck_no;
                        $log->arrival_date = $arrival_date;
                        $log->arrival_time = $arrival_time;
                        $log->type_scenario = $type_scenario;
                        $log->vendor_do = $vendor_do;
                        $log->jenis_kendaraan = $jenis_kendaraan;
                        $log->status = $next_status;
                        $log->next_status = $next_status;
                        $log->scaling_date_1 = $scaling_date_1;
                        $log->scaling_time_1 = $scaling_time_1;
                        $log->qty_scaling_1 = $qty_scaling_1;
                        $log->scaling_date_2 = $scaling_date_2;
                        $log->scaling_time_2 = $scaling_time_2;
                        $log->qty_scaling_2 = $qty_scaling_2;
                        $log->ship_to_party = $ship_to_party;
                        $log->delivery_order_no = $delivery_order_no;
                        $log->from_storage_location = $from_storage_location;
                        $log->upto_storage_location = $upto_storage_location;
                        $log->code_bg = $cek_rb->code_bg;
                        $log->date_at = date('Y-m-d');
                        $log->save();

                        DB::commit();

                        try {
                            return response()->json([
                                'status' => 'success',
                                'message'   => 'Data has been successfully updated!',
                            ], 200);
                        } finally {
                            BarierGateEvent::dispatch([
                                'status'  => $next_status,
                                'action'  => null,
                                'truck_no' => $truck_no
                            ]);
                        }
                    } else {
                        $log = new LogBarierGate();
                        $log->plant = $plant;
                        $log->sequence = $sequence;
                        $log->truck_no = $truck_no;
                        $log->arrival_date = $arrival_date;
                        $log->arrival_time = $arrival_time;
                        $log->type_scenario = $type_scenario;
                        $log->vendor_do = $vendor_do;
                        $log->jenis_kendaraan = $jenis_kendaraan;
                        $log->status = $next_status;
                        $log->next_status = $next_status;
                        $log->scaling_date_1 = $scaling_date_1;
                        $log->scaling_time_1 = $scaling_time_1;
                        $log->qty_scaling_1 = $qty_scaling_1;
                        $log->scaling_date_2 = $scaling_date_2;
                        $log->scaling_time_2 = $scaling_time_2;
                        $log->qty_scaling_2 = $qty_scaling_2;
                        $log->ship_to_party = $ship_to_party;
                        $log->delivery_order_no = $delivery_order_no;
                        $log->from_storage_location = $from_storage_location;
                        $log->upto_storage_location = $upto_storage_location;
                        $log->code_bg = $cek_rb->code_bg;
                        $log->date_at = date('Y-m-d');
                        $log->save();

                        DB::commit();

                        return response()->json([
                            'status' => 'success',
                            'message'   => 'Log data has been successfully saved!',
                        ], 202);
                    }
                } else {
                    if (!empty($next_status)) {
                        $rb = new RealBarier();
                        $code_bg = CodeNumbering::custom_code($rb, 'code_bg');

                        $rb->code_bg = $code_bg;
                        $rb->plant = $plant;
                        $rb->sequence = $sequence;
                        $rb->truck_no = $truck_no;
                        $rb->arrival_date = $arrival_date;
                        $rb->arrival_time = $arrival_time;
                        $rb->type_scenario = $type_scenario;
                        $rb->vendor_do = $vendor_do;
                        $rb->jenis_kendaraan = $jenis_kendaraan;
                        $rb->status = $next_status;
                        $rb->next_status = $next_status;
                        $rb->scaling_date_1 = $scaling_date_1;
                        $rb->scaling_time_1 = $scaling_time_1;
                        $rb->qty_scaling_1 = $qty_scaling_1;
                        $rb->scaling_date_2 = $scaling_date_2;
                        $rb->scaling_time_2 = $scaling_time_2;
                        $rb->qty_scaling_2 = $qty_scaling_2;
                        $rb->ship_to_party = $ship_to_party;
                        $rb->delivery_order_no = $delivery_order_no;
                        $rb->from_storage_location = $from_storage_location;
                        $rb->upto_storage_location = $upto_storage_location;
                        $rb->truck_type = $truck_type;
                        $rb->save();

                        $ts = new TrackStatus();
                        $ts->plant = $plant;
                        $ts->sequence = $sequence;
                        $ts->arrival_date = $arrival_date;
                        $ts->status = $next_status;
                        $ts->save();

                        $log = new LogBarierGate();
                        $log->plant = $plant;
                        $log->sequence = $sequence;
                        $log->truck_no = $truck_no;
                        $log->arrival_date = $arrival_date;
                        $log->arrival_time = $arrival_time;
                        $log->type_scenario = $type_scenario;
                        $log->vendor_do = $vendor_do;
                        $log->jenis_kendaraan = $jenis_kendaraan;
                        $log->status = $next_status;
                        $log->next_status = $next_status;
                        $log->scaling_date_1 = $scaling_date_1;
                        $log->scaling_time_1 = $scaling_time_1;
                        $log->qty_scaling_1 = $qty_scaling_1;
                        $log->scaling_date_2 = $scaling_date_2;
                        $log->scaling_time_2 = $scaling_time_2;
                        $log->qty_scaling_2 = $qty_scaling_2;
                        $log->ship_to_party = $ship_to_party;
                        $log->delivery_order_no = $delivery_order_no;
                        $log->from_storage_location = $from_storage_location;
                        $log->upto_storage_location = $upto_storage_location;
                        $log->code_bg = $code_bg;
                        $log->date_at = date('Y-m-d');
                        $log->save();

                        DB::commit();

                        try {
                            return response()->json([
                                'status' => 'success',
                                'message'   => 'Data has been successfully saved!',
                            ], 202);
                        } finally {
                            BarierGateEvent::dispatch([
                                'status'  => $next_status,
                                'action'  => null,
                                'truck_no' => $truck_no
                            ]);
                        }
                    } else {
                        $log = new LogBarierGate();
                        $log->plant = $plant;
                        $log->sequence = $sequence;
                        $log->truck_no = $truck_no;
                        $log->arrival_date = $arrival_date;
                        $log->arrival_time = $arrival_time;
                        $log->type_scenario = $type_scenario;
                        $log->vendor_do = $vendor_do;
                        $log->jenis_kendaraan = $jenis_kendaraan;
                        $log->status = $next_status;
                        $log->next_status = $next_status;
                        $log->scaling_date_1 = $scaling_date_1;
                        $log->scaling_time_1 = $scaling_time_1;
                        $log->qty_scaling_1 = $qty_scaling_1;
                        $log->scaling_date_2 = $scaling_date_2;
                        $log->scaling_time_2 = $scaling_time_2;
                        $log->qty_scaling_2 = $qty_scaling_2;
                        $log->ship_to_party = $ship_to_party;
                        $log->delivery_order_no = $delivery_order_no;
                        $log->from_storage_location = $from_storage_location;
                        $log->upto_storage_location = $upto_storage_location;
                        $log->code_bg = 0;
                        $log->date_at = date('Y-m-d');
                        $log->save();

                        DB::commit();

                        return response()->json([
                            'status' => 'success',
                            'message'   => 'Log data has been successfully saved!',
                        ], 202);
                    }
                }
            }
        } catch (QueryException $th) {
            DB::rollback();

            return response()->json([
                'status' => 'error',
                'message'   => $th->getMessage(),
            ], 422);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message'   => $th->getMessage(),
            ], 422);
        }
    }
}
