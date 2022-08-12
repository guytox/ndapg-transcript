<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Program;
use App\Models\StudyLevel;
use Illuminate\Database\Seeder;
use Exception;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $programs = [

            ['900','PhD Geography (Urban)','Ph.D.','Geography','academic'],
            ['900','PhD Geography (Remote Sensing & GIS)','Ph.D.','Geography','academic'],
            ['900','PhD Geography (Population)','Ph.D.','Geography','academic'],
            ['900','PhD Geography (Environmental Management)','Ph.D.','Geography','academic'],
            ['900','PhD Geography (Climatology)','Ph.D.','Geography','academic'],
            ['800','MSc Geography (Urban)','M.Sc.','Geography','academic'],
            ['800','MSc Geography (Remote Sensing & GIS)','M.Sc.','Geography','academic'],
            ['800','MSc Geography (Military Geography)','M.Sc.','Geography','academic'],
            ['800','MSc Geography (Environmental Management)','M.Sc.','Geography','academic'],
            ['800','MSc Geography (Climatology)','M.Sc.','Geography','academic'],
            ['700','Postgraduate Diploma in Remote Sensing and Geographic Information System (PGDRS/GIS)','PGD.','Geography','professional'],
            ['700','Postgraduate Diploma in Environmental Management (PGDEM)','PGD.','Geography','professional'],
            ['800','Master in Geographic Information System (MGIS)','Masters','Geography','professional'],
            ['800','Master in Enviromental Management (MEM)','Masters','Geography','professional'],
            ['700','Postgraduate Diploma in French Studies (PGDFS)','PGD.','Languages','professional'],
            ['900','PhD International Studies','Ph.D.','History And War Studies','academic'],
            ['900','PhD History (Military History)','Ph.D.','History And War Studies','academic'],
            ['800','MA International Studies','M.A.','History And War Studies','academic'],
            ['800','MA History (Military History)','M.A.','History And War Studies','academic'],
            ['700','Postgraduate Diploma in Leadership Studies (PGDLS)','PGD.','History And War Studies','professional'],
            ['800','Master in Leadership Studies (MLS)','Masters','History And War Studies','professional'],
            ['800','Master in Development Studies (MDS)','Masters','History And War Studies','professional'],
            ['800','Master in Conflict Security & Development (MCSD)','Masters','History And War Studies','professional'],
            ['900','PhD Defence and Strategic Studies','Ph.D.','Political Science','academic'],
            ['800','MSc Defence and Strategic Studies','M.Sc.','Political Science','academic'],
            ['700','Postgraduate Diploma in Public Administration (PGDPA)','PGD.','Political Science','professional'],
            ['700','Postgraduate Diploma in Conflict Management and Peace Studies (PGDCMPS)','PGD.','Political Science','professional'],
            ['800','Master in Public Administration (MPA)','Masters','Political Science','professional'],
            ['800','Master in International Affairs and Strategic Studies (MIASS)','Masters','Political Science','professional'],
            ['800','Master in Conflict Management and Peace Studies (MCMPS)','Masters','Political Science','professional'],
            ['800','MSc Clinical Psychology','M.Sc.','Psychology','academic'],
            ['700','Postgraduate Diploma in Psychology  (PGDP)','PGD.','Psychology','professional'],
            ['700','Postgraduate Diploma in Counselling and Psychotherapy (PGDCP)','PGD.','Psychology','professional'],
            ['700','Postgraduate Diploma in Strategy and Security Administration (PGDSSA)','PGD.','Defence And Security Studies','professional'],
            ['900','PhD Civil Engineering (Water Resources and Environmental Engineering)','Ph.D.','Civil Engineering','academic'],
            ['900','PhD Civil Engineering (Structures)','Ph.D.','Civil Engineering','academic'],
            ['900','PhD Civil Engineering (Geotechnics)','Ph.D.','Civil Engineering','academic'],
            ['800','MEng Civil Engineering (Water Resources and Environmental Engineering)','M.Eng.','Civil Engineering','academic'],
            ['800','MEng Civil Engineering (Structures)','M.Eng.','Civil Engineering','academic'],
            ['800','MEng Civil Engineering (Geotechnics)','M.Eng.','Civil Engineering','academic'],
            ['700','Postgraduate Diploma in Construction Management (PGDCM)','PGD.','Civil Engineering','professional'],
            ['700','Postgraduate Diploma in Civil  Engineering (PGDCE)','PGD.','Civil Engineering','professional'],
            ['800','Master in Construction Management  (MCM)','Masters','Civil Engineering','professional'],
            ['900','PhD Electrical/Electronic Engineering (Electronic and Communications)','Ph.D.','Electrical Electronics Engineering','academic'],
            ['900','PhD Electrical Engineering (Power and Machine)','Ph.D.','Electrical Electronics Engineering','academic'],
            ['800','MEng Electrical/Electronic Engineering (Electronic & Communications)','M.Eng.','Electrical Electronics Engineering','academic'],
            ['800','MEng Electrical Engineering (Power and Machine)','M.Eng.','Electrical Electronics Engineering','academic'],
            ['700','Postgraduate Diploma in Electrical Engineering (PGDEE)','PGD.','Electrical Electronics Engineering','professional'],
            ['900','PhD Mechanical Engineering (Thermofluid and Energy)','Ph.D.','Mechanical Engineering','academic'],
            ['900','PhD Mechanical Engineering (Production)','Ph.D.','Mechanical Engineering','academic'],
            ['800','MEng Mechanical Engineering (Production)','M.Eng.','Mechanical Engineering','academic'],
            ['800','MEng Mechanical Engineering  (Thermofluids & Energy)','M.Eng.','Mechanical Engineering','academic'],
            ['700','Postgraduate Diploma in Mechanical Engineering (PGDME)','PGD.','Mechanical Engineering','professional'],
            ['800','Master in Production Management (MPM)','Masters','Mechanical Engineering','professional'],
            ['700','Postgraduate Diploma in Mechatronic Engineering','PGD.','Mechatronic Engineering','professional'],
            ['900','PhD Accounting','Ph.D.','Accounting','academic'],
            ['800','MSc Accounting','M.Sc.','Accounting','academic'],
            ['700','Postgraduate Diploma in Accounting and Finance (PGDAF)','PGD.','Accounting','professional'],
            ['800','Master in Taxation and Treasury Management (MTTM)','Masters','Accounting','professional'],
            ['800','Master in Forensic Accounting (MFA)','Masters','Accounting','professional'],
            ['900','PhD Economics','Ph.D.','Economics','academic'],
            ['800','MSc Economics','M.Sc.','Economics','academic'],
            ['800','Master in Health Economics (MHE)','Masters','Economics','professional'],
            ['800','Master in Financial Economics (MFE)','Masters','Economics','professional'],
            ['700','Postgraduate Diploma in Logistics and Supply Chain Management  (PGDLSCM)','PGD.','Logistics And Supply Chain Management','professional'],
            ['800','Master in Logistics and Supply Chain Management (MLSCM)','Masters','Logistics And Supply Chain Management','professional'],
            ['700','Postgraduate Diploma in Management (PGDM)','PGD.','Management','professional'],
            ['800','Master in Industrial and Labour Relations (MILR)','Masters','Management','professional'],
            ['800','Master in Entrepreneurship and  Business (MEB)','Masters','Management','professional'],
            ['800','Master in Business Administration (MBA)','Masters','Management','professional'],
            ['800','MSc Computer Science','M.Sc.','Computer Science','academic'],
            ['700','Postgraduate Diploma in Information Technology (PGDIT)','PGD.','Computer Science','professional'],
            ['700','Postgraduate Diploma in Computer Science (PGDCS)','PGD.','Computer Science','professional'],
            ['800','Master in Information Technology (MIT)','Masters','Computer Science','professional'],
            ['800','Master in Computer Science (MCS)','Masters','Computer Science','professional'],
            ['700','Postgraduate Diploma in Cyber Security (PGDCS)','PGD.','Cyber Security','professional'],
            ['800','Master in Cyber Security','Masters','Cyber Security','professional'],
            ['700','Postgraduate Diploma in Intelligence and Security Studies (PGDISS)','PGD.','Cyber Security','professional'],
            ['800','Master in Intelligence and Security Studies (MISS)','Masters','Cyber Security','professional'],
            ['700','Postgraduate Diploma in Intelligence and Security Studies (PGDISS)','PGD.','Intelligence And Security Science','professional'],
            ['800','Master in Intelligence and Security Studies (MISS)','Masters','Intelligence And Security Science','professional'],
            ['900','PhD Parasitology','Ph.D.','Biology','academic'],
            ['900','PhD Medicinal & Poisonous Plants Studies','Ph.D.','Biology','academic'],
            ['800','MSc Parasitology','M.Sc.','Biology','academic'],
            ['800','MSc Medicinal & Poisonous Plants Studies','M.Sc.','Biology','academic'],
            ['700','Postgraduate Diploma in Environmental Biology (PGDEB)','PGD.','Biology','professional'],
            ['900','PhD Chemistry (Environmental)','Ph.D.','Chemistry','academic'],
            ['900','PhD Chemistry (Analytical)','Ph.D.','Chemistry','academic'],
            ['800','MSc Chemistry (Organic)','M.Sc.','Chemistry','academic'],
            ['800','MSc Chemistry (Material Science & Explosives)','M.Sc.','Chemistry','academic'],
            ['800','MSc Chemistry (Inorganic)','M.Sc.','Chemistry','academic'],
            ['800','MSc Chemistry (Environmental)','M.Sc.','Chemistry','academic'],
            ['800','MSc Chemistry (Analytical)','M.Sc.','Chemistry','academic'],
            ['700','Postgraduate Diploma in Forensic Science (PGDFS)','PGD.','Chemistry','professional'],
            ['800','Master in Science Lab Tech (MSLT)','Masters','Chemistry','professional'],
            ['800','Master in Forensic Science (MFS)','Masters','Chemistry','professional'],
            ['800','MSc Mathematics (Pure Mathematics)','M.Sc.','Mathematical Sciences','academic'],
            ['800','MSc Mathematics (Applied Mathematics)','M.Sc.','Mathematical Sciences','academic'],
            ['700','Postgraduate Diploma in Statistics and Data Analysis (PGDSDA)','PGD.','Mathematical Sciences','professional'],
            ['800','Master in Statistics and Data Analysis (MSDA)','Masters','Mathematical Sciences','professional'],
            ['900','PhD Physics (Solid State)','Ph.D.','Physics','academic'],
            ['900','PhD Physics (Nuclear and Radiation)','Ph.D.','Physics','academic'],
            ['800','MSc Physics (Theoretical)','M.Sc.','Physics','academic'],
            ['800','MSc Physics (Solid State)','M.Sc.','Physics','academic'],
            ['800','MSc Physics (Nuclear and Radiation)','M.Sc.','Physics','academic'],
            ['700','Postgraduate Diploma in Nuclear Security Safety and Safeguard (PGDNSSS)','PGD.','Physics','professional'],
            ['700','Postgraduate Diploma in Biotechnology (PGDBT)','PGD.','Biotechnology','professional'],
            ['800','Master in Biotechnology (MBT)','Masters','Biotechnology','professional'],
            ['900','PhD Defence and Strategic Studies','Ph.D.','Directorate Of Linkages And Collaboration','academic'],
        ];

        foreach ($programs as $program) {
            Program::firstOrCreate(['name' => $program[1]], [
                'name' => $program[1],
                'degree_title' => $program[2],
                'level_id' => $this->getStudyLevelByName($program[0]),
                'department_id' => $this->getDepartmentIdByName($program[3]),
                'uid' => uniqid('pr_'),
                'category' => $program[4]
            ]);
        }
    }

    public function getDepartmentIdByName($name)
    {
        $department = Department::where('name', $name)->first();

        if ($department) {
            return $department->id;
        }

        throw new Exception('Department with that name does not exist');
        return 0;
    }

    public function getStudyLevelByName($level){
        $level_id = StudyLevel::where('level', $level)->first();

        return $level_id->id;
    }
}
