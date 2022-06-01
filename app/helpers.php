<?php
use App\Models\FeePayment;
use App\Models\User;


function activeSession(){
    $session = \App\Models\AcademicSession::where('status', true)->first();
    if($session) {
        return $session;
    }

    throw new Exception('No active session configured');
}


function generateMatriculationNumber(object $student)
{

}

function generateApplicationNumber(){
    $applicationNo = \App\Models\MatricConfiguration::where('session_id', activeSession()->id)->first();

    if($applicationNo) {
        if($applicationNo->application_number_count === 0) {
            return $applicationNo->application_number . ($applicationNo->application_number_count + 1);
        }
        return $applicationNo->application_number . $applicationNo->application_number_count;
    }
}

function updateApplicationNumber($number)
{
    $number = substr($number, -1);

    $applicationNo = \App\Models\MatricConfiguration::where('session_id', activeSession()->id)->first();

    if($applicationNo) {
        $applicationNo->update([
            'application_number_count' => intval($number) + 1
        ]);
    }
}

function user()
{
    return Auth::user();
}

/**
 * Carbon get todays date only.
 */
function currentDate()
{
    return Carbon\Carbon::today()->toDateString();
}

function presentationFileURL($file)
{
    return asset('presentations/' . $file);
}


function generateUniqueTransactionReference()
{
    do {
        $code = random_int(100000, 999999);
    } while (FeePayment::where("txn_id", "=", $code)->first());

    return $code;
}

function generateCheckSum($amount, $transactionId, $terminalId, $responseURL, $secretKey)
{
    return md5($amount . $terminalId . $transactionId . $responseURL . $secretKey);
}

function checkSamePaymentUID($feePaymentUID, $paymentLogUID)
{
    if ($feePaymentUID === $paymentLogUID) {
        return false;
    } else {
        return true;
    }
}

function checkSamePaymentID($feePaymentID, $paymentLogID)
{
    if ($feePaymentID === $paymentLogID) {
        return false;
    } else {
        return true;
    }
}

function changeBillingPurpose($purpose)
{
    if ($purpose === config('app.constants.initial_billing')) {
        return 'Tuition';
    }

    return $purpose;
}

function calculateBalance($amountBilled, $amountPaid)
{
    return $amountBilled - $amountPaid;
}


function formatPhoneNumber($formatted_phone_number)
{
    switch ($formatted_phone_number) {
        case str_starts_with($formatted_phone_number, '0'):
            $formatted_phone_number = '234' . substr($formatted_phone_number, 1);
            break;
        case str_starts_with($formatted_phone_number, '234'):
            break;
        case substr($formatted_phone_number, 1) !== '0':
            $formatted_phone_number = '234' . $formatted_phone_number;
            break;
    }
    return $formatted_phone_number;
}

function getBalanceFromUID($uid)
{

    $feeBalance  =  FeePayment::where('uid', $uid)->first();

    if ($feeBalance) {
        return $feeBalance->balance;
    }

    throw new Exception('Invalid UID');
}

function getPaymentPurposeById($paymentConfigId){

    $purpose = PaymentConfiguration::where('id', $paymentConfigId)->first();

    return $purpose->purpose;
}

function getActiveAcademicSessionId() {
    $academicSession = \App\Models\AcademicSession::where('status',1)->first();

    if($academicSession) {
        return $academicSession->id;
    }

    abort(403, 'Session Not Configured');
}

function getPaymentConfigBySlug($configSlug){
    $slug = \App\Models\PaymentConfiguration::where('payment_purpose_slug', $configSlug)->first();

    if($slug) {
        return $slug;
    }

    \Illuminate\Support\Facades\Log::error('payment for this slug: ' . $configSlug . 'Does not exist');
    abort(403, 'An Error Occured');
}

