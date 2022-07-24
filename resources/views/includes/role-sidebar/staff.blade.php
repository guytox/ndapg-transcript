@role('staff')



@role('hod|dean|reg_officer|exam_officer')

<li>
    <a href="#" class="@if (Request::is('attendee.payment')) active @endif has-arrow waves-effect">
        <i class="mdi mdi-tag-heart"></i>
        <span>Registration Mgt.</span>
    </a>
    <ul>
        <li>
            <a href="{{ route('reg.index') }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>Approval Queue</span>
            </a>
        </li>
        <li>
            <a href="{{ route('reg.approvals') }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>Approved Registrations</span>
            </a>
        </li>

    </ul>
</li>

<li>
    <a href="#" class="@if (Request::is('attendee.payment')) active @endif has-arrow waves-effect">
        <i class="mdi mdi-tag-heart"></i>
        <span>Course Allocation</span>
    </a>
    <ul>
        <li>
            <a href="{{ route('course-allocation.index') }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>Course Allocation</span>
            </a>
        </li>

        <li>
            <a href="{{ route('reg.approvals') }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>View Previous Allocations</span>
            </a>
        </li>

    </ul>
</li>


@endrole

@role('lecturer')

<li>
    <a href="#" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
        <i class="mdi mdi-timelapse"></i>
        <span>My Lectures</span>
    </a>
</li>

@endrole


@endrole
