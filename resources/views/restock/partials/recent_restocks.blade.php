@foreach($recentRestocks as $recentRestock)
<li class="list-group-item">
    {{ $recentRestock->restock_number }} - 
    {{ ucfirst($recentRestock->type) }} - 
    {{ $recentRestock->branchRestocks->first()->branch->name ?? 'N/A' }}
</li>
@endforeach
