* Nueva Orden *

*Id:* {{ $order->id  }}
*Nombre:* {{ $order->customer->first_name }} {{ $order->customer->last_name  }}
*Telefono:* {{ $order->customer->mobile_phone }}
*Precio:* ${{ $order->total_price }}