@if($route == 'sales.index')
<li class="menu-item">
    <a class="menu-link" href="#">
        <div>Menu</div>
    </a>
    <ul class="sub-menu-container">
        <li class="menu-item"><a class="menu-link" href="{{ route('cashier.home') }}">
            <div>Home</div>
        </a></li>
        <li
            class="menu-item">
            <a class="menu-link" href="{{ route('credit.index') }}">
                <div>Credit Sales</div>
            </a>
        </li>
        <li
            class="menu-item">
            <a class="menu-link" href="{{ route('sales.all.index') }}">
                <div>All Sales</div>
            </a>
        </li>

        <li class="menu-item"><a
            class="menu-link" href="{{ route('expense.index') }}">
            <div>Expense</div>
        </a></li>

        <li class="menu-item"><a class="menu-link"
            href="{{ route('returns') }}">
            <div>Returns</div>
        </a></li>

        <li class="menu-item"><a
            class="menu-link" href="{{ route('customers.index') }}">
            <div>Customers</div>
        </a></li>
    </ul>
</li>
@else

<li class="menu-item {{ $route == 'cashier.home' ? 'current' : '' }} "><a class="menu-link" href="{{ route('cashier.home') }}">
        <div>Home</div>
    </a></li>
<li class="menu-item {{ $route == 'sales.index' ? 'current' : '' }}  {{ $route == 'sales.all.index' ? 'current' : '' }} {{ $route == 'credit.index' ? 'current' : '' }}">
    <a class="menu-link" href="#">
        <div>Sales</div>
    </a>
    <ul class="sub-menu-container">
        <li
            class="menu-item {{ $route == 'sales.index' ? 'current' : '' }}">
            <a class="menu-link" href="{{ route('sales.index') }}">
                <div>Record Sales</div>
            </a>
        </li>
       
        <li
            class="menu-item {{ $route == 'sales.all.index' ? 'current' : '' }}">
            <a class="menu-link" href="{{ route('sales.all.index') }}">
                <div>View Sales</div>
            </a>
        </li>

    </ul>
</li>
<li class="menu-item {{ $route == 'expense.index' ? 'current' : '' }} "><a
    class="menu-link" href="{{ route('expense.index') }}">
    <div>Expense</div>
</a></li>

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

<li class="menu-item {{ $route == 'estimate.index' ? 'current' : '' }} {{ $route == 'estimate.all.index' ? 'current' : '' }}">
    <a class="menu-link" href="#">
        <div>Estimate</div>
    </a>
    <ul class="sub-menu-container">
        <li
            class="menu-item {{ $route == 'estimate.index' ? 'current' : '' }}">
            <a class="menu-link" href="{{ route('estimate.index') }}">
                <div>Estimate</div>
            </a>
        </li>
        <li
            class="menu-item {{ $route == 'estimate.all.index' ? 'current' : '' }}">
            <a class="menu-link" href="{{ route('estimate.all.index') }}">
                <div>All Estimates</div>
            </a>
        </li>
    </ul>
</li>

<li class="menu-item  {{ $route == 'customers.index' ? 'current' : '' }} {{ $route == 'customers.profile' ? 'current' : '' }}">
    <a class="menu-link" href="#">
        <div>Customers</div>
    </a>
    <ul class="sub-menu-container">
        <li class="menu-item {{ $route == 'customers.index' ? 'current' : '' }} {{ $route == 'customers.profile' ? 'current' : '' }}"><a
            class="menu-link" href="{{ route('customers.index') }}">
            <div>Customers</div>
        </a></li>
        <li
            class="menu-item {{ $route == 'cashier.salary_advance.index' ? 'current' : '' }}">
            <a class="menu-link" href="{{ route('cashier.salary_advance.index') }}">
                <div>Salary Advance</div>
            </a>
        </li>

        <li
        class="menu-item {{ $route == 'cash_credits.index' ? 'current' : '' }}">
        <a class="menu-link" href="{{ route('cash_credits.index') }}">
            <div>Cash Credits</div>
        </a>
    </li>
       

    </ul>
</li>

@endif