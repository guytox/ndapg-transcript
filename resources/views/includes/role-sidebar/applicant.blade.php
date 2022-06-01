@role('applicant')

<li>
    <a href="{{ route('application.fee') }}" class="@if (Request::is('application.fee')) active @endif waves-effect">
        <i class="mdi mdi-cash-marker"></i>
        <span>Payment</span>
    </a>
</li>

<li>
    <a href="{{ route('applicant.profile') }}" class=" waves-effect">
        <i class="mdi mdi-face"></i>
        <span>Profile</span>
    </a>
</li>

@endrole
