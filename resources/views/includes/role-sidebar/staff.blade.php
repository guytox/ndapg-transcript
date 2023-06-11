@role('staff')


    @role('hod|dean|reg_officer|exam_officer')

            @role('dean')
                <li>Dean's Office</li>

                <li>
                    <a href="#" class="@if (Request::is('attendee.payment')) active @endif has-arrow waves-effect">
                        <i class="mdi mdi-tag-heart"></i>
                        <span>Admissions</span>
                    </a>
                    <ul>

                        <li>
                            <a href="{{ route('select.applicant.download',['as'=>'ityoughKiChukur']) }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                                <i class="mdi mdi-tag-heart"></i>
                                <span>Download Applicant List</span>
                            </a>
                        </li>

                        @role('dean')
                        <li>
                            <a href="{{ route('select.admission.applicants', ['as'=>'ityoughKiVesen']) }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                                <i class="mdi mdi-tag-heart"></i>
                                <span>Recommend Candidates</span>
                            </a>
                        </li>
                        @endrole

                        <li>
                            <a href="{{ route('reg.approvals',['as'=>'ityoughKiVesen']) }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                                <i class="mdi mdi-tag-heart"></i>
                                <span>Approved Admissions</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('reg.approvals',['as'=>'ityoughKiVesen']) }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                                <i class="mdi mdi-tag-heart"></i>
                                <span>Submitted Applications</span>
                            </a>
                        </li>

                    </ul>
                </li>


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
                        <span>Admissions</span>
                    </a>
                    <ul>

                        <li>
                            <a href="{{ route('select.applicant.download',['as'=>'ityoughKiChukur']) }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                                <i class="mdi mdi-tag-heart"></i>
                                <span>Download Applicant List</span>
                            </a>
                        </li>

                        @role('hod')
                        <li>
                            <a href="{{ route('select.admission.applicants', ['as'=>'ityoughKiChukur']) }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                                <i class="mdi mdi-tag-heart"></i>
                                <span>Recommend Candidates</span>
                            </a>
                        </li>
                        @endrole

                        <li>
                            <a href="{{ route('reg.approvals',['as'=>'ityoughKiChukur']) }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                                <i class="mdi mdi-tag-heart"></i>
                                <span>Approved Admissions</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('reg.approvals',['as'=>'ityoughKiChukur']) }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                                <i class="mdi mdi-tag-heart"></i>
                                <span>Submitted Applications</span>
                            </a>
                        </li>

                    </ul>
                </li>



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
                        <span>Admissions</span>
                    </a>
                    <ul>

                        <li>
                            <a href="{{ route('select.applicant.download',['as'=>'ityoughKiChukur']) }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                                <i class="mdi mdi-tag-heart"></i>
                                <span>Download Applicant List</span>
                            </a>
                        </li>

                        @role('reg_officer')
                        <li>
                            <a href="{{ route('select.admission.applicants', ['as'=>'ityoughKiNgeren']) }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                                <i class="mdi mdi-tag-heart"></i>
                                <span>Recommend Candidates</span>
                            </a>
                        </li>
                        @endrole

                        <li>
                            <a href="{{ route('reg.approvals',['as'=>'ityoughKiNgeren']) }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                                <i class="mdi mdi-tag-heart"></i>
                                <span>Approved Admissions</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('reg.approvals',['as'=>'ityoughKiNgeren']) }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                                <i class="mdi mdi-tag-heart"></i>
                                <span>Submitted Applications</span>
                            </a>
                        </li>

                    </ul>
                </li>

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

            <li>
                <a href="#" class="@if (Request::is('attendee.payment')) active @endif has-arrow waves-effect">
                    <i class="mdi mdi-tag-heart"></i>
                    <span>Curriculum Mgt.</span>
                </a>
                <ul>
                    <li>
                        <a href="{{ route('get.mycurricula') }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                            <i class="mdi mdi-tag-heart"></i>
                            <span>View Curriculums </span>
                        </a>
                    </li>

                </ul>
            </li>

    @endrole

    @role('lecturer')

        <li>
            <a href="{{ route('lecturer.grading.home',['as'=>'ortesenKwagh']) }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                <i class="mdi mdi-timelapse"></i>
                <span>Grade Allocated Courses</span>
            </a>
        </li>

    @endrole

    @role('admin')

        <li>
            <a href="#" class="@if (Request::is('attendee.payment')) active @endif has-arrow waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>Payment Processing</span>
            </a>
            <ul>
                <li>
                    <a href="{{route('manual.payment.verification')}}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                        <i class="mdi mdi-tag-heart"></i>
                        <span>Manual Verification</span>
                    </a>
                </li>

                <li>
                    <a href="{{route('applicant.paycode.form')}}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                        <i class="mdi mdi-tag-heart"></i>
                        <span>Upload Pay Code</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('search.paid.applicants')}}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                        <i class="mdi mdi-tag-heart"></i>
                        <span>View Uploaded Payments</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('clean.payment.log')}}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                        <i class="mdi mdi-tag-heart"></i>
                        <span>Clean Payment Logs</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('automatic.credo.verification')}}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                        <i class="mdi mdi-tag-heart"></i>
                        <span>Automatic Credo Verification</span>
                    </a>
                </li>

            </ul>
        </li>

    @endrole

    @role('admin|bursar|pay_processor|dean_pg')

        <li>
            <a href="#" class="@if (Request::is('attendee.payment')) active @endif has-arrow waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>Financial Reports</span>
            </a>
            <ul>
                <li>
                    <a href="{{route('fee.payment.report',['purpose'=>'acceptance'])}}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                        <i class="mdi mdi-tag-heart"></i>
                        <span>Acceptance Fee</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('fee.payment.report',['purpose'=>'idcard'])}}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                        <i class="mdi mdi-tag-heart"></i>
                        <span>ID Card</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('fee.payment.report',['purpose'=>'medical'])}}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                        <i class="mdi mdi-tag-heart"></i>
                        <span>Medical Fees</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('fee.payment.report',['purpose'=>'laboratory'])}}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                        <i class="mdi mdi-tag-heart"></i>
                        <span>Laboratory Fees</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('fee.payment.report',['purpose'=>'pgfees'])}}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                        <i class="mdi mdi-tag-heart"></i>
                        <span>Postgraduate Fees</span>
                    </a>
                </li>

            </ul>
        </li>

    @endrole


    @role('admin|dean_pg')

        <li>
            <a href="#" class="@if (Request::is('attendee.payment')) active @endif has-arrow waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>Admissions</span>
            </a>
            <ul>
                <li>
                    <a href="{{ route('select.applicant.download',['as'=>'ityoughKiChukur']) }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                        <i class="mdi mdi-tag-heart"></i>
                        <span>Download Applicant List</span>
                    </a>
                </li>

                @role('dean_pg')
                <li>
                    <a href="{{ route('select.admission.applicants', ['as'=>'ityoughKiChukur']) }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                        <i class="mdi mdi-tag-heart"></i>
                        <span>Approve Admissions</span>
                    </a>
                </li>
                @endrole

                <li>
                    <a href="{{ route('view.admission.list',['as'=>'ityoughKiChukur']) }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                        <i class="mdi mdi-tag-heart"></i>
                        <span>View Approved Admissions</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('view.for.veto',['as'=>'ityoughKiChukur']) }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                        <i class="mdi mdi-tag-heart"></i>
                        <span>Veto Admission</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('view.for.change.admission',['as'=>'ityoughKiChukur']) }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                        <i class="mdi mdi-tag-heart"></i>
                        <span>Change Program Admission</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('send.admission.notifications',['as'=>'ityoughKiChukur']) }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                        <i class="mdi mdi-tag-heart"></i>
                        <span>Notify Students</span>
                    </a>
                </li>

            </ul>
        </li>

        <li>
            <a href="#" class="@if (Request::is('attendee.payment')) active @endif has-arrow waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>Reports</span>
            </a>
            <ul>
                <li>
                    <a href="{{route('view.admission.reports')}}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                        <i class="mdi mdi-tag-heart"></i>
                        <span>View Admission Report</span>
                    </a>
                </li>

                <li>
                    <a href="{{route('search.registered.students')}}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                        <i class="mdi mdi-tag-heart"></i>
                        <span>View Registered Students</span>
                    </a>
                </li>

                <li>
                    <a href="{{route('search.notregistered.students')}}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                        <i class="mdi mdi-tag-heart"></i>
                        <span>Not Registered Students</span>
                    </a>
                </li>

            </ul>
        </li>

        {{-- Registration Management (for dean_pg alone--}}
<li>
    <a href="#" class="@if (Request::is('attendee.payment')) active @endif has-arrow waves-effect">
        <i class="mdi mdi-book-information-variant"></i>
        <span>Result Mgt Reports</span>
    </a>
    <ul>

        <li>
            <a href="{{ route('hod-confirm.index',['as'=>'dap']) }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>View Grading Report</span>
            </a>
        </li>

    </ul>
</li>

    @endrole


    @role('ict_support')

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

         {{-- Change of Name --}}
         <li>
            <a href="{{ route('update.username') }}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                <i class="mdi mdi-lock font-size-16 align-middle mr-1"></i>
                <span>Change of Name</span>
            </a>
        </li>


    @endrole

    @role('ict_support|admin|dean_pg')

        <li>
            <a href="#" class="@if (Request::is('attendee.payment')) active @endif has-arrow waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>App Form Reports</span>
            </a>
            <ul>
                <li>
                    <a href="{{route('view.applicant.payments')}}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                        <i class="mdi mdi-tag-heart"></i>
                        <span>View App Payments</span>
                    </a>
                </li>

                <li>
                    <a href="{{route('view.submitted.applications')}}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                        <i class="mdi mdi-tag-heart"></i>
                        <span>View Submitted Applications</span>
                    </a>
                </li>

            </ul>
        </li>


    @endrole

    @role('admin|dap|dean_pg|registry|bursary')

        <li>
            <a href="#" class="@if (Request::is('attendee.payment')) active @endif has-arrow waves-effect">
                <i class="mdi mdi-tag-heart"></i>
                <span>Admission Processing</span>
            </a>
            <ul>
                <li>
                    <a href="{{route('admission.processing.home')}}" class="@if (Request::is('attendee.payment')) active @endif waves-effect">
                        <i class="mdi mdi-tag-heart"></i>
                        <span>Clear Students</span>
                    </a>
                </li>

            </ul>
        </li>


    @endrole

@endrole
