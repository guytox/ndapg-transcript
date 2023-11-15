@role('student')

{{-- <li>
    <a href="{{ route('student.outstanding.payments') }}" class="@if (Request::is('student.outstanding.payments')) active @endif waves-effect">
        <i class="mdi mdi-tag-heart"></i>
        <span>Payment</span>
    </a>
</li> --}}

<li>
    <a href="#" class="@if (Request::is('attendee.payment')) active @endif has-arrow waves-effect">
        <i class="mdi mdi-tag-heart"></i>
        <span>Payment</span>
    </a>
    <ul>
        <li>
            <a href="{{ route('student.outstanding.balances',['id'=> user()->id]) }}" class="@if (Request::is('coursereg.index')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>Pay Balances</span>
            </a>
        </li>
        <li>
            <a href="{{ route('student.payment.history',['id'=>user()->id]) }}" class="@if (Request::is('coursereg.show')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>Payment History</span>
            </a>
        </li>
        <li>
            <a href="#" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>Advance Payment</span>
            </a>
        </li>

        {{-- <li>
            <a href="#" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>Activte SemesterReg</span>
            </a>
        </li> --}}



    </ul>
</li>

<li>
    <a href="#" class="@if (Request::is('attendee.payment')) active @endif has-arrow waves-effect">
        <i class="mdi mdi-tag-heart"></i>
        <span>Course Registration</span>
    </a>
    <ul>
        <li>
            <a href="{{ route('coursereg.index') }}" class="@if (Request::is('coursereg.index')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>Current Registration</span>
            </a>
        </li>
        <li>
            <a href="{{ route('student.registration.viewAll',['id'=>user()->id]) }}" class="@if (Request::is('coursereg.show')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>Registrations History</span>
            </a>
        </li>
        <li>
            <a href="#" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>View My Curriculum</span>
            </a>
        </li>

        {{-- <li>
            <a href="#" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>Activte SemesterReg</span>
            </a>
        </li> --}}



    </ul>
</li>

{{-- <li>
    <a href="#" class=" waves-effect">
        <i class="mdi mdi-face"></i>
        <span>Profile</span>
    </a>
</li> --}}

@endrole
