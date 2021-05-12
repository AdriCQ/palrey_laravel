* Nueva Orden *

*Id:* {{ $order->id  }}
*Nombre:* {{ $order->customer->name }}
*Telefono:* {{ $order->customer->mobile_phone }}
*Precio:* ${{ $order->total_price }}