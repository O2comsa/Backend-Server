<?php

namespace App\Http\Requests\Zoom\ZoomMeeting;



use App\Http\Requests\Request;

class UpdateOccurrence extends Request
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
            'start_date' => 'required',
            'end_date' => 'required|date_format:d/m/y|after_or_equal:start_date',
        ];
    }
}
