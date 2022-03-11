<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;

use DataTables;

class CountriesController extends Controller
{
    public function index()
    {
        //COUNTRİES LİST

        return view('countries-list');
    }

    //ADD NEW COUNTRY
    public function addCountry(Request $request)
    {
        $validator = \Validator::make($request->all(),[
            'country_name' => 'required|unique:countries',
            'capital_city' =>'required',
        ]);

        if (!$validator->passes()) {
            return response()->json(['code' => 0,'error' => $validator->errors()->toArray()]);
        }else{
            $country = new Country;
            $country->country_name = $request->country_name;
            $country->capital_city = $request->capital_city;
            $query = $country->save();
            if(!$query){
                return response()->json(['code' => 0 ,'msg' => 'Kayıt başarısız!']);
            }else{
                return response()->json(['code' => 1 ,'msg' => 'Kayıt girildi']);
            }
        }
    }

    // GET COUNTRY LİST
    public function getCountriesList()
    {
        $countries = Country::all();
        return DataTables::of($countries)
        ->addIndexColumn()
        ->addColumn('actions',function($row){
            return '<div class="btn-group">
                        <button class="btn btn-warning" data-id="'.$row['id'].'" id="editCountryBtn">Edit</button>
                        <button class="btn btn-danger" data-id="'.$row['id'].'" id="deleteCountry">Delete</button>
                    </div>';
        })
        ->addColumn('checkbox',function($row){
            return '<input type="checkbox" name="country_checkbox" data-id="'.$row['id'].'"><label></label>';
        })
        ->rawColumns(['actions','checkbox'])
        ->make(true);
    }
    public function getCountryDetails(Request $request)
    {
        $country_id = $request->country_id;
        $countryDetails = Country::find($country_id);
        return response()->json(['details' => $countryDetails]);
    }

    // UPDATE COUNTRY DETAİLS
    public function updateCountryDetails(Request $request)
    {
        $country_id = $request->cid;

        $validator = \Validator::make($request->all(),[
            'country_name'=>'required|unique:countries,country_name,'.$country_id,
            'capital_city'=>'required'
        ]);

        if(!$validator->passes()) {
            return response()->json(['code' => 0 , 'error'=>$validator->errors()->toArray()]);
        }else{
            $country = Country::find($country_id);
            $country->country_name = $request->country_name;
            $country->capital_city = $request->capital_city;
            $query = $country->save();

            if ($query) {
                return response()->json(['code' => 1,'msg'=>'Kayıt başarı ile güncellendi']);
            }else{
                return response()->json(['code' => 0,'msg'=>'Kayıt Güncellenemedi.']);
            }
        }
    }
    // DELETE COUNTRY
    public function deleteCountry(Request $request)
    {
        $country_id = $request->country_id;
        $query = Country::find($country_id)->delete();

        if ($query) {
            return response()->json(['code'=>1, 'msg'=>'Kayıt veri tabanından silindi']);
        }else{
            return response()->json(['code' =>0, 'msg'=>'Kayıt silinemedi eksik birşeyler var!!!']);
        }
    }
    public function deleteSelectedCountries(Request $request)
    {
        $country_ids = $request->countries_ids;
        Country::whereIn('id',$country_ids)->delete();
        return response()->json(['code' =>1 ,'msg' => 'Şeçili kutular silindi.']);
    }
    
}
