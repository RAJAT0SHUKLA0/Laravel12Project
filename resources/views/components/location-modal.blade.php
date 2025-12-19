<style>
    .modal-backdrop {
    background-color: transparent !important;
}
</style>
<div id="modalPaginationWrapper"
     data-url="{{ route('renderLocationList') }}"
     data-token="{{ csrf_token() }}"
     data-user-id="{{ request('user_id') }}">
<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-bs-backdrop="static" >
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="myLargeModalLabel">Location List</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="refreshData()"></button>
         </div>
         <div class="modal-body">
            <div class="table-responsive table-card mt-3 mb-1">
               <table class="table align-middle table-nowrap" id="customerTable">
                  <thead class="table-light">
                     <tr>
                         <th scope="col" style="width: 5%;">S No</th>
                         <th style="width: 20%;">Date&Time</th>
                         <th style="width: 20%;">Latitude</th>
                         <th style="width: 20%;">Longitude</th>
                         <th style="width: 10%;">Address</th>

                     </tr>
                  </thead>
                  <tbody class="list form-check-all">
                     @if(sizeof($locationlist))
                     @php
                     $i=1;
                     @endphp
                     @foreach($locationlist as $AttendanceList)
                     <tr>
                        <td class="id" >{{ $locationlist->firstItem() + $loop->index }}</td>
                        <td class="customer_name">{{isset($AttendanceList->date)?$AttendanceList->date:''}}<br>{{ $AttendanceList->created_at ? \Carbon\Carbon::parse($AttendanceList->created_at)->format('h:i A') : '' }}</td>
                        <td class="customer_name">{{isset($AttendanceList->latitude)?$AttendanceList->latitude:''}}</td>
                        <td class="customer_name">{{isset($AttendanceList->longitude)?$AttendanceList->longitude:''}}</td>
             @php
    $address = isset($AttendanceList->address) ? $AttendanceList->address : '';
    $wrappedAddress = wordwrap($address, 50, "\n", true);
    $lines = explode("\n", $wrappedAddress);
    $lines = array_slice($lines, 0, 6);
    while (count($lines) < 6) {
        $lines[] = '';
    }
@endphp

<td class="customer_name" style="font-size: 12px;">
    {!! implode('<br>', $lines) !!}
</td>


                     </tr>
                     @endforeach
                     @endif
                  </tbody>
               </table>
            </div>
        
{{ $locationlist->appends(['user_id' => request('user_id')])->links('pagination::bootstrap-5') }}

         </div>
      </div>
   </div>
</div>
</div>