    <li class="menu-item {{ $route == 'cashier.home' ? 'current' : '' }} "><a class="menu-link"
            href="{{ route('cashier.home') }}">
            <div>Home</div>
        </a></li>
    <li
        class="menu-item {{ $route == 'sales.index' ? 'current' : '' }} {{ $route == 'fund_transfer.index' ? 'current' : '' }} {{ $route == 'sales.all.index' ? 'current' : '' }} {{ $route == 'credit.index' ? 'current' : '' }}">
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
    <li class="menu-item {{ $route == 'expense.index' ? 'current' : '' }} "><a class="menu-link"
            href="{{ route('expense.index') }}">
            <div>Expense</div>
        </a></li>

    <li
        class="menu-item  {{ $route == 'customers.index' ? 'current' : '' }} {{ $route == 'customers.profile' ? 'current' : '' }}">
        <a class="menu-link" href="#">
            <div>Customers</div>
        </a>
        <ul class="sub-menu-container">
            <li
                class="menu-item {{ $route == 'customers.index' ? 'current' : '' }} {{ $route == 'customers.profile' ? 'current' : '' }}">
                <a class="menu-link" href="{{ route('customers.index') }}">
                    <div>Customers</div>
                </a></li>
            <li class="menu-item {{ $route == 'cashier.salary_advance.index' ? 'current' : '' }}">
                <a class="menu-link" href="{{ route('cashier.salary_advance.index') }}">
                    <div>Salary Advance</div>
                </a>
            </li>

            <li class="menu-item {{ $route == 'cash_credits.index' ? 'current' : '' }}">
                <a class="menu-link" href="{{ route('cash_credits.index') }}">
                    <div>Cash Credits</div>
                </a>
            </li>


        </ul>
    </li>