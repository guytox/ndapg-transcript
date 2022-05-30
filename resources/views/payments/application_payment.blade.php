@extends('layouts.setup')


@section('content')


    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h2 class="header-title">Click this button to proceed to payment gateway</h2>
                    <p class="card-title-desc"> </p>

                    @if(session('paymentData'))

                        <form method="post" action="https://demo.etranzact.com/webconnect/v3/caller.jsp">
                            <input type="hidden" name="TERMINAL_ID" value="{{ config('app.etranzact.terminal_id') }}">
                            <input type="hidden" name="TRANSACTION_ID" value="{{ session('paymentData')['txn_id']}}">
                            <input type="hidden" name="AMOUNT" value="{{ session('paymentData')['amount']}}">
                            <input type="hidden" name="DESCRIPTION" value="{{ session('paymentData')['description']}}">
                            <input type="hidden" name="EMAIL" value="{{ session('paymentData')['email']}}">
                            <input type="hidden" name="CURRENCY_CODE" value="NGN">
                            <input type="hidden" name="RESPONSE_URL" value="{{ session('paymentData')['responseurl']}}">
                            <input type="hidden" name="CHECKSUM" value="{{ session('paymentData')['checksum']}}">
                            <input type="hidden" name="FULL_NAME" value="{{ session('paymentData')['name']}}">
                            <input type="hidden" name="LOGO_URL" value="{{ session('paymentData')['logourl']}}">
                            <input type="hidden" name="PHONENO" value="07088877602">
                            <input type="hidden" name="PAYEE_ID" value="{{ session('paymentData')['payee_id']}}">
                            <button type="submit" class="btn btn-outline-primary">Proceed to pay</button>
                        </form>

                    @else
                        <script>
                            window.location = "/applicant/application-fee";
                        </script>
                    @endif
                    <form>

                    </form>

                </div>
            </div>




        </div>








    </div>
    </div>

    </div>


    </div>
    <!-- end row -->

@endsection

@section('js')

    <script text="text/javascript">
        if (window.performance && window.performance.navigation.type == window.performance.navigation.TYPE_BACK_FORWARD) {
            window.location.reload()
        }
    </script>

@endsection
