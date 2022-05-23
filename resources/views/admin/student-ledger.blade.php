@extends('layouts.setup')
@section('css')
<!-- DataTables -->
<link href="{{ asset('admin/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
{{-- <link href="{{ asset('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet" --}}
type="text/css" />
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @include('includes.messages')
                <div class="row">
                    <div class="col-sm-6">

                        <h4 class="header-title">Student Ledger Report</h4>
                        <p class="card-title-desc"> Payment Ledger for <b>{{ $student->username }}</b>

                        </p>

                    </div>
                </div>

                <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <!-- <th>S/N</th> -->
                            <th>Date</th>
                            <th>Particular</th>
                            <th>Reciept/ Reference</th>
                            <th> #Debit</th>
                            <th># Credit </th>
                            <th># Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction )
                        <tr>
                            @if($transaction->paymentLogs->count() >= 1 )
                            @foreach ($transaction->paymentLogs as $logPayment)
                            @if(checkSamePaymentUID($transaction->uid, $logPayment->uid))
                        <tr>
                            <td>{{ $logPayment->feePayment->created_at }}</td>
                            <td>{{ changeBillingPurpose($logPayment->feePayment->configuration->purpose) ?? '' }}</td>
                            <td>{{ $logPayment->uid }}</td>
                            <td>{{ number_format($logPayment->feePayment->amount_billed, 2) }}</td>
                            <td>{{ number_format($logPayment->amount_paid, 2) }} </td>
                            <td>{{ number_format(calculateBalance($logPayment->feePayment->amount_billed, $logPayment->amount_paid), 2) }} </td>
                        </tr>
                        @endif
                        @endforeach
                        @endif
                        <td>{{ $transaction->created_at }}</td>
                        <td>{{ changeBillingPurpose($transaction->configuration->purpose) ?? ''}}</td>
                        <td>{{ $transaction->uid }}</td>
                        <td>{{ number_format($transaction->amount_billed, 2) }}</td>
                        <td>{{ number_format($transaction->amount_paid, 2) }} </td>
                        <td>{{ number_format(calculateBalance($transaction->amount_billed, $transaction->amount_paid), 2) }} </td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>

            </div>
        </div>
    </div> <!-- end col -->
</div> <!-- end row -->
@endsection
@section('js')
<!-- Required datatable js -->
<script src="{{ asset('admin/assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('admin/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>


<!-- Datatable init js -->
<script src="{{ asset('admin/assets/js/pages/datatables.init.js') }}"></script>


@endsection
