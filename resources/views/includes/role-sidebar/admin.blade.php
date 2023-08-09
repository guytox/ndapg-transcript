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

        <li>
            <a href="{{ route('systemvariables.index') }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>System Variables</span>
            </a>
        </li>


    </ul>
</li>

{{-- Payment Config --}}
<li>
    <a href="#" class="@if (Request::is('attendee.payment')) active @endif has-arrow waves-effect">
        <i class="mdi mdi-tag-heart"></i>
        <span>Payment Config</span>
    </a>
    <ul>
        <li>
            <a href="{{ route('fee-items.index') }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>Fee Items Config</span>
            </a>
        </li>

        <li>
            <a href="{{ route('scholarsips.index') }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>Scholarhip Types Config</span>
            </a>
        </li>

        <li>
            <a href="{{ route('fee-types.index') }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>Fee Types Config</span>
            </a>
        </li>

        <li>
            <a href="{{ route('fee-categories.index') }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>Categories Config</span>
            </a>
        </li>

        <li>
            <a href="{{ route('fee-templates.index') }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>Templates Config</span>
            </a>
        </li>

        <li>
            <a href="{{ route('fee-configs.index') }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>PG Fee Configs</span>
            </a>
        </li>

    </ul>
</li>

{{-- Payments --}}

<li>
    <a href="#" class="@if (Request::is('attendee.payment')) active @endif has-arrow waves-effect">
        <i class="mdi mdi-tag-heart"></i>
        <span>Payment Reports</span>
    </a>
    <ul>
        <li>
            <a href="{{ route('faculties.index') }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>View Transactions</span>
            </a>
        </li>
        <li>
            <a href="{{route('verify.applicant.payments')}}" class="@if (Request::is('verify.applicant.payments')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>Verify Transactions</span>
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

        <li>
            <a href="{{ route('defermentMgt.create') }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>initiate Session Defferment</span>
            </a>
        </li>

        <li>
            <a href="{{ route('defermentMgt.create') }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>initiate Semester Defferment</span>
            </a>
        </li>

        <li>
            <a href="{{ route('defermentMgt.create') }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>initiate Course Defferment</span>
            </a>
        </li>

        <li>
            <a href="{{ route('defermentMgt.index') }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>View Defferment List</span>
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
        <span>Update User Password</span>
    </a>
</li>



{{-- Change of Matric Number --}}
<li>
    <a href="{{ route('update-matric') }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
        <i class="mdi mdi-lock font-size-16 align-middle mr-1"></i>
        <span>Matric Number Change</span>
    </a>
</li>

{{-- Change of Programme --}}
<li>
    <a href="{{ route('update-programme') }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
        <i class="mdi mdi-lock font-size-16 align-middle mr-1"></i>
        <span>Change of Programme</span>
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


{{-- Registration Management--}}
<li>
    <a href="#" class="@if (Request::is('attendee.payment')) active @endif has-arrow waves-effect">
        <i class="mdi mdi-book-information-variant"></i>
        <span>Registration Mgt</span>
    </a>
    <ul>

        <li>
            <a href="{{ route('add.bulk.registration') }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>Add/Remove Bulk Course</span>
            </a>
        </li>

        <li>
            <a href="{{ route('add.single.course',['as'=>'dap']) }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>Add/Remove Single Course</span>
            </a>
        </li>

        <li>
            <a href="{{ route('call.veto.approval') }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>Veto Registration Approval</span>
            </a>
        </li>

        <li>
            <a href="{{ route('start.single.vetoreg') }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>Single Veto Registration </span>
            </a>
        </li>

        <li>
            <a href="{{ route('hod-confirm.index',['as'=>'dap']) }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>View Grading Report</span>
            </a>
        </li>

    </ul>
</li>






@endrole
