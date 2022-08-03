@role('staff')



@role('hod|dean|reg_officer|exam_officer')

@role('dean')
    <li>Dean's Office</li>
    <li>
        <a href="#" class="@if (Request::is('attendee.payment')) active @endif has-arrow waves-effect">
            <i class="mdi mdi-tag-heart"></i>
            <span>Registration Mgt.</span>
        </a>
        <ul>
            <li>
                <a href="{{ route('reg.index',['as'=>'ityoughKiVesen']) }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                    <i class="mdi mdi-tag-heart"></i>
                    <span>Dean Approval Queue</span>
                </a>
            </li>

            <li>
                <a href="{{ route('reg.approvals',['as'=>'ityoughKiVesen']) }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                    <i class="mdi mdi-tag-heart"></i>
                    <span>Approved Registrations</span>
                </a>
            </li>

        </ul>
    </li>
@endrole

@role('hod')

    <li>HOD's Office</li>

    <li>
        <a href="#" class="@if (Request::is('attendee.payment')) active @endif has-arrow waves-effect">
            <i class="mdi mdi-tag-heart"></i>
            <span>Registration Mgt.</span>
        </a>
        <ul>
            <li>
                <a href="{{ route('reg.index',['as'=>'ityoughKiChukur']) }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                    <i class="mdi mdi-tag-heart"></i>
                    <span>HOD Approval Queue</span>
                </a>
            </li>

            <li>
                <a href="{{ route('reg.approvals',['as'=>'ityoughKiChukur']) }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
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
                <a href="{{ route('course-allocation.index',['as'=>'ityoughKiChukur']) }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                    <i class="mdi mdi-tag-heart"></i>
                    <span>Course Allocation</span>
                </a>
            </li>

        </ul>
    </li>

    <li>
        <a href="#" class="@if (Request::is('attendee.payment')) active @endif has-arrow waves-effect">
            <i class="mdi mdi-tag-heart"></i>
            <span>Result Management</span>
        </a>
        <ul>
            <li>
                <a href="{{ route('hod-confirm.index',['as'=>'ityoughKiChukur']) }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                    <i class="mdi mdi-tag-heart"></i>
                    <span>Grade Acceptance</span>
                </a>
            </li>

            <li>
                <a href="#" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                    <i class="mdi mdi-tag-heart"></i>
                    <span>View Computed Results</span>
                </a>
            </li>

        </ul>
    </li>

@endrole

@role('reg_officer')
    <li>Reg. Officer's Office</li>
    <li>
        <a href="#" class="@if (Request::is('attendee.payment')) active @endif has-arrow waves-effect">
            <i class="mdi mdi-tag-heart"></i>
            <span>Registration Mgt.</span>
        </a>
        <ul>
            @role('reg_officer')
            <li>
                <a href="{{ route('reg.index', ['as'=>'ityoughKiNgeren']) }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                    <i class="mdi mdi-tag-heart"></i>
                    <span>Reg_officer Approval Queue</span>
                </a>
            </li>
            @endrole

            <li>
                <a href="{{ route('reg.approvals',['as'=>'ityoughKiNgeren']) }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                    <i class="mdi mdi-tag-heart"></i>
                    <span>Approved Registrations</span>
                </a>
            </li>

        </ul>
    </li>


@endrole

@role('exam_officer')
    <li>Exam Officer's Office</li>
    <li>
        <a href="#" class="@if (Request::is('attendee.payment')) active @endif has-arrow waves-effect">
            <i class="mdi mdi-tag-heart"></i>
            <span>Registration Mgt.</span>
        </a>
        <ul>
            <li>
                <a href="{{ route('reg.index', ['as'=>'ityoughKiKyaren']) }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                    <i class="mdi mdi-tag-heart"></i>
                    <span>Reg_officer Approval Queue</span>
                </a>
            </li>

            <li>
                <a href="{{ route('reg.approvals',['as'=>'ityoughKiKyaren']) }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                    <i class="mdi mdi-tag-heart"></i>
                    <span>Approved Registrations</span>
                </a>
            </li>

        </ul>
    </li>


@endrole



@endrole

@role('lecturer')

<li>
    <a href="{{ route('lecturer.grading.home',['as'=>'ortesenKwagh']) }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
        <i class="mdi mdi-timelapse"></i>
        <span>Grade Allocated Courses</span>
    </a>
</li>

@endrole

@role('pay_processor')

<li>
    <a href="{{ route('student.paymentupload.form') }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
        <i class="mdi mdi-tag-heart"></i>
        <span>Upload Student Payment Details</span>
    </a>
</li>

@endrole

@endrole
