@role('admin')


{{-- Academic Configs --}}
<li>
    <a href="#" class="@if (Request::is('attendee.payment')) active @endif has-arrow waves-effect">
        <i class="mdi mdi-tag-heart"></i>
        <span>Academic Configs</span>
    </a>
    <ul>
        <li>
            <a href="{{ route('faculties.index') }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>Facuties</span>
            </a>
        </li>
        <li>
            <a href="{{ route('departments.index') }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>Departments</span>
            </a>
        </li>
        <li>
            <a href="{{ route('programs.index') }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>Programmes</span>
            </a>
        </li>
        <li>
            <a href="{{ route('studylevels.index') }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>StudyLevels</span>
            </a>
        </li>

        <li>
            <a href="{{ route('semestercourses.index') }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>Semester Courses</span>
            </a>
        </li>

        <li>
            <a href="{{ route('curricula.index') }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>Curriculum Config</span>
            </a>
        </li>

        <li>
            <a href="{{ route('acadsessions.index') }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>Academic Sessions</span>
            </a>
        </li>

        <li>
            <a href="{{ route('gradingsystems.index') }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>Grading Systems</span>
            </a>
        </li>


    </ul>
</li>

{{-- Payments --}}

<li>
    <a href="#" class="@if (Request::is('attendee.payment')) active @endif has-arrow waves-effect">
        <i class="mdi mdi-tag-heart"></i>
        <span>Payment</span>
    </a>
    <ul>
        <li>
            <a href="{{ route('faculties.index') }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
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

        <li>
            <a href="{{ route('student.paymentupload.form') }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>Upload Student Payment Details</span>
            </a>
        </li>

    </ul>
</li>

{{-- Appointments --}}

<li>
    <a href="#" class="@if (Request::is('attendee.payment')) active @endif has-arrow waves-effect">
        <i class="mdi mdi-tag-heart"></i>
        <span>Appointments</span>
    </a>
    <ul>
        <li>
            <a href="{{ route('appointments.get.deans') }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>Deans/Directors</span>
            </a>
        </li>
        <li>
            <a href="{{ route('appointments.get.hods') }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>HOD's/EO/RO</span>
            </a>
        </li>
        <li>
            <a href="#" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>Appoint Staff</span>
            </a>
        </li>



    </ul>
</li>

{{-- Manage Students --}}

<li>
    <a href="#" class="@if (Request::is('attendee.payment')) active @endif has-arrow waves-effect">
        <i class="mdi mdi-book-information-variant"></i>
        <span>Manage Students</span>
    </a>
    <ul>
        <li>
            <a href="{{ route('view.all.active') }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>List Students</span>
            </a>
        </li>
        <li>
            <a href="{{ route('student.upload.form') }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>Upload Student list</span>
            </a>
        </li>

        <li>
            <a href="{{ route('student.admissionoffer.form') }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>Upload Admission list</span>
            </a>
        </li>

    </ul>
</li>

{{-- Manage Staff --}}

<li>
    <a href="#" class="@if (Request::is('attendee.payment')) active @endif has-arrow waves-effect">
        <i class="mdi mdi-book-information-variant"></i>
        <span>Manage Staff</span>
    </a>
    <ul>
        <li>
            <a href="{{ route('stafflist.index') }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>List Staff</span>
            </a>
        </li>
        <li>
            <a href="{{ route('rolemanagement.index') }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>Add Roles</span>
            </a>
        </li>

    </ul>
</li>


{{-- Password Update --}}
<li>
    <a href="{{ route('update-userpass') }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
        <i class="mdi mdi-lock font-size-16 align-middle mr-1"></i>
        <span>User Password Update</span>
    </a>
</li>


{{-- Manage Emails --}}
<li>
    <a href="#" class="@if (Request::is('attendee.payment')) active @endif active has-arrow waves-effect">
        <i class="mdi mdi-book-information-variant"></i>
        <span>Manage Emails</span>
    </a>
    <ul>
        <li>
            <a href="{{ route('faculties.index') }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>Upload Student Emails</span>
            </a>
        </li>
        <li>
            <a href="{{ route('faculties.index') }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>View/Revoke Emails</span>
            </a>
        </li>

    </ul>
</li>

{{-- Upload Student Balances --}}

<li>
    <a href="#" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
        <i class="mdi mdi-book-information-variant"></i>
        <span>Upload Student Balances</span>
    </a>
</li>




@endrole
