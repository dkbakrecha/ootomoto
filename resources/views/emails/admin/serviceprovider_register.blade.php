@component('mail::message')
#Hello Admin!

A new service provider registered to flair. Please view and verify account.<br>
<table border="1">
    <tr>
        <td width="150px">Unique Id</td>
        <td width="150px">{{ $providerData->unique_id }}</td>
    </tr>

    <tr>
        <td>Name</td>
        <td>{{ $providerData->name }}</td>      
    </tr>

    <tr>
        <td>Address</td>
        <td>{{ $providerData->address }}</td>
    </tr>

    <tr>
        <td>In-charge</td>
        <td>{{ $providerData->incharge_name }}</td>
    </tr>

    <tr>
        <td>Phone</td>
        <td>{{ $providerData->phone }}</td>
    </tr>
    <tr>
        <td>Email Address</td>
        <td>{{ $providerData->email }}</td>
    </tr>

    <tr>
        <td>Map</td>
        <td><a href="{{ $providerData->map }}" target="_BLANK">View Map</a></td>
    </tr>

    <tr>
        <td width="150px">Owner Name</td>
        <td width="150px">{{ $providerData->owner_name }}</td>
    </tr>

    <tr>
        <td>Owner Phone</td>
        <td>{{ $providerData->owner_phone }}</td>
    </tr>

    <tr>
        <td>CR#</td>
        <td>{{ $providerData->crn }}</td>
    </tr>

    <tr>
        <td>Lincense#</td>
        <td>{{ $providerData->lincense }}</td>
    </tr>

    <tr>
        <td>Services</td>
        <td>
            {{ implode (", ", $services) }}
        </td>
    </tr>

    <tr>
        <td>Services For</td>
        @php
        $serviceFor = array();
        if($providerData->man == 1){
        $serviceFor[] = 'Men';
        }

        if($providerData->women == 1){
        $serviceFor[] = 'Women';
        }

        if($providerData->kid == 1){
        $serviceFor[] = 'Kids';
        }
        @endphp

        <td>
            {{ implode (", ", $serviceFor) }}
        </td>
    </tr>

    <tr>
        <td>Payment Methods</td>
        <td>{{ ($providerData->accept_payment != 0)?(($providerData->accept_payment == 1)?"Cash":"Card"):"Both" }}</td>
    </tr>

    <tr>
        <td>Booking Approval Mode</td>
        <td>{{ ($providerData->auto_approve == 1)?"Manually":"Auto" }}</td>
    </tr>

    @if(!empty($images))
    <tr>
        <td>Photos</td>
        <td>

            @foreach($images as $image)
            <div style="width:100px; float: left; margin-right: 5px;">
                <img src="{{ asset("images/shop") . "/" . $image->filename }}" width="100px">
            </div>
            @endforeach

        </td>
    </tr>    
    @endif
    
    <tr>
        <td>Comment</td>
        <td>{{ $providerData->comment }}</td>
    </tr>
</table>
<br>
You can approve this service provider and update information from Admin dashboard's user management section.

Regards,<br>
Team {{ config('app.name') }}
@endcomponent