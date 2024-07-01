@extends('Admin.layouts.app')

@section('page-title', trans('app.contact_us'))
@section('page-heading', trans('app.contact_us'))
@section('content')
    <div class="col-md-12">
        <div class="block block-rounded block-bordered">
            <div class="block-header block-header-default">
                <h3 class="block-title">
                    <small></small>
                </h3>
            </div>
            <div class="block-content">
                <table class="table table-striped table-vcenter" id="ContactUSTable" style="width: 100%">
                    <thead class="thead-dark">
                    <tr>
                        <th></th>
                        <th>@lang('app.user')</th>
                        <th>@lang('app.email')</th>
                        <th>@lang('app.show_message')</th>
                        <th>@lang('app.send_replay')</th>
                        <th>@lang('app.created_at')</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal" id="modal-show-message" tabindex="-1" role="dialog" aria-labelledby="modal-show-message" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title">@lang('app.contact_us')</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                <i class="fa fa-fw fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content font-size-sm">
                        <div class="form-group">
                            <label for="frontend-contact-name">@lang('app.name')</label>
                            <input type="text" class="form-control" id="name" disabled value="">
                        </div>
                        <div class="form-group">
                            <label for="frontend-contact-email">@lang('app.email')</label>
                            <input type="text" class="form-control" id="email" disabled value="">
                        </div>
                        <div class="form-group">
                            <label for="frontend-contact-message">@lang('app.message')</label>
                            <textarea class="form-control" id="message" name="message" rows="7" disabled></textarea>
                        </div>
                        <div class="form-group">
                            <label for="frontend-contact-message">@lang('app.files')</label>
                            <div id="divFiles">

                            </div>
                        </div>
                    </div>
                    <div class="block-content block-content-full text-right border-top">
                        <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal"><i class="fa fa-check mr-1"></i>@lang('app.close')</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal" id="modal-send-replay" tabindex="-1" role="dialog" aria-labelledby="modal-send-replay" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title">@lang('app.send_replay')</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                <i class="fa fa-fw fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content font-size-sm">
                        {!! Form::open(['route' => 'sendReplay', 'id' => 'send-replay-form']) !!}
                        <div class="row">
                            <input type="hidden" id="contact_us_id" name="contact_us_id">
                            <div class="form-group col-12">
                                <label for="frontend-contact-msg">@lang('app.message')</label>
                                <textarea class="form-control" id="frontend-contact-msg" name="message" rows="7" required></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-paper-plane mr-1"></i> @lang('app.Send Message')
                            </button>
                        </div>
                        {!! Form::close() !!}

                        <div class="block-content block-content-full text-right border-top">
                            <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">@lang('app.close')</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('css_after')
    <link rel="stylesheet" href="{{url('/')}}/js/plugins/datatables/dataTables.bootstrap4.css">
    <link rel="stylesheet" href="{{url('/')}}/js/plugins/sweetalert2/sweetalert2.min.css">
@stop
@section('js_after')
    <script src="{{url('/')}}/js/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="{{url('/')}}/js/plugins/datatables/dataTables.bootstrap4.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#ContactUSTable').DataTable({
                "lengthMenu": [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: '{!! route('getContactUs')!!}',
                columns: [
                    {data: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'user', name: 'user'},
                    {data: 'email', name: 'email'},
                    {data: 'show_message', name: 'show_message'},
                    {data: 'send_replay', name: 'send_replay'},
                    {data: 'created_at', name: 'created_at', orderable: false,},
                ]
            });
        });
        $(document).on('click', '#show_message', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type: "POST",
                url: "{{route('getContactUs.ajax')}}",
                data: {id: id},
                success: function (data) {
                    jQuery('#modal-show-message').modal('show');
                    $('#email').empty();
                    $('#name').empty();
                    $('#mobile').empty();
                    $('#message').empty();
                    $('#divFiles').empty();


                    $('#email').val(data.email);
                    $('#name').val(data.name);
                    $('#mobile').val(data.mobile);
                    $('#message').val(data.message);
                    data.files_assets.map(function (file) {
                    var modifiedFile = file.replace('/upload/', '/upload/upload/');
                    $('#divFiles').append('<a href="' + modifiedFile + '">اضغط هنا</a> <br/>');
                });
                }
            });
        });
        $(document).on('click', '#send_replay', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            jQuery('#modal-send-replay').modal('show');
            $('#contact_us_id').val(id);
        });
    </script>
@stop
