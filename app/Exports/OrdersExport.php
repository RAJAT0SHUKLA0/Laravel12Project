<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrdersExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Order::with(['seller','staff'])
            ->select('id','order_id','seller_id','staff_id','date','total_price','status','cancel_date','pickup_date','delivery_date','discount')
            ->get()
            ->map(function ($order) {
                return [
                    'S.No'        => $order->id,
                    'Order ID'    => $order->order_id,
                    'Seller'      => $order->seller->name ?? '',
                    'Shop Name'   => $order->seller->shop_name ?? '',
                    'Staff'       => $order->staff->name ?? '',
                    'Order Date'  => \Carbon\Carbon::parse($order->date)->format('d-m-Y'),
                   'Deliver Date' => ($order->delivery_date && $order->delivery_date !== '0000-00-00')
                        ? \Carbon\Carbon::parse($order->delivery_date)->format('d-m-Y')
                        : 'N/A',
                    'Pickup Date'  => ($order->pickup_date && $order->pickup_date !== '0000-00-00')
                        ? \Carbon\Carbon::parse($order->pickup_date)->format('d-m-Y')
                        : 'N/A',
                   'Cancel Date'  => ($order->cancel_date  && $order->cancel_date !== '0000-00-00')
                        ? \Carbon\Carbon::parse($order->cancel_date)->format('d-m-Y') 
                        : 'N/A',
                    'Amount'      => $order->total_price,
                    'Discount'      => $order->discount,
                    'Status'      => $this->statusLabel($order->status),
                ];
            });
    }

    public function headings(): array
    {
        return ['S.No', 'Order ID', 'Seller', 'Shop Name', 'Staff', 'Order Date', 'Deliver Date','Pickup Date','Cancel Date','Amount','Discount', 'Status'];
    }

    private function statusLabel($status)
    {
        $labels = [
            0 => 'Pending',
            1 => 'To Delivered',
            2 => 'Pickup',
            3 => 'Delivered',
            4 => 'Cancel',
            5 => 'Return',
            6 => 'Assign',
        ];
        return $labels[$status] ?? 'Pending';
    }
}
