<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Validator;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $message    = ['message' => [__('Successful')]];
        $status     = 'success';
        $data       = Customer::with(['document'])->get();;

        return response([
            'data'      => $data,
            'status'    => $status,
            'message'   => $message
        ],200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCustomerRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $message    = ['message' => [__('Something is not right')]];
        $status     = 'warning';
        $data       = false;


        $credentials = $request->only([
            'first_name',
            'last_name',
            'address',
            'phone',
            'identification',
            'document_id',
        ]);

        $validation = Validator::make($credentials,[
            'first_name'        => 'required|max:50|min:2|string',
            'last_name'         => 'required|max:50|min:2|string',
            'address'           => 'required|max:250|min:10|string',
            'phone'             => 'required|max:30|min:7|unique:customers,phone',
            'identification'    => 'required|min:6|max:11|unique:customers,identification',
            'document_id'       => 'required|integer|exists:documents,id',
        ]);


        if (!$validation->fails()) {

            $insertOk = Customer::create($credentials);

            $message    = ['message' => [__('Successful')]];
            $status     = 'success';
            $data       = $insertOk;


        }else{
            $message    = $validation->messages();
            $status     = 'warning';
            $data       = false;

        }

        return response([
            'data'      => $data,
            'status'    => $status,
            'message'   => $message
        ],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        $message    = ['message' => [__('Successful')]];
        $status     = 'success';
        $data       = $customer->load(['document']);

        return response([
            'data'      => $data,
            'status'    => $status,
            'message'   => $message
        ],200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCustomerRequest  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Customer $customer)
    {
        $message    = ['message' => [__('Something is not right')]];
        $status     = 'warning';
        $data       = false;

            $credentials = $request->only([
                'first_name',
                'last_name',
                'address',
                'phone',
                'identification',
                'document_id',
            ]);

            $validation = Validator::make($credentials,[
                'first_name'        => 'sometimes|required|max:50|min:2|string',
                'last_name'         => 'sometimes|required|max:50|min:2|string',
                'address'           => 'sometimes|required|max:250|min:10|string',
                'phone'             => 'sometimes|required|max:30|min:7|unique:customers,phone,'. $customer->id,
                'identification'    => 'sometimes|required|min:6|max:11|unique:customers,identification,'. $customer->id,
                'document_id'       => 'sometimes|required|integer|exists:documents,id',
            ]);

            if (!$validation->fails()) {


                        foreach ($credentials as $key => $value) {
                            if ($credentials[$key] == $customer[$key]) {
                                unset($credentials[$key]);
                            }
                        }

                        if(count($credentials)){

                            $okUpdate = $customer->fill($credentials)->save();

                            if($okUpdate){
                                $message    = ['message' => [__('Successful')]];
                                $status     = 'success';
                                $data       = $customer->load(['document']);

                            }else{
                                $message    = ['message' => [__('Try one more time')]];
                                $status     = 'warning';
                                $data       = false;
                            }

                        }else{

                            $message    = ['message' => [__('Nothing new to update')]];
                            $status     = 'warning';
                            $data       = false;
                        }
            }else{
                $message    = $validation->messages();
                $status     = 'warning';
                $data       = false;

            }
        return response([
            'data'      => $data,
            'status'    => $status,
            'message'   => $message
        ],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        $message    = ['message' => [__('Something is not right')]];
        $status     = 'warning';
        $data       = false;


        $deleteOk = $customer->delete();

        if($deleteOk){
            $message    = ['message' => [__('Successful')]];
            $status     = 'success';
            $data       = true;
        }
        
        return response([
            'data'      => $data,
            'status'    => $status,
            'message'   => $message
        ],200);
    }
}
