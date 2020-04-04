<?php $notification_owner = unserialize($notification_owner);?>
<?php $original_ticket = unserialize($original_ticket);?>
<?php $ticket = unserialize($ticket);?>

@extends($email)

@section('subject')
	{{ trans('ticketit::email/globals.status') }}
@stop

@section('link')
	<a style="background: #001728; border: 15px solid #001728; font-family: sans-serif; font-size: 13px; line-height: 110%; text-align: center; text-decoration: none; display: block; border-radius: 3px; font-weight: bold; color: #ffffff" href="{{ route($setting->grab('main_route').'.show', $ticket->id) }}">
		{{ trans('ticketit::email/globals.view-ticket') }}
	</a>
@stop

@section('content')
	{!! trans('ticketit::email/status.data', [
	    'name'        =>  $notification_owner->name,
	    'subject'     =>  $ticket->subject,
	    'old_status'  =>  $original_ticket->status->name,
	    'new_status'  =>  $ticket->status->name
	]) !!}
@stop
