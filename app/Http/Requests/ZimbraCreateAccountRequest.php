<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ZimbraCreateAccountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'username' => 'required|string',
            'domain' => 'required||string|in:zimbra.sumit.sum.ba,zextras.sumit.sum.ba',
            'password' => 'required|string',
            'givenName' => 'nullable|string',
            'displayName' => 'nullable|string'
        ];
    }
}
