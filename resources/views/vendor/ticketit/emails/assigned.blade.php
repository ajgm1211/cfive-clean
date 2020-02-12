<?php $notification_owner = unserialize($notification_owner);?>
<?php $ticket = unserialize($ticket);?>

@extends($email)

@section('subject')
	{{ trans('ticketit::email/globals.assigned') }}
@stop

@section('link')
	<a style="background: #001728; border: 15px solid #001728; font-family: sans-serif; font-size: 13px; line-height: 110%; text-align: center; text-decoration: none; display: block; border-radius: 3px; font-weight: bold; color: #ffffff" href="{{ route($setting->grab('main_route').'.show', $ticket->id) }}">
		{{ trans('ticketit::email/globals.view-ticket') }}
	</a>
@stop

@section('content')
	{!! trans('ticketit::email/assigned.data', [
		'name'      =>  $notification_owner->name,
		'subject'   =>  $ticket->subject,
		'status'    =>  $ticket->status->name,
		'category'  =>  $ticket->category->name
	]) !!}
@stop
