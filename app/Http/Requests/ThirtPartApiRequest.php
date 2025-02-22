<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ThirtPartApiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        switch ($this->type) {
            case 'mail_config':
                return [
                    'mailer_name' => 'required',
                    'host'        => 'required',
                    'driver'      => 'required',
                    'port'        => 'required',
                    'username'    => 'required|email',
                    'email'       => 'required|email',
                    'encryption'  => 'required|in:ssl,tls',
                    'password'    => 'required',
                ];
            case 'google-map':
                return [
                    'google_api_key' => 'required|string',
                ];
            default:return [];
        }
    }
}
