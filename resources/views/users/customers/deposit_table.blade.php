<div class="table-responsive border">
    <table class="deposit_table table" style="width:100%; font-size: 12px;">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Date</th>
                <th scope="col">Amount</th>
                <th scope="col">Method</th>
                <th></th>
            </tr>
        </thead>
        @forelse ($deposits as $key => $deposit)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $deposit->created_at->diffForHumans() }}</td>
            <td>{{ number_format($deposit->payment_amount, 0) }}</td>
            <td>{{ ucfirst($deposit->payment_method) }}</td>
            <td>
                <button type="button" onclick="editDeposit('{{ $deposit->id }}', '{{ $deposit->payment_amount }}')"
                    class="btn btn-secondary btn-sm"><i class="fa fa-edit text-white"></i></button>
                <button type="button" onclick="PrintReceiptContent('{{ $deposit->id }}')"
                    class="btn btn-secondary btn-sm"><i class="fa fa-print text-white"></i></button>
            </td>
        </tr>
        

        @empty
            <tr>
                <td colspan="4" class="bg-danger text-white"> No Records Found</td>
            </tr>
        @endforelse

        <tbody>
        </tbody>
    </table>
</div>