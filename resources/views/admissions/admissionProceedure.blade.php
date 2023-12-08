@extends('app-index')

@section('content')

    @include('includes.messages')

    <legend dir="ltr" style='font-variant: small-caps; font-size: larger;'>Application Proceedure</legend>
    <div style='font-size: medium'>
        <dl>
            <dt>Register your account(Step 1)</dt>
                <ol>
                    <li>To begin the process Click here or follow this link <a href='https://spgs.nda.edu.ng/register' target="_blank"><b>https://spgs.nda.edu.ng/register</b></a></li>
                    <li> Provide your name, email and password</li>
                    <li> Check your email inbox and click on the verification link sent to you to activate your account</li>
                    <li> Note: You will not be able to proceed until you have verified your email</li>
                </ol>
                <dt>Make your payment (Step 2)</dt>
                <ol>
                    <li> Login to your application portal by providing the email and password in step 1 here</li>
                    <li> Click on “payments”</li>
                    <li> You will be redirected to the payment page where you will make your payment</li>
                    <li>use any of the available payment options “Card” or “Bank Transfer” and complete the payment process </li>
                    <li> You will be rediected from the payment page back to the portal </li>
                    <li> Click on payment again after you have been redirected and you will see your receipt </li>
                </ol>
                <dt>Update Profile (Step 3)</dt>
                <ol>
                    <li>Login to your application portal using your email and password</li>
                    <li>Click on “Profile”</li>
                    <li>Then click on “Bio-data”</li>
                    <li>Provide your “Date of Birth”, “Phone Number”, “Gender”, “Marital Status” and upload your “Passport Photograph”</li>
                    <li>Click on “Submit Biodata”</li>
                    <li>Next, click on “Contact Details”</li>
                    <li>Provide your contact address and permanent home address</li>
                    <li>Click on “Save Contact Details”</li>
                    <li>Next, click on “Update Personal Details”</li>
                    <li>Provide your full name, Nationality, State, Local Government, and Home town</li>
                    <li>Click on “Save Personal Details”</li>
                </ol>

            <dt>Choose Your Prefered Programme (Step 4)</dt>
                <ol>
                    <li>To check available courses, click <a href="programmes.html">here</a></li>
                    <li>Login to your application portal using your email and password</li>
                    <li>Click on “Academics”</li>
                    <li>Next, click on “Select Programme”</li>
                    <li>Select faculty, department, and programme</li>
                    <li>Select the appropriate service records (This section is for serving Military Personnel alone), ignore if you are not one.</li>
                    <li>Click on “Save Programme Details”</li>

                </ol>
            <dt>Provide O'Level Result details (Step 5)</dt>
                <ol>
                    <li>(a) Click on “Academics”</li>
                    <li>(b) Click on “Add O’level Result”</li>
                    <li>(c) Select “Exam Body”, “Examination type”, and “Examination year”</li>
                    <li>(d) Choose sitting “first” or “second” (Note: you can only upload two sittings)</li>
                    <li>(e) Provide your grade for “English Language” and “Mathematics”</li>
                    <li>(f) Provide three other relevant subjects and their grades</li>
                    <li>(g) Click on “Submit O level result”</li>
                    <li>Repeat steps (b) to (g) above to upload your second sitting. (Note: remember to choose “second sitting”)</li>

                </ol>
            <dt>Add O'Level Verication Cards (Step 6)</dt>
                <ol>
                    <li>You are required to upload an O Level verification card for each sitting you have uploaded</li>
                    <li>Click on “Academics”</li>
                    <li>Then click on “Add Verification Cards”</li>
                    <li>Specify the exam details, card pin, and card serial number</li>
                    <li>Choose the sitting “first” or “second”</li>
                    <li>Click on “Submit Verification Card”</li>
                    <li>Follow the process above for the “second” sitting</li>

                </ol>
            <dt>Add Referees (Step 7)</dt>
                <ol>
                    <li>You need a minimum of two referees</li>
                    <li>Click on “Referee”</li>
                    <li>Provide Referee’s name</li>
                    <li>Provide Referee’s valid e-mail</li>
                    <li>Note: Your referee will get an email with a link to fill in the online referee form that will take only two minutes to fill.</li>
                    <li>Click on “Submit Confidential Referee”</li>
                    <li>Follow the process above to add the second referee</li>
                    <li>Note: You will be able to delete and add another referee provided any of your nominated referees has not responded</li>
                    <li>Note: You will be able to check if they have responded or not on the same page below</li>

                </ol>
            <dt>Provide Your Academic qualifications (Step 8)</dt>
                <ol>
                    <li>Click on “Qualifications”</li>
                    <li>Next, click on “School”</li>
                    <li>Provide “Certificate Type”, “Awarding Institution”, “Qualification Obtained”, “Class”, and “Year Obtained”</li>
                    <li>Click on “Save”</li>
                    <li>Follow the above steps to add all your academic qualifications</li>

                </ol>
            <dt>Provide your professional qualifications (Step 9)</dt>
                <ol>
                    <li>Click on “Qualifications”</li>
                    <li>Next, click on “Professional”</li>
                    <li>Provide “Certificate Type”, “Awarding Institution”, “Qualification Obtained”, “Certificate Number (No)”, “Issue Date”, and “Expiry Date”</li>
                    <li>Click on “Save”</li>
                    <li>Follow the above steps to add all your professional qualifications</li>
                </ol>
                    </ol>

                    <dt>Preview your application form (Step 10)</dt>
                <ol>
                    <li>Note: You will not be allowed to preview your application form until the following have been provided:
                        <ol type="1">
                            <li>You have made payment</li>
                            <li>You have provided everything in the Profile section</li>
                            <li>You have uploaded at least one O’level sitting</li>
                            <li>You have provided O’level verification cards for all uploaded O Level exam sittings</li>
                            <li>You have provided at least two referees, and all of them have responded (please remove and add other referees as the need may be)</li>
                            <li>You have provided at least two academic qualifications</li>
                            <li>You have provided uploads (if required)</li>
                        </ol>
                    </li>
                    <li>After Preview, you can go back to edit any section that is in error</li>

                    </ol>

                    <dt>Submit Your Form (Step 11)</dt>
                <ol>
                    <li>To submit your form</li>
                    <li>Click on "Submit Form"</li>
                    <li>Follow the instructions to complete the submission process</li>
                    <li>Note: You cannot make any changes to your application form after you have submitted. You are therefore advised to check and recheck to correct any wrong details before submission</li>
                </ol>
                    </ol>



        </dl>
        <ol>
            <li>If you have any inquiry or are encountering any difficulty,The help lines are 09120353957 or 08164403543 (08:00am to 6:00pm) or send an e-m@il to <a href="mailto:pgenquiries@nda.edu.ng"><b>pgenquiries@nda.edu.ng</b></a></li>
        </ol>
    </div>


@endsection

