<?php

namespace App\Http\Requests\Zoom\ZoomMeeting;



use App\Http\Requests\Request;

class UpdateMeeting extends Request
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
        $company = company();

        return [
            'meeting_title' => 'required',
            'start_date' => 'required',
            'end_date' => 'required|date_format:d/m/y|after_or_equal:start_date',
            'all_employees' => 'sometimes',
            'all_clients' => 'sometimes',
            'employee_id.0' => 'required_without_all:all_employees,all_clients,client_id.0',
            'client_id.0' => 'required_without_all:all_employees,all_clients,employee_id.0',
        ];
    }

    public function messages()
    {
        return [
            'employee_id.0.required_without_all' => __('zoom.zoommeeting.attendeeValidation'),
            'client_id.0.required_without_all' => __('zoom.zoommeeting.attendeeValidation'),
        ];
    }
}
