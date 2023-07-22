<?php

namespace App\Jobs;

use App\Models\ApplicantAdmissionRequest;
use App\Models\ApplicationFeeRequest;
use App\Models\FeePayment;
use App\Models\PaymentConfiguration;
use App\Models\PaymentLog;
use App\Models\Program;
use App\Models\User;
use App\Models\UserProfile;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class SubmitAdminAdmissionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    private $name;
    private $progId;
    private $progchoice;
    private $gender;
    private $maritalstatus;
    private $dob;
    private $email;
    private $nationality;
    private $stateorigin;
    private $stateid;
    private $lga;
    private $gsm;
    private $occupation;
    private $staff;

    public function __construct($name, $progId, $progchoice, $gender, $maritalstatus, $dob, $email, $nationality, $stateorigin, $stateid, $lga, $gsm, $occupation, $staff)
    {
        $this->name = $name;
        $this->progId = $progId;
        $this->progchoice = $progchoice;
        $this->gender = $gender;
        $this->maritalstatus = $maritalstatus;
        $this->dob = $dob;
        $this->email = $email;
        $this->nationality =  $nationality;
        $this->stateorigin = $stateorigin;
        $this->stateid = $stateid;
        $this->lga =  $lga;
        $this->gsm = $gsm;
        $this->occupation = $occupation;
        $this->staff = $staff;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info("<<-------------------Begining Admission Upload---------------------->> ");
        Log::info("Begining Admission Upload for ". $this->email);

        Log::info("Finding Applicant Programme");
            $programme = Program::find($this->progId);
        Log::info("Applicant Programme Found to be - ". $programme->name);

        Log::info("Creating Account ...");
            # Create User
            $user = User::create([
                'name' => $this->name,
                'email' =>$this->email,
                'phone_number' => $this->gsm,
                'application_no' => generateApplicationNumber(),
                'password' => Hash::make($this->email),
            ]);

            #Assign applicant role to user
            $user->assignRole(config('app.roles.applicant'));
            #Assign Admitted role to applicant
            $user->assignRole('admitted');

            #create user_profile for the user
            $profile = UserProfile::updateOrCreate(['user_id' => $user->id], [
                'applicant_program' => $programme->id,
                // 'is_serving_officer' => $user->profile->is_serving_officer ?? "0",
                // 'service_number' => $user->profile->service_number ?? null,
                // 'service_rank' => $user->profile->service_rank ?? null,
                'user_id' => $user->id,
                'marital_status' => $this->maritalstatus,
                'gender' => $this->gender,
                'nationality' => $this->nationality,
                'local_government' => $this->lga,
                'state_id' => $this->stateid,

            ]);

        Log::info("Generating Payment Records");
            #Get the payment configuration
            $applicationFeeConfiguration = PaymentConfiguration::where('payment_purpose_slug', 'application-fee')->first();
            # Generate the tx_id for all transactions here
            $transactionId = generateUniqueTransactionReference();
            # Generate the uid for all transactions here
            $uuid = uniqid('fm');
            # Enter record in Application_fee_request
            $transaction = ApplicationFeeRequest::updateOrCreate(['payee_id' => $user->id, 'session_id' => getApplicationSession()], [
                'amount' => $applicationFeeConfiguration->amount,
                'payee_id' => $user->id,
                'txn_id' => $transactionId,
                'checksum' => $transactionId,
                'uid' => $uuid,
                'session_id' => getApplicationSession(),
                'status' => "paid",
            ]);
            #
            $feeRequest = ApplicationFeeRequest::where('payee_id', $user->id)->first();

            $feePaymentTransaction = FeePayment::updateOrCreate([
                'user_id' => $user->id,
                'payment_config_id' => $applicationFeeConfiguration->id,
                'academic_session_id' => getApplicationSession(),
            ],[
                'amount_billed' => $applicationFeeConfiguration->amount,
                'user_id' => $user->id,
                'payment_config_id' => $applicationFeeConfiguration->id,
                'academic_session_id' => getApplicationSession(),
                'payment_status' => config('app.status.paid'),
                'amount_paid' => $applicationFeeConfiguration->amount,
                'uid' => $uuid,
                'balance' => 0,
                'txn_id' => $transactionId,
            ]);

            PaymentLog::create([
                'fee_payment_id' => $feePaymentTransaction->id,
                'amount_paid' => $feePaymentTransaction->amount_paid,
                'uid' => $uuid,
                'tx_id' => $transactionId,
                'payment_channel' => "Manual Payment",
            ]);

        Log::info("Payment Records generated successfully");

        #generate form number
        $formNumber = getformNumber();

        $actionat = now();

        Log::info("Application Number generated successfully");
            #generate Admission details
            $admissionDetails = ApplicantAdmissionRequest::updateOrCreate(['user_id'=>$user->id, 'session_id'=> getApplicationSession()],[
                'user_id'=>$user->id,
                'session_id' => getApplicationSession(),
                'program_id' => $programme->id,
                'uid' => uniqid('NDAPG_'),
                'form_number'=> $formNumber,
                'pg_coord' => 1,
                'hod' => 1,
                'dean' => 1,
                'dean_spgs' => 1,
                'is_admitted' => 1,
                'is_submitted' => 1,
                'pg_coord_at' => $actionat,
                'hod_at' => $actionat,
                'dean_at' => $actionat,
                'dean_spgs_at' => $actionat,
                'admitted_at' => $actionat,
                'submitted_at' => $actionat,
                'pg_coord_by' => $this->staff,
                'hod_by' => $this->staff,
                'dean_by' => $this->staff,
                'dean_spgs_by' => $this->staff,
                'admitted_by' => $this->staff,


            ]);



        Log::info("Generating Application Entry");

        Log::info("Application Entry generated successfully");
        Log::info("Account Creation Completed - ".$admissionDetails->id);

        Log::info("Generating Application Number");
            #update new Application Number in user table
            $user->application_no = $formNumber;
            $user->username = $formNumber;
            $user->save();
        Log::info($formNumber);
        Log::info("Applicaton Number Generated Successfully");
        Log::info("Admitting Candidate");
        Log::info("Admission Completed Successfully");
        Log::info("Notifying Candidate");
            #Send notification job
            $scTime = Carbon::now()->addMinutes(30);
            AdmissionNotificationJob::dispatch($admissionDetails->id)->delay($scTime);
        Log::info("Candidate Scheduled for Notice Successfully !!! ");
        Log::info("<<-------------------Completed Admission Upload Successfully !!!---------------------->> ");


    }
}
