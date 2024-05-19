@extends('layouts.app')
@section('PageTitle', 'Debtors')
@section('content')
    <section id="content">
        <div class="content-wrap">
            <div class="container">

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="col-4 "><span class="text-bold fs-16">Debtors ({{ auth()->user()->branch->name }})</span></div>
            
                    </div>
                    <div class="card-body">

                        <div class="table-responsive">
                           
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Total Owed</th>
                                        <th>Total Paid</th>
                                        <th>Last Sales Date</th>
                                        <th>Last Payment Date</th>
                                        <th>Days Since Last Payment</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($customers as $customer)
                                        <tr>
                                            <td>{{ ($customers->currentPage() - 1) * $customers->perPage() + $loop->iteration }}</td>

                                            <td>{{ $customer['first_name'] }}</td>
                                            <td>{{ $customer['total_owed'] }}</td>
                                            <td>{{ $customer['total_paid'] }}</td>
                                            <td>{{ $customer['last_sales_date']->format('Y-m-d') }}</td>
                                            <td>{{ $customer['last_payment_date'] ? $customer['last_payment_date']->format('Y-m-d') : 'N/A' }}</td>
                                            <td>{{ $customer['days_since_last_payment'] }}</td>
                                            <td>
                                                <a href="#" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#salesModal" data-customer-id="{{ $customer['customer_id'] }}">
                                                    <i class="fas fa-info-circle"></i>
                                                </a>
                                                <a href="#" class="btn btn-warning"><i class="fas fa-comment-alt"></i></a>
                                                <a href="#" class="btn btn-success" onclick="sendWhatsAppMessage('{{ $customer['phone'] }}', '{{ $customer['first_name'] }}', {{ $customer['total_owed'] }}, {{ $customer['total_paid'] }})">
                                                    <i class="fab fa-whatsapp"></i>
                                                </a>
                                            </td>
                                            
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-center">
                                {{ $customers->links() }}
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </section>

<!-- Sales Modal -->
<div class="modal fade" id="salesModal" tabindex="-1" role="dialog" aria-labelledby="salesModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="salesModalLabel">Customer Sales Transactions</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@section('js')


<script>
    $(document).ready(function() {
        $('#salesModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var customerId = button.data('customer-id');
            var modal = $(this);

            // Show loading spinner
            modal.find('.modal-body').html('<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>');

            // Make AJAX request
            $.ajax({
                url: '/debtors/customer-sales/' + customerId,
                type: 'GET',
                success: function(response) {
                    // Construct HTML for sales transactions
                    var html = '';
                    $.each(response, function(receiptNo, sales) {
                        var totalAmount = 0;
                        var amountPaid = '';

                        html += '<div><strong>Receipt No: ' + receiptNo + '</strong></div><ul>';

                        $.each(sales, function(index, sale) {
                            html += '<li>' + (index + 1) + '. ' + sale.product.name + ': ' + sale.quantity + ' x NGN ' + sale.price + ' - NGN ' + sale.discount + '</li>';
                            totalAmount += (sale.quantity * sale.price) - sale.discount;
                            if (index === 0 && sale.status === 'partial') {
                                amountPaid = 'Amount Paid: NGN ' + sale.payment_amount;
                            }
                        });

                        html += '<li><strong>Total Amount: NGN ' + totalAmount + '</strong></li>';
                        if (amountPaid !== '') {
                            html += '<li>' + amountPaid + '</li>';
                        }
                        html += '</ul>';
                    });

                    // Update modal body with sales transactions
                    modal.find('.modal-body').html(html);
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        });
    });
</script>





<script>
    function sendWhatsAppMessage(phone, firstName, totalOwed, totalPaid) {
        // Add country code if phone number is less than or equal to 11 digits
        if (phone.length <= 11) {
            phone = '234' + phone;
        }

        // Get business details
        let businessName = "EL-Habib Plumbing Materials and Services - {{ auth()->user()->branch->name }} Branch";
        let businessDetails = '';
        @if (auth()->user()->branch->name == 'Azare')
            businessDetails = "Address: Along Ali Kwara Hospital, Azare.\nPhone: 0916-844-3058\nEmail: support@elhabibplumbing.com\nWebsite: www.elhabibplumbing.com";
        @endif
        @if (auth()->user()->branch->name == 'Misau')
            businessDetails = "Address: Kofar Yamma, Misau, Bauchi State\nPhone: 0901-782-0678\nEmail: support@elhabibplumbing.com\nWebsite: www.elhabibplumbing.com";
        @endif

        // Account details
        let accountDetails = "Account number: 8033174228\nAccount name: Umar Katibu\nBank name: Opay";

        // Calculate balance
        let balance = totalOwed - totalPaid;

        // Compose message
        let message = `Hello ${firstName},\n\nYou have an outstanding credit balance of NGN ${balance.toFixed(2)} with ${businessName}.\n\n${businessDetails}\n\nPlease make a payment at your earliest convenience.\n\n${accountDetails}`;

        // Encode message for WhatsApp URL
        let encodedMessage = encodeURIComponent(message);

        // Construct WhatsApp URL
        let whatsappURL = `https://api.whatsapp.com/send?phone=${phone}&text=${encodedMessage}`;

        // Open WhatsApp in new tab with the composed message
        window.open(whatsappURL, '_blank');
    }
</script>



@endsection
