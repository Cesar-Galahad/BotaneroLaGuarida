@extends('layouts.app')

@section('content')
<div class="p-6 max-w-4xl mx-auto">

    <h1 class="text-2xl font-bold text-gray-800 mb-6">Crear Pedido</h1>

    <div class="bg-white shadow rounded-xl p-6">

        <form>

            <div class="grid grid-cols-2 gap-6 mb-6">

                <div>
                    <label class="block text-sm font-semibold mb-2">Mesa</label>
                    <input type="number"
                           class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-500 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">Cliente</label>
                    <input type="text"
                           class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-500 outline-none">
                </div>

            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold mb-2">Observaciones</label>
                <textarea rows="3"
                          class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-500 outline-none"></textarea>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('pedidos.listado') }}"
                   class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-lg">
                    Cancelar
                </a>
                <button class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg shadow">
                    Guardar Pedido
                </button>
            </div>

        </form>

    </div>

</div>
@endsection