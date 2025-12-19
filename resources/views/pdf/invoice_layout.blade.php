<table width="100%" cellspacing="0" cellpadding="10">
  <tr>
    <td width="50%" valign="top" style="border-right:1px solid #000; padding-right:10px;">
        <!-- Left invoice -->
        @include('pdf.invoice', ['orderList' => $orderList])
    </td>
    <td width="50%" valign="top" style="padding-left:10px;">
        <!-- Right invoice -->
        @include('pdf.invoice', ['orderList' => $orderList])
    </td>
  </tr>
</table>