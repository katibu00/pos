
    {{-- <li class="menu-item {{ $route == 'data-sync.index' ? 'current' : '' }}"><a class="menu-link"
            href="{{ route('data-sync.index') }}">
            <div>Data Synch</div>
        </a></li> --}}


     <li
        class="menu-item {{ $prefix == '/dashboard' ? 'current' : '' }}">
        <a class="menu-link" href="#">
            <div>Home</div>
        </a>
        <ul class="sub-menu-container">
            <li
                class="menu-item {{ $route == 'admin.home' ? 'current' : '' }}">
                <a class="menu-link" href="{{ route('admin.home') }}">
                    <div>Home (All)</div>
                </a>
            </li>

            <li class="menu-item {{ $route == 'admin.select_cashier' ? 'current' : '' }}">
                <a class="menu-link" href="{{ route('admin.select_cashier') }}">
                    <div>Home (Cashier)</div>
                </a>
            </li>
            
        </ul>
    </li>


    <li
        class="menu-item {{ $route == 'report.index' ? 'current' : '' }} {{ $route == 'report.generate' ? 'current' : '' }}">
        <a class="menu-link" href="{{ route('report.index') }}">
            <div>Report</div>
        </a>
    </li>
    {{-- <li class="menu-item {{ $route == 'expense.index' ? 'current' : '' }} "><a class="menu-link"
            href="{{ route('expense.index') }}">
            <div>Expense</div>
        </a></li> --}}


    <li class="menu-item {{ request()->is('expenses*') ? 'current' : '' }}">
        <a class="menu-link" href="#">
            <div>Expenses</div>
        </a>
        <ul class="sub-menu-container">
            <li class="menu-item {{ request()->is('expenses/deposits*') ? 'current' : '' }}">
                <a class="menu-link" href="{{ route('expenses.deposits') }}">
                    <div>Deposits</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('expenses/records*') ? 'current' : '' }}">
                <a class="menu-link" href="{{ route('expenses.records') }}">
                    <div>Record Expenses</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('expenses/reports*') ? 'current' : '' }}">
                <a class="menu-link" href="{{ route('expenses.reports') }}">
                    <div>Reports</div>
                </a>
            </li>
        </ul>
    </li>

    <li
        class="menu-item {{ $prefix == '/online-store' ? 'current' : '' }}">
        <a class="menu-link" href="#">
            <div>Online Store</div>
        </a>
        <ul class="sub-menu-container">
            <li
                class="menu-item {{ $route == 'online-store.products' ? 'current' : '' }}">
                <a class="menu-link" href="{{ route('online-store.products') }}">
                    <div>Products</div>
                </a>
            </li>

            <li class="menu-item {{ $route == 'categories.index' ? 'current' : '' }}">
                <a class="menu-link" href="{{ route('categories.index') }}">
                    <div>Categories</div>
                </a>
            </li>
            
        </ul>
    </li>

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
        class="menu-item {{ $route == 'transactions.index' ? 'current' : '' }} {{ $route == 'estimate.all.index' ? 'current' : '' }} {{ $route == 'fund_transfer.index' ? 'current' : '' }} {{ $route == 'sales.all.index' ? 'current' : '' }} {{ $route == 'credit.index' ? 'current' : '' }}">
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
        class="menu-item {{ $route == 'customers.index' || $route == 'admin.salary_advance.index' || $route == 'customers.profile' || $route == 'suppliers.index' || $route == 'debtors.index' || $route == 'users.index' ? 'current' : '' }}">
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
            <li class="menu-item {{ $route == 'debtors.index' ? 'current' : '' }}">
                <a class="menu-link" href="{{ route('debtors.index') }}">
                    <div>Debtors</div>
                </a>
            </li>
        </ul>
    </li>