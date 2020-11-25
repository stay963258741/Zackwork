<?php

namespace App\Http\Controllers;


use App\Models\Address;
use GuzzleHttp;
use Log;


class AddressController extends Controller
{
    public function create()
    {
        return view('addresses.create');
    }

    public function show($hostname)
    {
        $address = Address::find($hostname);
        return view('welcome', ['address' => $address]);
    }

    public function guzzle()
    {
        $rows = Address::all();
        foreach ($rows as $row) {
            $status = $this->get_status($row->hostname);
            if ($status) {
                Address::where('hostname', '=', $row->hostname)
                    ->update(['status' => 1]);
            } else {
                Address::where('hostname', '=', $row->hostname)
                    ->update(['status' => 0]);
            }
        }
    }

    public function get_status($url)
    {
        try {
            $client = new GuzzleHttp\Client;

            $status = $client->get($url);
            if ($status->getStatusCode() == 200) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function store()
    {
        $addresses = new Address();
        $addresses->hostname = request('hostname');
        $addresses->save();
        $this->guzzle();
        return redirect('/home');  //儲存後回到ＨＯＭＥ
    }

}

;
