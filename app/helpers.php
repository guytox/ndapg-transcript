<?php
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
