<ul style="float: left; width: 190px; position: absolute; top:140px; height: 400px; border: none; background-color:none; line-height: 40px; text-align: center">
    <li>
    <a href="{{'/'}}" @if (Request::is('/')) class="btn2" @else class="btn" @endif  >Application Home</a>
    </li>
    <li>
    <a href="{{route('academic.programmes')}}" @if (Request::is('academicProgrammes')) class="btn2" @else class="btn" @endif >Academic Programmes</a>
    </li>
    <li>
        <a href="{{route('professional.programmes')}}" @if (Request::is('professionalProgrammes')) class="btn2" @else class="btn" @endif >Professional Programmes</a>
        </li>
    <li>
    <a href="{{route('admission.requirements')}}" @if (Request::is('admissionRequirements')) class="btn2" @else class="btn" @endif >Admission Requirements</a>
    </li>
    <li>
    <a href="{{route('application.proceedure')}}" @if (Request::is('applicationProceedure')) class="btn2" @else class="btn" @endif >Application Procedure</a>
    </li>

    <li>
    <a href="/register" class="btn">Register</a>
    </li>
    <li>
    <a href="{{'/login'}}" class="btn">Log In</a>
    </li>


</ul>
