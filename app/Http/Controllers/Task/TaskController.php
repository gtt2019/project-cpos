<?php

namespace App\Http\Controllers\Task;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Service\TokenService;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    const STATUS_ID_IN_PROGRESS = 3;
    const ORDER_STATUS = [1, 3, 5, 7, 8];

    /**
     * API to get new status task for all delevery boy.
     */
    public function getAllNewTask(Request $request) 
    {
        $token = $request->input('token');
        $userId = $request->input('userId');     
        $accesToken = "aaaaa123456@#";
        $statusId = 1;
        $addressType = ['Store', 'User'];

        $v = Validator::make($request->all(), [
            'userId' => 'required|int',
            'token' => 'required'
            ]);
    
        if ($v->fails())
        {            
            $err = $v->errors();       
            $message = $err->all();
            return json_encode(['error' => $message] );
        }

        $tokenService = new TokenService();
        $tokenStatus = $tokenService->validateAccessToken($token);

        if ($tokenStatus['status'] !== true) {

            return response()->json([
                'message' => $tokenStatus['messsage'],
                'statusCode' => 400,
                'accessToken' => $accesToken,
                'data' => $data
            ]);                
        }

        $tasks = DB::table('ORDERMASTER')                                    
            ->select('orderid', 'orderno', 'statusid','ORDERMASTER.storeid',
             'CUSTMASTER.customerid', 'orderarea', 'orderdesc', 
            'totalamtwithtax', 'totaldiscount', 'CUSTMASTER.firstName',
            'STRSTORE.name as storeName', 'STRSTORE.owner as ownerName',            
            'STRSTORE.latitude', 'STRSTORE.longitude'
            )
            ->join('CUSTMASTER', 'ORDERMASTER.customerid', '=', 'CUSTMASTER.customerid' )
            ->join('STRSTORE', 'ORDERMASTER.storeid', '=', 'STRSTORE.storeid')            
            ->whereIn('statusid', self::ORDER_STATUS)            
            ->where('assignedto', $userId)
            ->get();
            
            $data = [];
            foreach ($tasks as $key => $task) {                
                $storeAddress = $this->getAddress('Store', $task->storeid);
                $customerAddress = $this->getAddress('User', $task->customerid);

                $data[$key] = [
                    'orderDetails' => $task,
                    'storeAddress' => $storeAddress,
                    'customerAddress' => $customerAddress
                ];
            }
            if (empty($data)) {
                $message = "No new tasks";
                $code = 204;     
                $accesToken = "aaaaa123456@#";
                $data = nul;
            }else {
                $data = $data;
                $message = "";
                $code = 200;     
                $accesToken = "aaaaa123456@#";
            }
    
            return response()->json([
                'message' => $message,
                'statusCode' => $code,
                'accessToken' => $accesToken,
                'data' => $data
            ]);            
    }   


    public function getAddress($addressType, $addressTypeId) 
    {        
        $tasks = DB::table('CMNADDRESS')                                    
            ->select(
                'address1', 'address2', 'locality'
            )
            ->where('addresstype', $addressType)            
            ->where('addresstypeid', $addressTypeId)
            ->where('primaryaddress', 1)
            ->first();

            return $tasks;
    }
    
    public function updateTaskStatus(Request $request)
    {
        $token = $request->input('token');
        $userId = $request->input('userId');     
        $orderId = $request->input('orderId');
        $accesToken = "aaaaa123456@#";
        $statusId = 1;
        
        $v = Validator::make($request->all(), [
            'userId' => 'required|int',
            'token' => 'required',
            'orderId' => 'required'
            ]);
    
        if ($v->fails())
        {            
            $err = $v->errors();       
            $message = $err->all();
            return json_encode(['error' => $message] );
        }

        $tokenService = new TokenService();
        $tokenStatus = $tokenService->validateAccessToken($token);

        if ($tokenStatus['status'] !== true) {

            return response()->json([
                'message' => $tokenStatus['messsage'],
                'statusCode' => 400,
                'accessToken' => $accesToken,
                'data' => $data
            ]);                
        }
        
        $tasks = DB::table('ORDERMASTER')
                    ->where(['orderid' => $orderId, 'active' => 'Y'])
                    ->update(['statusid' => self::STATUS_ID_IN_PROGRESS]);
                    dd($tasks);

        dd("dffdf");
    }
}
