@extends('layouts.app')

@section('sectionTitle', __('messages.feedback'))

@section('sectionButtons')
<div class="user-buttons">
    <span class="label label-primary">{{ __('messages.create') }}</span>
    <a href="{{ route('feedback.create') }}" type="button" class="btn btn-feedback">
        {{ __('messages.feedback') }}
    </a>
</div>
@endsection

@section('content')
@include('elements.general_top')

@include('elements.messages')

<div style="margin-top:40px;">
    <div class="row" id="feedback-board">
        @php $class = ""; @endphp
        @if( empty($feedbackData[0]) )
        @php $class = "emptysearch"; @endphp
        @endif
        
        <div class="col-xs-4 col-sm-5 col-md-4 feedback-left"> 
            <form action="{{ route('feedbacks') }}" method="GET" role="search">
                <div class="input-group feedback-search">
                    <span class="input-group-btn">
                        <button type="submit" class="btn btn-default">
                            <span class="glyphicon glyphicon-search"></span>
                        </button>
                    </span>
                    <input type="text" class="form-control" name="q"
                           placeholder="Search users" value="{{ $q }}"> 
                </div>
            </form>
            @foreach ($feedbackData as $feedback)
            <div class="left-feedbar">
                <div class="row feedback-tile" data-id="{{ $feedback->parent_id }}" data-user_id="{{ (!empty($feedback->receiver) && ($feedback->receiver->id != Auth::user()->id))?$feedback->receiver->id:$feedback->sender->id }}">
                    @if(!empty($feedback->receiver) && ($feedback->receiver->id != Auth::user()->id))
                    <div class="col-xs-3 col-sm-3 col-md-3 user-image">
                        @if(!empty($feedback->receiver->profile_image))
                        <img src="{{ asset('images/profile/' . $feedback->receiver->profile_image)  }}" width="40px">
                        @else
                        <img src="{{ asset('images/no_user.png')  }}" width="40px">
                        @endif
                    </div>        
                    <div class="col-xs-9 col-sm-9 col-md-9 left-aside">
                        <span class="direct-chat-timestamp pull-right">{{ date('M d', strtotime($feedback->created_at)) }}</span>
                        <div class="feed-name">{{ $feedback->receiver->name }}</div>     
                        <div class="feed-user-info">{{ substr($feedback->message, 0, 30) }}</div>   
                    </div>

                    @else
                    <div class="col-xs-3 col-sm-3 col-md-3 user-image">
                        @if(!empty($feedback->sender->profile_image))
                        <img src="{{ asset('images/profile/' . $feedback->sender->profile_image)  }}" width="40px">
                        @else
                        <img src="{{ asset('images/no_user.png')  }}" width="40px">
                        @endif
                    </div>        
                    <div class="col-xs-9 col-sm-9 col-md-9 left-aside">
                        <span class="direct-chat-timestamp pull-right">{{ date('M d', strtotime($feedback->created_at)) }}</span>
                        <div class="feed-name">{{ $feedback->sender->name }}</div>
                        <div class="feed-user-info">{{ substr($feedback->message, 0, 30) }}</div>
                    </div>

                    @endif
                </div>
            </div>
            @endforeach
        </div>
        <div class="col-xs-8 col-sm-7 col-md-8 feedback-right">
            @if( !empty($feedbackData[0]) )
            
            <div id="feedback-message" class="direct-chat-danger">
                <!-- This is messages area -->
            </div>
            <div class="reply-right-section">
                <form action="{{ route('feedbackReply') }}" method="post" class="form-horizontal feedbackReplyForm" id="feedbackReplyForm">
                    <div class="form-group">
                        <div class="col-sm-12">
                            <input type="hidden" id="id" name="id">
                            <input type="hidden" id="user_id" name="user_id">
                            <textarea placeholder="Click here to reply" id="replay_message" class="form-control" name="replay_message" required=""></textarea>
                        </div>
                    </div>
                    <button type="submit" id="feedbackReplyFormSubmit" class="btn btn-primary">{{ __('messages.submit') }}</button>
                </form>
            </div>
            
            @else
        
            <div class="no-search-message">
                <h3> {{ __('messages.feedback_list_empty') }} </h3>
            </div>

            @endif
        </div>
        
        
    </div>
</div>
<div class="site-pagination">
    {!! $feedbackData->links() !!}
</div>  
@endsection

@section('page-js-script')
<script type="text/javascript">
    $(document).ready(function () {
        $(".feedback-tile").click(function () {
            var _conid = $(this).data('id');
            var _user_id = $(this).data('user_id');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            jQuery.ajax({
                url: "{{ url('/getMessages') }}",
                method: 'post',
                data: {id: _conid, user_id: _user_id},
                success: function (result) {
                    $("#feedback-message").html(result);
                    $("#feedbackReplyForm #id").val(_conid);
                    $("#feedbackReplyForm #user_id").val(_user_id);
                    $('#feedback-message .direct-chat-messages').animate({
                        scrollTop: $('#feedback-message .direct-chat-messages')[0].scrollHeight}, 0);
                }
            });
        });

        $("#feedback-board").find(".feedback-tile").first().click();

        $("#feedback-board").find('.feedbackReplyForm').submit(function (e) {
            e.preventDefault();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var _replyTxt = $(this).find("#replay_message").val();

            jQuery.ajax({
                url: "{{ url('/feedbackReply') }}",
                method: 'post',
                data: $(this).serialize(),
                success: function (result) {
                    if (result.errors)
                    {
                        jQuery('.alert-danger').html('');

                        jQuery.each(result.errors, function (key, value) {
                            jQuery('.alert-danger').show();
                            jQuery('.alert-danger').append('<li>' + value + '</li>');
                        });
                    } else
                    {

                        var month = new Array();
                        month[0] = "Jan";
                        month[1] = "Feb";
                        month[2] = "Mar";
                        month[3] = "Apr";
                        month[4] = "May";
                        month[5] = "Jun";
                        month[6] = "Jul";
                        month[7] = "Aug";
                        month[8] = "Sep";
                        month[9] = "Oct";
                        month[10] = "Nov";
                        month[11] = "Dec";

                        var d = new Date();
                        var n = month[d.getMonth()] + " " + d.getDate() + ", " + d.getFullYear();

                        $("#feedback-board").find('.direct-chat-messages').append('<div class="direct-chat-msg"><div class="direct-chat-info clearfix"><span class="direct-chat-timestamp">' + n + '</span></div><div class="direct-chat-text">' + _replyTxt + '</div></div>');
                        $("#feedback-board").find("#replay_message").val("");
                        $('#feedback-message .direct-chat-messages').animate({
                            scrollTop: $('#feedback-message .direct-chat-messages')[0].scrollHeight}, 0);
                        $.notify({
                            // options
                            message: 'Reply sent successfully'
                        }, {
                            // settings
                            type: 'success'
                        });
                    }
                }});
        });
    });
</script>
@endsection