<li class="menu-item {{ $route == 'admin.home' ? 'current' : '' }}"><a class="menu-link" href="{{ route('admin.home') }}">
        <div>Home</div>
    </a></li>
<li class="menu-item {{ $route == 'report.index' ? 'current' : '' }} {{ $route == 'report.generate' ? 'current' : '' }}">
    <a class="menu-link" href="{{ route('report.index') }}">
        <div>Report</div>
    </a>
</li>
<li class="menu-item {{ $route == 'expense.index' ? 'current' : '' }} "><a class="menu-link"
        href="{{ route('expense.index') }}">
        <div>Expense</div>
    </a></li>

<li
    class="menu-item {{ $route == 'purchase.index' ? 'current' : '' }}  {{ $route == 'purchase.create' ? 'current' : '' }} {{ $route == 'purchase.details' ? 'current' : '' }} {{ $route == 'shopping_list.index' ? 'current' : '' }}">
    <a class="menu-link" href="#">
        <div>Purchases</div>
    </a>
    <ul class="sub-menu-container">
        <li
            class="menu-item {{ $route == 'purchase.index' ? 'current' : '' }}  {{ $route == 'purchase.create' ? 'current' : '' }} {{ $route == 'purchase.details' ? 'current' : '' }}">
            <a class="menu-link" href="{{ route('purchase.index') }}">
                <div>Purchases</div>
            </a>
        </li>
        <li class="menu-item {{ $route == 'shopping_list.index' ? 'current' : '' }}">
            <a class="menu-link" href="{{ route('shopping_list.index') }}">
                <div>Low Stocks</div>
            </a>
        </li>
        <li class="menu-item">
            <a class="menu-link" href="{{ route('shopping_list.index') }}">
                <div>Shopping List</div>
            </a>
        </li>

    </ul>
</li>


<li class="menu-item {{ $route == 'stock.index' ? 'current' : '' }}"><a class="menu-link"
        href="{{ route('stock.index') }}">
        <div>Inventory</div>
    </a></li>

<li
    class="menu-item {{ $route == 'sales.index' ? 'current' : '' }}  {{ $route == 'sales.all.index' ? 'current' : '' }} {{ $route == 'credit.index' ? 'current' : '' }}">
    <a class="menu-link" href="#">
        <div>Sales</div>
    </a>
    <ul class="sub-menu-container">
        <li class="menu-item {{ $route == 'sales.index' ? 'current' : '' }}">
            <a class="menu-link" href="{{ route('sales.index') }}">
                <div>Sales</div>
            </a>
        </li>
        <li class="menu-item {{ $route == 'credit.index' ? 'current' : '' }}">
            <a class="menu-link" href="{{ route('credit.index') }}">
                <div>Credit Sales</div>
            </a>
        </li>
        <li class="menu-item {{ $route == 'sales.all.index' ? 'current' : '' }}">
            <a class="menu-link" href="{{ route('sales.all.index') }}">
                <div>All Sales</div>
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
            class="menu-item {{ $route == 'admin.salary_advance.index' ? 'current' : '' }}">
            <a class="menu-link" href="{{ route('admin.salary_advance.index') }}">
                <div>Salary Advance</div>
            </a>
        </li>
    </ul>
</li>

<li
    class="menu-item {{ $route == 'estimate.index' ? 'current' : '' }} {{ $route == 'estimate.all.index' ? 'current' : '' }}">
    <a class="menu-link" href="#">
        <div>Estimate</div>
    </a>
    <ul class="sub-menu-container">
        <li class="menu-item {{ $route == 'estimate.index' ? 'current' : '' }}">
            <a class="menu-link" href="{{ route('estimate.index') }}">
                <div>Estimate</div>
            </a>
        </li>
        <li class="menu-item {{ $route == 'estimate.all.index' ? 'current' : '' }}">
            <a class="menu-link" href="{{ route('estimate.all.index') }}">
                <div>All Estimate</div>
            </a>
        </li>

    </ul>
</li>


<li class="menu-item {{ $route == 'returns' ? 'current' : '' }} {{ $route == 'returns.all' ? 'current' : '' }}">
    <a class="menu-link" href="#">
        <div>Returns</div>
    </a>
    <ul class="sub-menu-container">
        <li class="menu-item {{ $route == 'returns' ? 'current' : '' }}"><a class="menu-link"
                href="{{ route('returns') }}">
                <div>Returns</div>
            </a></li>
        <li class="menu-item {{ $route == 'returns.all' ? 'current' : '' }}">
            <a class="menu-link" href="{{ route('returns.all') }}">
                <div>All Returns</div>
            </a>
        </li>
    </ul>
</li>
