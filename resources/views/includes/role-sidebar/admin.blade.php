@role('admin')

<li>
    <a href="javascript: void(0);" class="has-arrow waves-effect">
        <i class="mdi mdi-email-multiple-outline"></i>
        <span>Email</span>
    </a>

</li>

<li>
    <a href="#" class="@if (Request::is('attendee.payment')) active @endif has-arrow waves-effect">
        <i class="mdi mdi-tag-heart"></i>
        <span>Payment</span>
    </a>
    <ul>
        <li>
            <a href="{{ route('view.transactions') }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>View Transactions</span>
            </a>
        </li>
        <li>
            <a href="#" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>Search/Verify Transactions</span>
            </a>
        </li>

    </ul>
</li>

<li>
    <a href="#" class="@if (Request::is('attendee.payment')) active @endif has-arrow waves-effect">
        <i class="mdi mdi-book-information-variant"></i>
        <span>Manage Students</span>
    </a>
    <ul>
        <li>
            <a href="{{ route('view.students') }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>List Students</span>
            </a>
        </li>
        <li>
            <a href="{{ route('import.user') }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>Upload Student list</span>
            </a>
        </li>

    </ul>
</li>

<li>
    <a href="#" class="@if (Request::is('attendee.payment')) active @endif active has-arrow waves-effect">
        <i class="mdi mdi-book-information-variant"></i>
        <span>Manage Emails</span>
    </a>
    <ul>
        <li>
            <a href="{{ route('import.emails') }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>Upload Student Emails</span>
            </a>
        </li>
        <li>
            <a href="{{ route('view.emails') }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>View/Revoke Emails</span>
            </a>
        </li>

    </ul>
</li>

<li>
    <a href="{{ route('import.balances') }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
        <i class="mdi mdi-book-information-variant"></i>
        <span>Upload Student Balances</span>
    </a>
</li>

<li>
    <a href="" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
        <i class="mdi mdi-timelapse"></i>
        <span>Lecturers</span>
    </a>
</li>


@endrole
