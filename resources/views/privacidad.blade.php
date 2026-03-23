@extends('layouts.app')

@section('titulo', 'Aviso de Privacidad')

@section('content')

<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl shadow p-8">

        {{-- Encabezado --}}
        <div class="flex items-center gap-4 mb-8 pb-6" style="border-bottom: 2px solid #ea0000;">
            <img src="/imagenes/LogoGuarida.png" class="w-16 h-16 object-contain">
            <div>
                <h1 class="text-2xl font-bold" style="color: #1d1d1b;">Aviso de Privacidad</h1>
                <p class="text-sm" style="color: #6b7280;">La Guarida — Centro Botanero</p>
            </div>
        </div>

        {{-- Contenido --}}
        <div class="space-y-6 text-sm leading-relaxed" style="color: #374151;">

            <section>
                <h2 class="font-bold text-base mb-2" style="color: #1d1d1b;">1. Responsable del tratamiento de datos</h2>
                <p>
                    <strong>La Guarida, Centro Botanero</strong>, con domicilio en [Dirección], [Ciudad], [Estado], México,
                    es responsable del uso y protección de sus datos personales, de conformidad con la
                    Ley Federal de Protección de Datos Personales en Posesión de los Particulares (LFPDPPP).
                </p>
            </section>

            <section>
                <h2 class="font-bold text-base mb-2" style="color: #1d1d1b;">2. Datos personales que recabamos</h2>
                <p>Para llevar a cabo las finalidades descritas en el presente aviso, recabamos los siguientes datos personales:</p>
                <ul class="list-disc ml-6 mt-2 space-y-1">
                    <li>Nombre completo</li>
                    <li>Número de teléfono</li>
                    <li>Fotografía (opcional)</li>
                    <li>Historial de consumo y puntos acumulados en nuestro programa de lealtad</li>
                </ul>
            </section>

            <section>
                <h2 class="font-bold text-base mb-2" style="color: #1d1d1b;">3. Finalidades del tratamiento</h2>
                <p>Sus datos personales serán utilizados para las siguientes finalidades:</p>
                <ul class="list-disc ml-6 mt-2 space-y-1">
                    <li>Gestión de su cuenta en nuestro programa de lealtad</li>
                    <li>Acumulación y canje de puntos por consumo</li>
                    <li>Atención y seguimiento de pedidos</li>
                    <li>Envío de promociones y ofertas especiales (solo si usted lo autoriza)</li>
                    <li>Cumplimiento de obligaciones legales y fiscales</li>
                </ul>
            </section>

            <section>
                <h2 class="font-bold text-base mb-2" style="color: #1d1d1b;">4. Transferencia de datos</h2>
                <p>
                    Sus datos personales no serán transferidos a terceros sin su consentimiento, salvo en los casos
                    previstos por la legislación aplicable o cuando sea necesario para la prestación del servicio contratado.
                </p>
            </section>

            <section>
                <h2 class="font-bold text-base mb-2" style="color: #1d1d1b;">5. Derechos ARCO</h2>
                <p>
                    Usted tiene derecho a <strong>Acceder, Rectificar, Cancelar u Oponerse</strong> al tratamiento
                    de sus datos personales (derechos ARCO). Para ejercer estos derechos, puede contactarnos en:
                </p>
                <ul class="list-disc ml-6 mt-2 space-y-1">
                    <li>Correo electrónico: <span style="color: #ea0000;">[correo@laguarida.com]</span></li>
                    <li>Teléfono: <span style="color: #ea0000;">[número de contacto]</span></li>
                    <li>Domicilio: [Dirección completa]</li>
                </ul>
            </section>

            <section>
                <h2 class="font-bold text-base mb-2" style="color: #1d1d1b;">6. Seguridad de los datos</h2>
                <p>
                    Contamos con medidas de seguridad administrativas, técnicas y físicas para proteger sus datos
                    personales contra daño, pérdida, alteración, destrucción o uso no autorizado.
                </p>
            </section>

            <section>
                <h2 class="font-bold text-base mb-2" style="color: #1d1d1b;">7. Cambios al aviso de privacidad</h2>
                <p>
                    Nos reservamos el derecho de efectuar modificaciones o actualizaciones al presente aviso.
                    Cualquier cambio será notificado a través de nuestros canales oficiales o en este mismo apartado.
                </p>
            </section>

            <section>
                <h2 class="font-bold text-base mb-2" style="color: #1d1d1b;">8. Consentimiento</h2>
                <p>
                    Al proporcionar sus datos personales, usted otorga su consentimiento para el tratamiento
                    de los mismos conforme a lo establecido en este aviso de privacidad.
                </p>
            </section>

        </div>

        {{-- Pie --}}
        <div class="mt-8 pt-6 text-xs text-center" style="border-top: 1px solid #e5e7eb; color: #9ca3af;">
            Última actualización: {{ date('d/m/Y') }} — La Guarida, Centro Botanero
        </div>

    </div>
</div>

@endsection