@role('student')

<li>
    <a href="{{ route('student.outstanding.payments') }}" class="@if (Request::is('student.outstanding.payments')) active @endif waves-effect">
        <i class="mdi mdi-tag-heart"></i>
        <span>Payment</span>
    </a>
</li>

<li>
    <a href="{{ route('edit.student.profile') }}" class=" waves-effect">
        <i class="mdi mdi-face"></i>
        <span>Profile</span>
    </a>
</li>

@endrole
