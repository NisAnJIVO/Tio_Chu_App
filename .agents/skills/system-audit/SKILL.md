---
name: system-audit
description: Guía y estándar de auditoría técnica, consistencia de lógica financiera y estándares UX/UI para Tío Chu.
---

# Auditoría Técnica y Estándar de Calidad — Tío Chu

Esta skill establece los principios de arquitectura, integridad transaccional, cálculos de caja y estándares UX/UI requeridos para cualquier cambio o revisión en el sistema Tío Chu.

---

## 1. Integridad Transaccional y Lógica Financiera

### 1.1 Deducción de Tarjeta POS (Bruto vs. Neto)
- **Principio**: La comisión del 4.5% de la pasarela/banco es un gasto operativo del negocio, **nunca** un faltante que el cajero deba reponer en efectivo.
- **Regla**: En todos los cierres y arqueos de barra (`BarSaleController`, `CashClosingController`), el cálculo del saldo en efectivo restante debe deducir el importe **Bruto** de la tarjeta (`card_amount` o `amount` de la factura tipo POS):
  ```php
  $barCashRemaining = max(0, $grandTotalBar - $barTotalQr - $barTotalCard);
  ```
- **Prohibido**: Deducir el importe neto (`net_amount`) al conciliar el efectivo físico con las ventas reportadas.

### 1.2 Transaccionalidad Atómica (`DB::transaction`)
- Cualquier operación que actualice múltiples registros o revierta inventarios **debe** ejecutarse dentro de un bloque `DB::transaction()`:
  - Carga masiva de inventario de barras (`BarInventoryController@updateBulk`).
  - Actualización masiva de ventas de barra (`BarSaleController@updateBulk`).
  - Anulación/eliminación de ventas de tienda (`StoreSaleController@destroy`): reponer stock de almacén y eliminar en cascada registros asociados (`QrPayment`, `Invoice`).

### 1.3 Blindaje contra Edición en Noches Cerradas
- Una noche cerrada (`$session->isClosed()` o `closed_at !== null`) no debe permitir modificaciones silenciosas en sus arqueos ni en sus turnos históricos.
- Métodos como `PaymentHistoryController@paySingle` o guardados manuales deben verificar `$session->isOpen()`. Si está cerrada, responder con error `422 Unprocessable Entity` o redirección informando que la noche ya fue cerrada contablemente.

### 1.4 Centralización de Métricas (Evitar lógica en Blade)
- Las vistas Blade jamás deben contener consultas directas `DB::table()`, closures complejas de cálculo o declaraciones de funciones inline.
- Toda agregación y cálculo debe residir en servicios especializados (como `NightSessionService::getDashboardMetrics()`) o métodos estáticos de modelos.

---

## 2. Estándar UX/UI (iOS Minimalista Tío Chu)

### 2.1 Paleta de Color Oficial
- **Fondo Primario:** `#000000` (Negro absoluto). Cero tonos plomos o azules deslavados (`#14151b`, `#18181b`).
- **Superficie de Paneles / Tarjetas:** `#09090b` con bordes sutiles `border border-zinc-800/80`.
- **Acento:** Ámbar oficial Tío Chu `#F5B81C` (o Tailwind `amber-500` / `#eab308` según contexto). Reservado para estados activos, llamadas a la acción e indicadores principales.
- **Tipografía:** *Plus Jakarta Sans* como fuente unificada en toda la aplicación.

### 2.2 Cabeceras de Vistas Estandarizadas
Todas las vistas principales deben utilizar el contenedor de cabecera estándar:
```html
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-3.5 rounded-2xl bg-[#09090b] border border-zinc-800/80 shadow-sm">
    <div>
        <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-white font-sans">
            Título de la Vista
        </h1>
        <p class="text-xs text-zinc-400 mt-0.5">
            Descripción breve de la función
        </p>
    </div>
    <!-- Selectores / Acciones en pastillas discretas -->
    <div class="flex items-center gap-2">
        ...
    </div>
</div>
```

### 2.3 Sistema de Notificaciones Efímeras (Toasts)
- **Cero banners estáticos invasivos:** No renderizar alertas tipo alert-danger o alert-success que empujen el contenido de la pantalla.
- **Formato:** Notificación flotante inferior centrada:
  - Clase: `fixed bottom-6 left-1/2 -translate-x-1/2 z-50 rounded-full bg-black/95 border border-zinc-800 shadow-2xl`
  - Duración: 1.8 segundos para confirmaciones de éxito, 2.2 segundos para errores/advertencias.
  - Desvanecimiento: Transición suave de opacidad y desplazamiento sutil (`opacity 0.25s ease, transform 0.25s ease`).

### 2.4 Copywriting
- Siempre referirse a la marca de forma sobria: **Tío Chu**.
- Evitar adjetivos hiperbólicos ("Panel Ultra Avanzado", "Solución Ejecutiva").
- Mensajes directos, funcionales y concisos.

---

## 3. Checklist de Verificación en Cada Entrega

- [ ] ¿Los cálculos de efectivo deducen tarjeta bruta y QR sin duplicar ni subestimar?
- [ ] ¿Las anulaciones de ventas eliminan o desvinculan pagos QR y facturas generadas?
- [ ] ¿Las operaciones en lote de inventario y ventas están en `DB::transaction()`?
- [ ] ¿Las rutas de mutación validan el estado abierto/cerrado de la sesión nocturna?
- [ ] ¿Las vistas Blade carecen de llamadas `DB::table()` o lógica pesada de cálculo?
- [ ] ¿El fondo general es negro `#000000` y las tarjetas `#09090b` sin tonos azulados?
- [ ] ¿Los mensajes de éxito/error usan toasts flotantes inferiores efímeros?
- [ ] ¿Todos los tests automatizados (`phpunit`) pasan con 0 errores?
