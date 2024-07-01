<script src="{{ asset('js/plugins/bootstrap-notify/bootstrap-notify.min.js')}}"></script>

@if(isset ($errors) && count($errors) > 0)
    @foreach($errors->all() as $error)
        <script>
            jQuery(function () {
                One.helpers('notify', {from: 'top' ,type: 'danger', icon: 'fa fa-times mr-1', message: '{{$error}}'});
            });
        </script>
    @endforeach
@endif

@if(Session::get('success', false))
    <?php $data = Session::get('success'); ?>
    @if (is_array($data))
        @foreach ($data as $msg)
            <script>
                jQuery(function () {
                    One.helpers('notify', {from: 'top' , type: 'success', icon: 'fa fa-check mr-1', message: '{{$msg}}' });
                });
            </script>
        @endforeach
    @else
        <script>
            jQuery(function () {
                One.helpers('notify', {from: 'top' ,type: 'success', icon: 'fa fa-check mr-1', message: '{{$data}}'});
            });
        </script>
    @endif
@endif
