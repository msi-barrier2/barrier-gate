<?php

namespace App\Http\Controllers;

use DateTime;
use App\Models\RealBarier;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Route;

class FullRealController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $type_scenario = $request->type_scenario;
            $next_status = $request->next_status;

            if (!empty($type_scenario) && !empty($next_status)) {
                $bg = RealBarier::with('track')
                    ->where('type_scenario', $type_scenario)
                    ->where('next_status', $next_status)
                    ->where(function ($query) {
                        $query->whereBetween('created_at', [now()->subDays(2)->startOfDay(), now()->endOfDay()]);
                        $query->whereNot(function ($query) {
                            $query->where('next_status', 'completed')
                                ->orWhere('next_status', 'reject all by qc');
                        });
                    })->orWhere(function ($query) {
                        $query->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()]);
                        $query->where(function ($query) {
                            $query->where('next_status', 'completed')
                                ->orWhere('next_status', 'reject all by qc');
                        });
                    })
                    ->orderByRaw("CASE WHEN next_status = 'completed' THEN 1 WHEN next_status  = 'reject all by qc' then 2 END ASC")
                    ->select('*');
            } else if (!empty($type_scenario)) {
                $bg = RealBarier::with('track')
                    ->where('type_scenario', $type_scenario)
                    ->where(function ($query) {
                        $query->whereBetween('created_at', [now()->subDays(2)->startOfDay(), now()->endOfDay()]);
                        $query->whereNot(function ($query) {
                            $query->where('next_status', 'completed')
                                ->orWhere('next_status', 'reject all by qc');
                        });
                    })->orWhere(function ($query) {
                        $query->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()]);
                        $query->where(function ($query) {
                            $query->where('next_status', 'completed')
                                ->orWhere('next_status', 'reject all by qc');
                        });
                    })
                    ->orderByRaw("CASE WHEN next_status = 'completed' THEN 1 WHEN next_status  = 'reject all by qc' then 2 END ASC")
                    ->select('*');
            } else if (!empty($next_status)) {
                $bg = RealBarier::with('track')
                    ->where('next_status', $next_status)
                    ->where(function ($query) {
                        $query->whereBetween('created_at', [now()->subDays(2)->startOfDay(), now()->endOfDay()]);
                        $query->whereNot(function ($query) {
                            $query->where('next_status', 'completed')
                                ->orWhere('next_status', 'reject all by qc');
                        });
                    })->orWhere(function ($query) {
                        $query->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()]);
                        $query->where(function ($query) {
                            $query->where('next_status', 'completed')
                                ->orWhere('next_status', 'reject all by qc');
                        });
                    })
                    ->orderByRaw("CASE WHEN next_status = 'completed' THEN 1 WHEN next_status  = 'reject all by qc' then 2 END ASC")
                    ->select('*');
            } else {
                $bg = RealBarier::with('track')
                    ->where(function ($query) {
                        $query->whereBetween('created_at', [now()->subDays(2)->startOfDay(), now()->endOfDay()]);
                        $query->whereNot(function ($query) {
                            $query->where('next_status', 'completed')
                                ->orWhere('next_status', 'reject all by qc');
                        });
                    })->orWhere(function ($query) {
                        $query->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()]);
                        $query->where(function ($query) {
                            $query->where('next_status', 'completed')
                                ->orWhere('next_status', 'reject all by qc');
                        });
                    })->orderByRaw("CASE WHEN next_status = 'completed' THEN 1 WHEN next_status  = 'reject all by qc' then 2 ELSE updated_at END DESC")
                    ->select('*');
            }

            // $bg = RealBarier::with('track')
            // ->orderBy('id', 'DESC')->select('*');

            return DataTables::of($bg)
                ->addColumn('orders', function ($bg) {
                    $orders = null;
                    $do_no = $bg->delivery_order_no;
                    $type_scenario = explode(" ", $bg->type_scenario);

                    $orders .= "
                        <span class='badge bg-dark'>PLANT: {$bg->plant}</span> &nbsp;
                    ";

                    if ($type_scenario[0] == 'inbound' || $type_scenario[0] == 'outbound') {
                        $orders .= "
                            <span class='badge bg-dark'>SEQ: {$bg->sequence}</span> <br>
                        ";
                    } else {
                        $orders .= "
                            <span class='badge bg-dark'>BT: {$bg->sequence}</span> <br>
                        ";
                    }


                    $created_at = date('d-m-Y H:i:s', strtotime($bg->created_at));

                    $orders .= "
                        <span class='badge bg-warning'><i class='fa fa-calendar-days'></i>
                            {$created_at}</span> 
                        ";

                    if ($bg->next_status == 'completed') {
                        $updated_at = date('d-m-Y H:i:s', strtotime($bg->updated_at));
                        $orders .= "
                        &nbsp; <span class='badge bg-success'><i class='fa fa-calendar-days'></i>
                            {$updated_at}</span> &nbsp; <br>
                        ";
                    } else {
                        $orders .= "<br> ";
                    }

                    if ($bg->truck_no) {
                        $orders .= "
                       <span class='badge bg-light text-dark'><i class='fa fa-credit-card'></i>
                        $bg->truck_no</span> <br>
                        ";
                    }

                    if ($bg->arrival_time) {
                        $orders .= "
                        <span class='badge bg-light text-dark'><i class='fa fa-clock'></i>
                        $bg->arrival_time</span> <br>
                        ";
                    }

                    if ($bg->jenis_kendaraan) {
                        $orders .= "
                        <span class='badge bg-light text-dark'><i class='fa fa-truck'></i>
                        $bg->jenis_kendaraan</span> <br>
                        ";
                    }

                    if ($bg->ship_to_party) {
                        $stp = explode(";", $bg->ship_to_party);
                        $stp = array_unique($stp);
                        $stp = implode(";", $stp);
                        $stp = str_replace(";", "<br>", $stp);
                        $orders .= "
                        <span class='badge bg-light text-dark text-start'><i class='fa fa-location-pin'></i>
                        $stp</span> <br>
                        ";
                    }

                    if ($bg->from_storage_location) {
                        $orders .= "
                        <span class='badge bg-light text-dark'><i class='fa fa-database'></i>
                        $bg->from_storage_location</span> <br>
                        ";
                    }

                    if ($bg->upto_storage_location) {
                        $orders .= "
                        <span class='badge bg-light text-dark'><i class='fa fa-database'></i>
                        $bg->upto_storage_location</span> <br>
                        ";
                    }

                    if ($do_no) {
                        $do_no = wordwrap($do_no, 30, "<br>\n", true);
                        $orders .= "
                           <span class='badge bg-primary'>#
                                $do_no</span> <br>
                            ";
                    }

                    return $orders;
                })
                ->addColumn('scale_1', function ($bg) {
                    $scale_1 = null;
                    $qty_scaling_1 = (float)  preg_replace("/[^0-9]/", "", $bg->qty_scaling_1);
                    $qty = number_format($qty_scaling_1, 0, ",", ".");
                    // $qty = $bg->qty_scaling_1;
                    if ($bg->scaling_date_1 || $bg->scaling_time_1 || $bg->qty_scaling_1) {
                        $scale_1 .= "
                        <span class='badge bg-dark'>
                        $bg->scaling_date_1</span> <br>
                        <span class='badge bg-dark'>
                        $bg->scaling_time_1</span> <br>
                        <span class='badge bg-dark'>
                        $qty KG</span>
                        ";
                    }

                    return $scale_1;
                })
                ->addColumn('scale_2', function ($bg) {
                    $scale_2 = null;
                    $qty_scaling_2 = (float)  preg_replace("/[^0-9]/", "", $bg->qty_scaling_2);
                    $qty = number_format($qty_scaling_2, 0, ",", ".");
                    if ($bg->scaling_date_2 || $bg->scaling_time_2 || $bg->qty_scaling_2) {
                        $scale_2 .= "
                        <span class='badge bg-dark'>
                        $bg->scaling_date_2</span> <br>
                        <span class='badge bg-dark'>
                        $bg->scaling_time_2</span> <br>
                        <span class='badge bg-dark'>
                        $qty KG</span>
                        ";
                    }

                    return $scale_2;
                })
                ->addColumn('scenario', function ($bg) {
                    $scenario = "";

                    $type_scenario = explode(" ", $bg->type_scenario);
                    if ($type_scenario[0] == 'inbound') {
                        $scenario .= "
                            <span class='badge bg-success'>{$bg->type_scenario}</span> <br>
                        ";
                    } else if ($type_scenario[0] == 'outbound') {
                        $scenario .= "
                            <span class='badge bg-primary'>{$bg->type_scenario}</span> <br>
                        ";
                    } else {
                        $scenario .= "
                            <span class='badge bg-info'>{$bg->type_scenario}</span> <br>
                        ";
                    }

                    if ($bg->truck_no) {
                        $scenario .= "
                       <h3><span class='badge bg-light text-dark'><i class='fa fa-credit-card'></i>
                        $bg->truck_no</span></h3>
                        ";

                        // BUTTON PANGGIL PLAT
                        $scenario .= "
                            <a href='javascript:void(0)' class='badge bg-danger btn-plat' data-plat='$bg->truck_no'><i class='fa fa-play'></i></a>
                        ";
                    }

                    return $scenario;
                })
                ->addColumn('status_bg', function ($bg) {
                    $next_status = wordwrap($bg->next_status, 10, "<br>\n");
                    if ($bg->next_status == 'completed') {
                        $qty_scaling_1 = (float)  preg_replace("/[^0-9]/", "", $bg->qty_scaling_1);
                        $qty_scaling_2 = (float)  preg_replace("/[^0-9]/", "", $bg->qty_scaling_2);

                        if ($qty_scaling_1 >= $qty_scaling_2) {
                            $result = $qty_scaling_1 - $qty_scaling_2;
                            $result = number_format($result, 0, ",", ".");
                        } else {
                            $result = $qty_scaling_2 - $qty_scaling_1;
                            $result = number_format($result, 0, ",", ".");
                        }



                        $status = "
                            <span class='badge bg-success'>{$next_status}</span> <br>
                            <span class='badge bg-primary'>{$this->time_elapsed_string($bg->created_at,$bg->updated_at)}</span> <br>
                            <span class='badge bg-warning'>{$result} KG</span> <br>
                        ";
                    } else if ($bg->next_status == 'reject all by qc') {
                        $status = "
                            <span class='badge bg-danger'>{$next_status}</span>
                        ";
                    } else {
                        $status = "
                            <span class='badge bg-warning'>{$next_status}</span>
                        ";
                    }


                    return $status;
                })
                ->addColumn('track_status', function ($bg) use ($request) {
                    $track = null;
                    $i = 0;
                    $len = count($bg->track);

                    if ($request->path() == 'full_table') {
                        if ($bg->track) {
                            $track .= "
                            <div class='row'>
                            ";
                            foreach ($bg->track as $key => $val) {
                                if ($len == 1) {
                                    $track .= "
                                    <div class='order-tracking completed'>
                                        <span class='is-complete'></span>
                                        <span class='full_table'>{$val->status}</span>
                                    </div>
                                    ";
                                } else {
                                    if ($i == $len - 1) {
                                        if ($val->status == 'completed') {
                                            $track .= "
                                            <div class='order-tracking completed'>
                                                <span class='is-complete'></span>
                                                  <span class='full_table'>{$val->status}</span>
                                            </div>
                                            ";
                                        } else if ($val->status == 'reject all by qc') {
                                            $track .= "
                                            <div class='order-tracking rejected'>
                                                <span class='is-reject'></span>
                                                  <span class='full_table'>{$val->status}</span>
                                            </div>
                                            ";
                                        } else {
                                            $track .= "
                                            <div class='order-tracking '>
                                                <span class='is-complete'></span>
                                                  <span class='full_table'>{$val->status}</span>
                                            </div>
                                            ";
                                        }
                                    } else {
                                        $track .= "
                                            <div class='order-tracking completed'>
                                                <span class='is-complete'></span>
                                                  <span class='full_table'>{$val->status}</span>
                                            </div>
                                            ";
                                    }
                                }

                                $i++;
                            }
                            $track .= "
                             </div>
                            ";
                        }
                    } else {
                        if ($bg->track) {

                            $track .= "
                            <div class='row'>
                            ";
                            foreach ($bg->track as $key => $val) {
                                if ($len == 1) {
                                    $track .= "
                                    <div class='order-tracking completed'>
                                        <span class='is-complete'></span>
                                        <span>{$val->status}</span>
                                    </div>
                                    ";
                                } else {
                                    if ($i == $len - 1) {
                                        if ($val->status == 'completed') {
                                            $track .= "
                                            <div class='order-tracking completed'>
                                                <span class='is-complete'></span>
                                                <span>{$val->status}</span>
                                            </div>
                                            ";
                                        } else if ($val->status == 'reject all by qc') {
                                            $track .= "
                                            <div class='order-tracking rejected'>
                                                <span class='is-reject'></span>
                                                <span>{$val->status}</span>
                                            </div>
                                            ";
                                        } else {
                                            $track .= "
                                            <div class='order-tracking '>
                                                <span class='is-complete'></span>
                                                <span>{$val->status}</span>
                                            </div>
                                            ";
                                        }
                                    } else {
                                        $track .= "
                                            <div class='order-tracking completed'>
                                                <span class='is-complete'></span>
                                                <span>{$val->status}</span>
                                            </div>
                                            ";
                                    }
                                }

                                $i++;
                            }
                            $track .= "
                             </div>
                            ";
                        }
                    }

                    return $track;
                })
                ->addColumn('action', function ($bg) {
                    if (auth()->user()->role == 'admin') {
                        $action = "
                            <a href='javascript:void(0)' class='btn btn-xs btn-danger btn-delete-bg' data-plant='$bg->plant' data-seq='$bg->sequence' data-date='$bg->arrival_date'><i class='fa fa-trash'></i></a>
                        ";
                    } else {
                        $action = "-";
                    }

                    return $action;
                })

                ->rawColumns(['orders', 'scenario', 'scale_1', 'scale_2', 'status_bg', 'track_status', 'action'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('full.index');
    }

    private function time_elapsed_string($from, $to)
    {
        $awal  = strtotime($from);
        $akhir = strtotime($to);
        $diff  = $akhir - $awal;

        $jam   = floor($diff / (60 * 60));
        $menit = $diff - ($jam * (60 * 60));
        $menit = floor($menit / 60);
        $detik = $diff % 60;

        if ($jam == 0) {
            $result = "{$menit} Minutes";
        } else {
            $result = "{$jam} Hours, {$menit} Minutes";
        }
        return $result;
    }

    public function ajax_get_ts(Request $request)
    {
        $search = $request->q;
        $data = [];
        if ($request->ajax()) {
            if ($search == '') {
                $data = RealBarier::orderby('id', 'asc')->limit(10)->groupBy('type_scenario')
                    ->select('type_scenario')->get();
            } else {
                $data = RealBarier::orderby('id', 'asc')->where('type_scenario', 'like', "%$search%")->limit(10)->groupBy('type_scenario')
                    ->select('type_scenario')->get();
            }
        }

        return response()->json($data);
    }

    public function ajax_get_sts(Request $request)
    {
        $search = $request->q;
        $data = [];
        if ($request->ajax()) {
            if ($search == '') {
                $data = RealBarier::orderby('id', 'asc')->limit(10)->groupBy('next_status')
                    ->select('next_status')->get();
            } else {
                $data = RealBarier::orderby('id', 'asc')->where('next_status', 'like', "%$search%")->limit(10)->groupBy('next_status')
                    ->select('next_status')->get();
            }
        }

        return response()->json($data);
    }
}
