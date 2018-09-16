<?php

namespace App\Http\Controllers;

use App\Company;
use App\Offer;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    function getAllProfessionals(Request $request)
    {
        if ($request->isJson()) {
            $offer = Offer::findOrFail($request->offer_id);
            $professionals = $offer->professionals;
            return response()->json($professionals, 200);
        }
        return response()->json(['error' => 'Unsupported Media Type'], 415, []);
    }

    function createOfferProfessional(Request $request)
    {
        if ($request->isJson()) {
            $data = $request->json()->all();
            $user = User::create([
                'name' => $data['name'],
                'user_name' => $data['user_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'api_token' => str_random(60),
            ]);
            return response()->json($user, 201);
        }
        return response()->json(['error' => 'Unsupported Media Type'], 415, []);
    }

    function updateOfferProfessional(Request $request)
    {
        if ($request->isJson()) {
            $data = $request->json()->all();
            $user = User::find($data['id'])->update([
                'name' => $data['name'],
                'user_name' => $data['user_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'api_token' => str_random(60),
            ]);
            return response()->json($user, 201);
        }
        return response()->json(['error' => 'Unsupported Media Type'], 415, []);
    }

    function deleteOfferProfessional($id)
    {
        $user = User::findOrFail($id)->delete();
        return response()->json($user, 201);
    }

    function getAllOffers(Request $request)
    {
        if ($request->isJson()) {
            $offers = Offer::orderby($request->field, $request->order)->paginate($request->limit);
            return response()->json($offers, 200);
        }
        return response()->json(['error' => 'Unsupported Media Type'], 415, []);
    }

    function filterOffers(Request $request)
    {
        if ($request->isJson()) {
            //para tener varias condiciones en un array
            //$users = User::orWhere([$request->conditions])
            $data = $request->json()->all();
            $offers = Offer::orWhere('broad_field', 'like', $data['broad_field'] . '%')
                ->orWhere('specific_field', 'like', $data['specific_field'] . '%')
                ->orWhere('position', 'like', $data['position'] . '%')
                ->orWhere('remuneration', 'like', $data['remuneration'] . '%')
                ->orWhere('working_day', 'like', $data['working_day'] . '%')
                ->orderby($request->field, $request->order)
                ->paginate($request->limit);
            return response()->json([
                'pagination' => [
                    'total' => $offers->total(),
                    'current_page' => $offers->currentPage(),
                    'per_page' => $offers->perPage(),
                    'last_page' => $offers->lastPage(),
                    'from' => $offers->firstItem(),
                    'to' => $offers->lastItem()
                ], 'offers' => $offers], 200);
        }
        return response()->json(['error' => 'Unsupported Media Type'], 415, []);
    }

    function createOffer(Request $request)
    {
        if ($request->isJson()) {
            $data = $request->json()->all();
            $company = Company::findOrFail($request->company_id);
            $response = $company->offers()->create([
                'code' => $data['code']
            ]);
            return response()->json($response, 201);
        }
        return response()->json(['error' => 'Unsupported Media Type'], 415, []);
    }

    function updateOffer(Request $request)
    {
        if ($request->isJson()) {
            $data = $request->json()->all();
            $offer = Offer::find($request->id)->update([
                'code' => $data['code']
            ]);
            return response()->json($offer, 201);
        }
        return response()->json(['error' => 'Unsupported Media Type'], 415, []);
    }

    function deleteOffer(Request $request)
    {
        $offer = Offer::findOrFail($request->id)->delete();
        return response()->json($offer, 201);
    }
}
