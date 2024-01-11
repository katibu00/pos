
    <li class="menu-item {{ $route == 'admin.home' ? 'current' : '' }}"><a class="menu-link"
            href="{{ route('admin.home') }}">
            <div>Home</div>
        </a></li>
    {{-- <li class="menu-item {{ $route == 'data-sync.index' ? 'current' : '' }}"><a class="menu-link"
            href="{{ route('data-sync.index') }}">
            <div>Data Synch</div>
        </a></li> --}}
    <li
        class="menu-item {{ $route == 'report.index' ? 'current' : '' }} {{ $route == 'report.generate' ? 'current' : '' }}">
        <a class="menu-link" href="{{ route('report.index') }}">
            <div>Report</div>
        </a>
    </li>
    <li class="menu-item {{ $route == 'expense.index' ? 'current' : '' }} "><a class="menu-link"
            href="{{ route('expense.index') }}">
            <div>Expense</div>
        </a></li>

    <li
        class="menu-item {{ $route == 'purchase.index' ? 'current' : '' }}  {{ $route == 'purchase.create' ? 'current' : '' }} {{ $route == 'purchase.details' ? 'current' : '' }} {{ $route == 'reorder.all.index' ? 'current' : '' }} {{ $route == 'reorder.index' ? 'current' : '' }}">
        <a class="menu-link" href="#">
            <div>Reorder</div>
        </a>
        <ul class="sub-menu-container">
            <li
                class="menu-item {{ $route == 'purchase.index' ? 'current' : '' }}  {{ $route == 'purchase.create' ? 'current' : '' }} {{ $route == 'purchase.details' ? 'current' : '' }}">
                <a class="menu-link" href="{{ route('purchase.index') }}">
                    <div>Self-Fulfilled</div>
                </a>
            </li>

            <li class="menu-item {{ $route == 'reorder.index' ? 'current' : '' }}">
                <a class="menu-link " href="{{ route('reorder.index') }}">
                    <div>New Reorder</div>
                </a>
            </li>
            <li class="menu-item {{ $route == 'reorder.all.index' ? 'current' : '' }}">
                <a class="menu-link" href="{{ route('reorder.all.index') }}">
                    <div>All Reorders</div>
                </a>
            </li>

        </ul>
    </li>

    <li class="menu-item {{ $route == 'stock.index' ? 'current' : '' }}"><a class="menu-link"
            href="{{ route('stock.index') }}">
            <div>Inventory</div>
        </a></li>

    <li
        class="menu-item {{ $route == 'transactions.index' ? 'current' : '' }} {{ $route == 'fund_transfer.index' ? 'current' : '' }} {{ $route == 'sales.all.index' ? 'current' : '' }} {{ $route == 'credit.index' ? 'current' : '' }}">
        <a class="menu-link" href="#">
            <div>Transactions</div>
        </a>
        <ul class="sub-menu-container">
            <li class="menu-item {{ $route == 'transactions.index' ? 'current' : '' }}">
                <a class="menu-link" href="{{ route('transactions.index') }}">
                    <div>Record Transactions</div>
                </a>
            </li>
           
            <li class="menu-item {{ $route == 'sales.all.index' ? 'current' : '' }}">
                <a class="menu-link" href="{{ route('sales.all.index') }}">
                    <div>View Sales</div>
                </a>
            </li>
            <li class="menu-item {{ $route == 'estimate.all.index' ? 'current' : '' }}">
                <a class="menu-link" href="{{ route('estimate.all.index') }}">
                    <div>View Estimates</div>
                </a>
            </li>
        <li class="menu-item {{ $route == 'returns.all' ? 'current' : '' }}">
            <a class="menu-link" href="{{ route('returns.all') }}">
                <div>View Returns</div>
            </a>
        </li>
        <li class="menu-item {{ $route == 'fund_transfer.index' ? 'current' : '' }}">
            <a class="menu-link" href="{{ route('fund_transfer.index') }}">
                <div>Funds Transfer</div>
            </a>
        </li>

        </ul>
    </li>


    <li
        class="menu-item {{ $route == 'users.index' ? 'current' : '' }} {{ $route == 'suppliers.index' ? 'current' : '' }} {{ $route == 'customers.profile' ? 'current' : '' }} {{ $route == 'admin.salary_advance.index' ? 'current' : '' }} {{ $route == 'customers.index' ? 'current' : '' }}">
        <a class="menu-link" href="#">
            <div>Users</div>
        </a>
        <ul class="sub-menu-container">

            <li
                class="menu-item {{ $route == 'customers.index' ? 'current' : '' }} {{ $route == 'customers.profile' ? 'current' : '' }}">
                <a class="menu-link" href="{{ route('customers.index') }}">
                    <div>Customers</div>
                </a>
            </li>
            <li
                class="menu-item {{ $route == 'users.index' ? 'current' : '' }} {{ $route == 'users.edit' ? 'current' : '' }}">
                <a class="menu-link" href="{{ route('users.index') }}">
                    <div>Staff</div>
                </a>
            </li>
            <li
                class="menu-item {{ $route == 'suppliers.index' ? 'current' : '' }} {{ $route == 'suppliers.edit' ? 'current' : '' }}">
                <a class="menu-link" href="{{ route('suppliers.index') }}">
                    <div>Suppliers</div>
                </a>
            </li>
            <li
                class="menu-item {{ $route == 'cash_credits.index' ? 'current' : '' }}">
                <a class="menu-link" href="{{ route('cash_credits.index') }}">
                    <div>Cash Credits</div>
                </a>
            </li>
            <li class="menu-item {{ $route == 'admin.salary_advance.index' ? 'current' : '' }}">
                <a class="menu-link" href="{{ route('admin.salary_advance.index') }}">
                    <div>Salary Advance</div>
                </a>
            </li>
        </ul>
    </li>
