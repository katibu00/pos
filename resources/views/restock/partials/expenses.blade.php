<h5>Current Expenses</h5>
@if($expenses->count() > 0)
<table class="table table-striped">
    <thead>
        <tr>
            <th>Type</th>
            <th>Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach($expenses as $expense)
        <tr>
            <td>{{ ucfirst($expense->expense_type) }}</td>
            <td>₦{{ number_format($expense->amount, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<p>No expenses recorded yet.</p>
@endif

<h5>Add New Expense</h5>
<form action="{{ route('restock.store.expenses', $restock->id) }}" method="POST">
    @csrf
    <div class="mb-3">
        <label for="expense_type" class="form-label">Expense Type</label>
        <select class="form-select" id="expense_type" name="expense_type" required>
            @foreach($expenseCategories as $category)
                <option value="{{ $category }}">{{ ucfirst($category) }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label for="amount" class="form-label">Amount</label>
        <input type="number" class="form-control" id="amount" name="amount" step="0.01" min="0" required>
    </div>
    <button type="submit" class="btn btn-primary">Add Expense</button>
</form>