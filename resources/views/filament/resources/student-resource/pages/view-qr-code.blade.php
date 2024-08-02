<x-filament::page>
    @php
        $qrData = route('matafuego.info', ['id' => $record->id]);
    @endphp

    <div style="text-align: center;">
        <h1>Información del Matafuego</h1>
        {{-- <img src="{{asset('/images/logo.jpeg')}}"  alt="logo" class="h-10 text-center" style="border-radius: 10px"> --}}

        {{-- <br> --}}
        <div style="display: flex; justify-content: center;">
            {!! QrCode::size(200)->generate($qrData) !!}
        </div>
        <p>Escanea el código QR para más información</p>
    </div>
</x-filament::page>
