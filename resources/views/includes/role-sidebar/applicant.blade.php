@role('applicant')

<li>
    <a href="#" class="has-arrow waves-effect">
        <i class="mdi mdi-face"></i>
        <span>Profile</span>
    </a>

    <ul>
        <li>
            <a href="{{ route('applicants.profile.biodata') }}" class=" waves-effect">
                <i class="mdi mdi-face-recognition"></i>
                <span>Biodata</span>
            </a>
        </li>

        <li>
            <a href="{{ route('applicant.profile.contact_details') }}" class="@if (Request::is('applicant.profile.contact_details')) active @endif waves-effect">
                <i class="mdi mdi-face-outline"></i>
                <span>Contact Details</span>
            </a>
        </li>
        <li>
            <a href="{{ route('applicant.profile.personal_details') }}" class="@if (Request::is('applicant.profile.personal_details')) active @endif waves-effect">
                <i class="mdi mdi-face-profile"></i>
                <span>Personal Details</span>
            </a>
        </li>

    </ul>
</li>


<li>
    <a href="#" class="has-arrow @if (Request::is('application.referee')) active @endif waves-effect">
        <i class="mdi mdi-school-outline"></i>
        <span>Academics</span>
    </a>

    <ul>
        <li>
            <a href="{{ route('applicant.view_programme') }}" class="@if (Request::is('application.referee')) active @endif waves-effect">
                <i class="mdi mdi-cash-marker"></i>
                <span>Select Programme</span>
            </a>
        </li>

        <li>
            <a href="{{ route('applicant.add_result') }}" class="@if (Request::is('applicant.profile.contact_details')) active @endif waves-effect">
                <i class="mdi mdi-file-document-edit"></i>
                <span>Add O-Level Result</span>
            </a>
        </li>

        <li>
            <a href="{{ route('applicant.add_card') }}" class="@if (Request::is('applicant/profile/*')) active @endif waves-effect">
                <i class="mdi mdi-file-document-box-check"></i>
                <span>Add Verification Cards</span>
            </a>
        </li>

        <li>
            <a href="{{ route('applicant.view_result') }}" class="@if (Request::is('applicant.profile.contact_details')) active @endif waves-effect">
                <i class="mdi mdi-file-document-box-multiple"></i>
                <span>View O-level Result(s)</span>
            </a>
        </li>


    </ul>
</li>

<li>
    <a href="{{ route('application.fee') }}" class="@if (Request::is('application.fee')) active @endif waves-effect">
        <i class="mdi mdi-cash-marker"></i>
        <span>Payment</span>
    </a>
</li>



<li>
    <a href="{{ route('applicant.referee') }}" class="@if (Request::is('application.referee')) active @endif waves-effect">
        <i class="mdi mdi-cash-marker"></i>
        <span>Referee</span>
    </a>
</li>

<li>
    <a href="#" class="has-arrow waves-effect">
        <i class="mdi mdi-briefcase"></i>
        <span>Qualifications</span>
    </a>

    <ul>

        <li>
            <a href="{{ route('applicant.nysc') }}" class="@if (Request::is('applicant.nysc')) active @endif waves-effect">
                <i class="mdi mdi-school-outline"></i>
                <span>NYSC</span>
            </a>
        </li>

        <li>
            <a href="{{ route('applicant.qualifications.school') }}" class="@if (Request::is('applicant.profile.contact_details')) active @endif waves-effect">
                <i class="mdi mdi-school-outline"></i>
                <span>School</span>
            </a>
        </li>

        <li>
            <a href="{{ route('applicant.qualifications.professional') }}" class="@if (Request::is('applicant.profile.personal_details')) active @endif waves-effect">
                <i class="mdi mdi-file-account"></i>
                <span>Professional</span>
            </a>
        </li>

    </ul>
</li>

<li>
    <a href="{{ route('applicant.research') }}" class="@if (Request::is('application.referee')) active @endif waves-effect">
        <i class="mdi mdi-cash-marker"></i>
        <span>Research Proposal</span>
    </a>
</li>

<li>
    <a href="{{ route('preview.application',['id'=>user()->id]) }}" class="@if (Request::is('application.referee')) active @endif waves-effect">
        <i class="mdi mdi-cash-marker"></i>
        <span>Preview And Submit</span>
    </a>
</li>

@endrole

@role('admitted')


@endrole
