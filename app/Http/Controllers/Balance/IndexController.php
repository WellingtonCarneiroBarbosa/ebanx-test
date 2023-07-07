<?php

namespace App\Http\Controllers\Balance;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends Controller
{
    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'account_id' => ['required', 'integer'],
        ]);

        try {
            $account = Account::findOrFail($data['account_id']);
        } catch (ModelNotFoundException) {
            return response(0, Response::HTTP_NOT_FOUND);
        }

        return response($account->balance);
    }
}
