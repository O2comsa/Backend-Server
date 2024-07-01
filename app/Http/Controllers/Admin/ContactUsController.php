<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Models\ContactUs;
use App\Notifications\ContactUsReplayNotification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\DataTables;

class ContactUsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:contactus.manage');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('Admin.contact_us.index');
    }

    public function getContactUs()
    {
        return Datatables::of(ContactUs::latest())
            ->addIndexColumn()
            ->addColumn('user', function ($contact_us) {
                if (isset($contact_us->user_id)) {
                    return (isset($contact_us->user)) ? '<a href="' . route('users.edit', $contact_us->user->id) . '">' . $contact_us->user->name . '</a>' : 'مستخدم محذوف';
                } else {
                    return $contact_us->name;
                }
            })
            ->addColumn('show_message', function ($contact_us) {
                return '<button id="show_message" type="button" data-toggle="modal" class="btn btn-sm btn-primary push" data-id="' . $contact_us->id . '">' . trans('app.show_message') . '</button>';
            })
            ->addColumn('send_replay', function ($contact_us) {
                return '<button id="send_replay" type="button" data-toggle="modal" class="btn btn-sm btn-primary push" data-id="' . $contact_us->id . '">' . trans('app.send_replay') . '</button>';
            })
            ->addColumn('created_at', function ($contact_us) {
                return $contact_us->created_at->diffForHumans();
            })
            ->filterColumn('user', function ($query, $keyword) {
                $query->where(function ($query) use ($keyword) {
                    $query->whereHas('user', function ($query) use ($keyword) {
                        $query->where('name', 'like', "%{$keyword}%");
                    })->orWhere('name', 'like', "%{$keyword}%");
                });
            })
            ->rawColumns(['created_at', 'user', 'show_message', 'send_replay'])
            ->make(true);
    }
    
    public function getContactUsAjax(Request $request)
    {
        $contactUs = ContactUs::find($request->get('id'));
        return $contactUs;
    }

    public function sendReplay(Request $request)
    {
        $contactUs = ContactUs::find($request->get('contact_us_id'));

        try {
            // notify by user
            $admin = new Admin();
            $admin->email = $contactUs->email;
            $admin->notify(new ContactUsReplayNotification($contactUs->title, $contactUs->name, $contactUs->email, $contactUs->message, $contactUs->mobile, $request->get('message')));

            return redirect()->route('contactus')->withSuccess(trans('app.message_send_success'));

            //end notify
        } catch (\Exception $exception) {
            return redirect()->route('contactus')->withErrors(trans('app.message_send_errors'));
        }
    }
}
