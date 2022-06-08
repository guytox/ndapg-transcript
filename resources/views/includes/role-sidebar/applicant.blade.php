@role('applicant')

<li>
    <a href="{{ route('application.fee') }}" class="@if (Request::is('application.fee')) active @endif waves-effect">
        <i class="mdi mdi-cash-marker"></i>
        <span>Payment</span>
    </a>
</li>

<li>
    <a href="#" class="has-arrow waves-effect">
        <i class="mdi mdi-face"></i>
        <span>Profile</span>
    </a>

    <ul>
        <li>
            <a href="#" class="@if (Request::is('applicant/profile/*')) active @endif waves-effect">
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
    <a href="#" class="has-arrow waves-effect">
        <i class="mdi mdi-briefcase"></i>
        <span>Qualifications</span>
    </a>

    <ul>

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

@endrole
