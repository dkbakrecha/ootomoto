<div class="row user-right-section">
    <div class="col-md-2 feedback-chat-img">
        @if(!empty($userData->profile_image))
        <img src="{{ asset('images/profile/' . $userData->profile_image)  }}" width="40px">
        @else
        <img src="{{ asset('images/no_user.png')  }}" width="40px">
        @endif
    </div>        
    <div class="col-md-7 chat-heading">
        <div class="chat-name">{{ $userData->name }}</div>        

        @switch($userData->user_type)
        @case(0)
        <div class="label label-primary">{{ __("messages.service_provider") }}</div>
        @break

        @case(1)
        <div class="label label-primary">{{ __("messages.supervisor") }}</div>
        @break

        @case(2)
        <div class="label label-primary">{{ __("messages.customer") }}</div>
        @break
        @default
        <div class="label label-primary">{{ __("messages.admin") }}</div>
        @endswitch

    </div>
</div>
<div class="direct-chat-messages" style="overflow-y: scroll;">
    @foreach($feedbackData as $feedback)
    <div class="direct-chat-msg {{ ($feedback['from_id'] != Auth::user()->id)?"right":"" }}" >
        <div class="direct-chat-info clearfix">
            <span class="direct-chat-timestamp">{{ date('M d, Y', strtotime($feedback['created_at'])) }}</span>
        </div>
        <div class="direct-chat-text">
            @if ($feedback['message_type'] == 1) 
            <span class="pop">
                <img  src="{{ URL::to('/') }}/images/feedback/{{ $feedback['message'] }}" width="150px">
            </span>
            @else
            {{ $feedback['message'] }}
            @endif
        </div>
    </div>
    @endforeach
</div>