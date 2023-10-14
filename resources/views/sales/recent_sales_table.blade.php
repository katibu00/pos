<div class="card recent-table">      
    <div class="card-body">
        <div class="table-responsive">
            <table class="table recent mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Transaction No</th>
                        <th>Transaction Type</th>
                        <th>Amount</th>
                        <th>Customer</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transactionData as $index => $transaction)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $transaction['transaction_no'] }}</td>
                                    <td>{{ $transaction['type'] }}</td>
                                    <td>{{ number_format($transaction['totalAmount'],0) }}</td>
                                    <td>
                                        @if ($transaction['customer'])
                                        {{ $transaction['customer']->first_name }}
                                    @else
                                        Walk-in Customer
                                    @endif

                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-secondary btn-sm print-receipt" onclick="PrintReceiptContent('{{ $transaction['transaction_no'] }}', '{{ $transaction['type'] }}')">
                                            <i class="fa fa-print text-white"></i>
                                        </button>
                                        
                                    </td>
                                </tr>
                                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
