# Sistema de Diseño y Reglas UX/UI — Tío Chu

Este documento define la identidad visual, estándares de experiencia de usuario (UX) y reglas de interfaz (UI) para el sistema de **Tío Chu**. Debe seguirse de manera estricta en todas las vistas y componentes.

---

## 1. Filosofía de Diseño: Minimalismo iOS & Dark Puro

- **Estética iOS Premium:** Limpio, directo, enfocado en el contenido. Bordes suaves, radios de esquina modernos (`rounded-xl` / `rounded-2xl`), tipografía legible y jerarquía visual impecable.
- **Negro Puro (`#000000`):** Fondo negro total. Cero lavados grisáceos ("plomo"), sin gradientes sucios ni fondos que contrasten de manera indeseada con los recursos gráficos del establecimiento.
- **Cero Relleno de "IA Generativa":**
  - **PROHIBIDO** agregar títulos artificiales o pretenciosos como *"Acceso Ejecutivo"*, *"Sistema de Alta Gama"*, *"Discoteca Tío Chu Control de Inventario & Arqueo de Caja"*, etc.
  - La marca habla por sí sola: simplemente **Tío Chu** o el logo oficial.
  - Mantener los formularios limpios: solo los campos necesarios (Correo, Contraseña, etc.) sin tarjetas gigantes artificiales.

---

## 2. Paleta de Colores Oficial

| Elemento | Color Hex | Uso |
| :--- | :--- | :--- |
| **Fondo Principal** | `#000000` (Pure Black) | Fondo de pantalla, body, contenedores base. |
| **Acento Primario (Dorado)** | `#F5B81C` | Botones de acción principal (CTA), indicadores activos, bordes de foco. Es el dorado ámbar del logo de cerveza, **NO es naranja chillón**. |
| **Dorado Hover/Active** | `#E5AC18` / `#D69D0F` | Estados de hover y active en botones. |
| **Bordes de Contenedores** | `#27272A` (`border-zinc-800` / `border-white/10`) | Líneas ultrafinas, elegantes y discretas. |
| **Fondo de Campos (Inputs)** | `#000000` o `#0A0A0A` | Negro puro o negro grafito sutil con borde fino. |
| **Campos Autocompletados (Autofill)** | `#09090b` (Zinc 950 grafito) | Fondo forzado vía box-shadow inset y texto blanco para neutralizar el fondo blanco por defecto del navegador. |
| **Texto Principal** | `#FFFFFF` (`text-white`) | Títulos, etiquetas clave y valores principales. |
| **Texto Secundario** | `#A1A1AA` (`text-zinc-400`) / `#71717A` (`text-zinc-500`) | Placeholders, descripciones breves, metadatos. |
| **Texto en Botones Dorados** | `#000000` (`text-black font-semibold`) | Contraste óptimo y legibilidad perfecta. |

---

## 3. Notificaciones y Feedback al Usuario (UX)

- **Cero bloques invasivos:** No colocar banners gigantes de color verde o rojo estáticos que empujen los formularios o rompan el diseño.
- **Pantalla de Login Limpia:** No se muestran banners ni toasts de confirmación para mantener la interfaz 100% minimalista y centrada en los campos de acceso.
- **Errores de validación:** Mensajes tipográficos concisos centrados discretamente (`text-xs text-rose-400`), sin alertas escandalosas.

---

## 4. Estructura y Distribución (Layout)

- **Login Split 50 / 50:**
  - Mitad izquierda (o principal): Formulario iOS centrado, con el logo oficial [LogoTioChu.png](file:///c:/Users/Usuario/Desktop/Tio%20Chu/public/images/LogoTioChu.png) prominente arriba y campos directos.
  - Mitad derecha: Imagen institucional [fondoTioChuLogo.png](file:///c:/Users/Usuario/Desktop/Tio%20Chu/public/images/fondoTioChuLogo.png) ocupando el 100% de la altura (`object-cover`) con desvanecimiento sutil al negro en el borde divisorio.
- **Adaptabilidad Móvil:** En pantallas pequeñas (`< lg`), se oculta la imagen decorativa y se centra el formulario en pantalla completa garantizando rapidez y accesibilidad táctil.

---

## 5. Animaciones y Transiciones (GSAP)

- **Tecnología:** Utilizar **GSAP** (GreenSock) para garantizar 60-120 FPS sin caídas de rendimiento ni lag computacional.
- **Secuencia de Entrada en Login (Apertura de Puerta / Cinema Reveal):**
  1. **Preloader / Splash:** Pantalla negra pura con el logo central de Tío Chu que carga y sutilmente respira o pulsa.
  2. **Transición Split:** El logo central se difumina con un zoom sutil mientras los dos paneles se abren / deslizan como puertas:
     - El formulario entra suavemente desde la izquierda (`x: -50px -> 0`, `opacity: 0 -> 1`).
     - La imagen de fondo entra desde la derecha (`x: 50px -> 0`, `opacity: 0 -> 1`).
  3. **Stagger Interno:** Los campos del formulario (correo, contraseña, botón) aparecen con un ligero retraso escalonado (stagger) para un efecto ultra fluido.
- **Secuencia de Salida (Submit de Login):**
  1. Al presionar *Iniciar sesión*, las dos puertas se repliegan hacia los extremos.
  2. En el centro aparece el logo oficial de Tío Chu con la barrita dorada (`#F5B81C`), la cual se llena con agilidad en 0.45s.
  3. **Trazado en Letra Carta Estilo iPhone "Hello":** La barrita se disuelve y se convierte directamente en una línea fluida que dibuja caligráficamente la palabra **"Bienvenido"** en letra carta (`Sacramento` / `Dancing Script`) de izquierda a derecha (`stroke-dashoffset` sincronizado con máscara progresiva y una chispa guía luminosa). Al finalizar el trazo, la caligrafía florece en amarillo dorado ámbar resplandeciente y tras una pausa ágil de lectura (0.25s), da paso inmediato al Dashboard.

---

## 6. Identidad de Marca en Navegador (Favicon & Head)

- **Logo en Pestaña del Navegador:**
  - En todas las plantillas maestras (`layouts/app.blade.php` y `auth/login.blade.php`), debe enlazarse obligatoriamente el isotipo oficial `images/LogoTioChu.png` como favicon mediante:
    - `<link rel="icon" type="image/png" href="{{ asset('images/LogoTioChu.png') }}">`
    - `<link rel="shortcut icon" href="{{ asset('favicon.ico') }}">`
    - `<link rel="apple-touch-icon" href="{{ asset('images/LogoTioChu.png') }}">`
  - El archivo `public/favicon.ico` debe mantenerse sincronizado con el logo oficial para responder a peticiones directas del navegador, garantizando que el logo de Tío Chu se visualice en la barra de pestañas, marcadores y accesos directos móviles en lugar del ícono predeterminado del framework.

---

## 7. Arquitectura del Sidebar — Compacto, Cero Sombras, Colapsable & Sin Scroll

- **Filosofía Visual (Dark Minimal & Ultra Compacto):**
  - **Cero Sombras Duras:** Eliminación de sombras desplazadas para un aspecto pulido, moderno y limpio sin distracciones visuales.
  - **Comportamiento de Altura & Scroll Adaptativo:** En pantallas normales cabe completo sin necesidad de scroll; ante zoom del navegador (`Ctrl +`) o pantallas de menor altura, el área de navegación se desplaza de forma fluida (`overflow-y-auto min-h-0`) con una barra ultra delgada y oscura que no invade el diseño.
  - **Fichas Más Bajas (Compactas):** Padding vertical reducido (`py-1.5 px-2.5`) para maximizar el espacio útil y permitir que todas las rutas se muestren simultáneamente.
  - **Tipografía Más Grande y Legible:** Texto aumentado a `text-[13px]` con peso `font-semibold` en inactivo y `font-extrabold` en activo.
  - **Cabecera Limpia (Sin Relleno):** Eliminado subtítulo genérico "Club & Bar", conservando únicamente isotipo y marca "TÍO CHU".
- **Modo Colapsable (Menú Hamburguesa & Solo Logos):**
  - **Botón Hamburguesa Interactivo:** Ubicado en la cabecera superior; al hacer hover se ilumina en amarillo oficial (`text-[#F5B81C] bg-[#F5B81C]/10`).
  - **Transición a Modo Riel de Íconos (Solo Logos):** Al pulsar el botón, el sidebar se contrae suavemente a `w-[68px]`, ocultando los textos y centrando exclusivamente los íconos de cada ruta con su respectivo tooltip nativo (`title`).
  - **Persistencia en Navegación:** El estado colapsado/expandido se guarda en `localStorage` (`tiochu_sidebar_collapsed`) y se aplica en `<head>` para evitar parpadeos (FOUC) al recargar o cambiar de página.
- **Navegación & Claridad Iconográfica:**
  - **Íconos Semánticos Intuitivos:** Cada opción cuenta con un ícono SVG claro y alusivo a su función real (ej. copa de bar para Inventario de Barras, caja registradora / gráfica para Ventas, lector de tarjeta para POS, código QR nítido para Cobros QR, caja fuerte para Cierre de Caja, luna de turno para Historial de Noches).
  - **Estado Inactivo:** Tarjetas limpias con borde transparente o sutil, texto blanco/zinc legible y efecto hover suave (`bg-zinc-900/80`).
  - **Estado Activo:** Ficha sólida en dorado Tío Chu (`#F5B81C`) con texto negro e indicador circular para jerarquía inmediata.
- **Ficha de Usuario & Salida:** Perfil compacto de 32px cerrado y botón de desconexión sutil sin sobrecarga.

---

## 8. Arquitectura del Dashboard — Minimalismo Sobrio iOS Pure Dark

- **Paleta Sobria & Cero Arcoíris:**
  - Erradicados colores chillones o mezclas saturadas (azules eléctricos, verdes estridentes, bordes multicolor).
  - La interfaz se rige por: fondo negro `#000000`, tarjetas sólidas `#09090b` con bordes `#27272a` (`border-zinc-800/80`), números y valores monetarios en blanco puro nítido monospace con prefijo dorado ámbar oficial `#F5B81C`, y textos secundarios en zinc tenue.
  - Íconos unificados en contenedores oscuros de bajo contraste (`bg-zinc-950 border border-zinc-800 text-zinc-300`).
- **Política Estricta de Integridad:** 
  - **Cero Datos Falsos o Simulados:** Prohibido el uso de mapas de calor artificiales o métricas inventadas. Todo número, barra y estado proviene 100% de la base de datos real.
- **Lenguaje Claro y Natural para Personas Mayores (Don Ludo):**
  - **Vocabulario Cotidiano y Directo:**
    - `Venta en Barras` (Barra Principal y Subte).
    - `Tarjetas (Neto Banco)` (con detalle de importe bruto).
    - `Cobros por QR` (Yasta y Yape).
    - `Efectivo en Caja` (Plata a favor / Faltante).
    - `Ventas por Barra` (Barra Kelly, Barra Ariel, Tienda).
    - `Cuentas de la Noche` (Ingresos de Noche vs Egresos y Pagos).
- **Tarjetas 100% Estáticas:**
  - Sin saltos ni traslaciones molestas al pasar el mouse. Los botones de acción responden de forma táctil con micro-animaciones sobrias (`active:scale-95`).

### 9. Rediseño Maestro de Bodega Central (Almacén de Bebidas)
- **Vista Híbrida Inteligente (Tabla Ejecutiva vs Tarjetas):**
  - **Tabla Ejecutiva (Predeterminada):** Diseñada para auditorías y conteo rápido de 30+ marcas sin saturación visual. Filas de alto contraste, desglose simultáneo de Cajas y Unidades Sueltas, botones de ajuste rápido (`-10`, `-1`, `+1`, `+10`) e input central con guardado automático sin recargar la página.
  - **Cuadrícula de Tarjetas Minimalistas:** Accesible con un clic en el selector superior para vista táctil o visualización en tablets.
  - La preferencia de vista se almacena en `localStorage` (`tiochu_bodega_view`).
- **Lenguaje Cotidiano y Directo:**
  - `Total en Bodega` *(botellas contadas)*
  - `Plata en Mercadería` *(según precio venta en barras)*
  - `Variedad de Bebidas` *(marcas activas en carta)*
  - `Bebidas por Agotarse` *(alertas con 10 o menos botellas)*
- **Drawers Laterales (Crear y Editar Bebida):**
  - Paneles deslizantes oscuros (`#drawer-create-product` y `#drawer-edit-product`) sin recargar la pantalla, con validación completa y botón seguro de eliminación.

### 10. Rediseño Maestro de Inventario de Barras
- **Cero Tropas Generativas de IA:**
  - **Prohibidos Emojis:** Cero emojis tipo 🔒, copas o cohetes. La interfaz usa exclusivamente tipografía limpia y SVGs discretos de línea fina.
  - **Cero Bordes Arcoíris ni Píldoras de Colores:** Los inputs son 100% homogéneos y neutros (`bg-zinc-950 border border-zinc-800`), eliminando marcos verdes o amarillos dentro de las celdas para evitar fatiga visual.
  - **Cero Banners Alarmistas:** Los estados de sesión cerrada se indican con insignias discretas y sobrias en la barra superior.
- **Eliminación del "Toast" Flotante:**
  - Prohibidas las barras pegadas (`sticky bottom`) que flotan sobre las filas y tapan la información.
  - El botón principal **"Guardar Cambios"** se sitúa en la cabecera superior (siempre accesible) y se replica en un pie de tabla estático al final de la lista.
- **Fondo Neutro Puro & Cero Distracciones:**
  - Lienzo ultra oscuro neutral (`#08080a` con tarjetas `#0f1013` y bordes `#22232a`).
  - Eliminados los orbes de luz desenfocada (luces azules y ámbar flotantes) que generaban gradientes sucios y pérdida de enfoque.
- **Estructura Humana de 5 Columnas (Pensada para Don Ludo):**
  1. **Bebida:** Nombre nítido en blanco, presentación y factor de botellas por caja.
  2. **1. Saldo de Ayer:** Cajas y botellas sueltas con cálculo total inmediato.
  3. **2. Subido al Abrir:** Cajas y botellas sueltas agregadas en apertura.
  4. **3. Reposición en Noche:** Controles táctiles discretos (`+` y `−`) sin estridencias para sumar botellas pedidas durante el turno.
  5. **Total Disponible:** Número grande, destacado y dorado (`#F5B81C`) como único foco visual clave para saber el stock listo para la venta.
- **Control Segmentado iOS:**
  - Barra Kelly y Barra Ariel como pestañas compactas y legibles con ubicación entre paréntesis.
  - Filtro por categoría (Todos, Licores, Mixers) y buscador en tiempo real.

---

### 11. Rediseño Maestro de Ventas por Barra & Combos
- **Cero Relleno ni Píldoras de IA:**
  - Prohibidos subtítulos artificiales como *"Módulo 6 — Liquidación de Barras"*, tags de píldoras multicolor o emojis (🔒, etc.).
  - Las sesiones cerradas se indican discretamente con tipografía limpia e ícono SVG fino.
- **Acceso Directo a Guardado (Sin Overlays Flotantes):**
  - El botón principal dorado **"Guardar Ventas"** se sitúa en la cabecera superior junto al selector de noche y punto de venta.
  - El pie de formulario incluye un resumen de liquidación y botón secundario de guardado integrado en el flujo natural de la página.
- **Selector Segmentado de Puntos de Venta:**
  - Pestañas nítidas estilo iOS: **Barra Kelly (Piso Principal)**, **Barra Ariel (Subterráneo)** y **Tienda (Directo)**.
- **Métricas Claras y Humanas para Don Ludo:**
  - `Total Bebidas` (Combos + Extras) destacado en tipografía dorada `#F5B81C`.
  - `Cobrado por QR` (desglosado entre YASTA y YAPE).
  - `Tarjetas (POS)` (con detalle de comisión y neto).
  - `Efectivo en Barra` (dinero físico en mano que debe entregar el bartender).
- **Tablas de Alto Contraste & Fondo Neutro:**
  - Eliminados fondos lechosos (*liquid glass* difuso) reemplazados por tarjetas sólidas `#0f1013` con bordes `#22232a`.
  - Entradas de texto homogéneas en negro puro grafito (`bg-zinc-950 border border-zinc-800 focus:border-[#F5B81C]`).
  - Columnas de resultado (**Combos Vendidos** y **Extras**) destacadas como puntos focales en dorado.
- **Tienda con Despacho Inmediato:**
  - Formulario ágil con botones rápidos de asignación (Todo Efectivo, Todo QR, 50% y 50%) y registro histórico instantáneo.

---

### 12. Rediseño Maestro de Facturas & Tarjetas (Tarjeteos)
- **Lenguaje Humano y Cotidiano (Cero Siglas Técnicas 'POS'):**
  - Eliminado el acrónimo técnico abstracto *"POS"* de títulos, etiquetas y del menú del sidebar.
  - El módulo se denomina formalmente **Facturas & Tarjetas** (o *Tarjeteos*).
  - Métricas cotidianas para Don Ludo:
    - `Pasado por Tarjeta` *(Total según vouchers pasados por la máquina)*
    - `Comisión Banco` *(Retención que descuenta el banco por operar con tarjeta)*
    - `Plata que Entra al Banco` *(Total neto real que ingresa a la cuenta bancaria, destacado en dorado `#F5B81C`)*
    - `Facturas en Efectivo` *(Dinero físico cobrado en mano)*
- **Cero Relleno ni Tropas de IA:**
  - Eliminados subtítulos artificiales como *"Módulo 1 — Tarjetero & POS"*, emojis (🔒) y banners alarmistas.
- **Distribución Funcional:**
  - **Panel de Registro Rápido:** Formulario con número de factura correlativo automático, selector directo de barra y monto en dorado.
  - **Tabla de Auditoría:** Historial completo con cálculo de comisión y neto en tiempo real, sin elementos flotantes ni sobrecarga visual.

---

### 13. Rediseño Maestro de Cobros por QR (Yasta & Yape)
- **Lenguaje Claro y Conciso:**
  - Título directo: **Cobros por QR** con subtítulo natural: *Transferencias bancarias recibidas por Yasta (Banco Unión) y Yape (Banco BCP)*.
  - Cero etiquetas de IA como *"Módulo 4 — Pagos Digitales QR"* o emojis (🔒).
- **Métricas por Punto de Venta:**
  - `Total Cobrado por QR` (destacado en dorado `#F5B81C` con desglose Yasta y Yape).
  - Subtotales claros e independientes para **Barra Kelly (Principal)**, **Tienda (Entrada)** y **Barra Ariel (Subte)**.
- **Formulario Ágil de Registro:**
  - Selector de Punto de Venta, Personal clasificado por rol (*Bartenders*, *Meseros*, *Refuerzos*), App bancaria y Monto.
- **Cuadrícula de Auditoría de 3 Columnas:**
  - Columnas paralelas por punto de venta con tarjetas sólidas `#0f1013`, bordes `#22232a` y visualización inmediata de transacciones sin sobrecarga visual.

---

### 14. Rediseño Maestro de Cierre de Caja & Control de Gastos
- **Lenguaje Transparente y Sin Jerga Contable Artificial:**
  - Prohibidos términos pretenciosos como *"Superávit"*, *"Déficit"*, *"Ficha Contable Oficial"* o *"Módulo 5 — Resumen de Cierre"*.
  - Enfoque directo y comprensible: **Cuentas Claras: Entradas y Salidas**.
  - Destacado principal: **Plata que debe quedar en caja** (monto neto en dorado `#F5B81C` o esmeralda a favor, rojo en caso de saldo negativo).
  - Dos bloques simétricos:
    - `1. Todo el Dinero que Entró` *(Tarjetas Netas al Banco, QR Yasta, QR Yape, Efectivo en Mano de Barras)*.
    - `2. Todo lo que se Pagó en la Noche` *(Pago a Trabajadores y Gastos Operativos del Turno)*.
- **Cero Tropas de IA ni Emojis:**
  - Cero emojis (🔒) ni banners alarmistas. Botón de cierre definitivo sobrio y elegante (`bg-rose-500/15 border border-rose-500/40 text-rose-300`).
- **Distribución en 2 Columnas Inferiores:**
  - **Pagos al Personal:** Planilla liquidada con indicador de pagado/pendiente y enlace directo a gestión de personal.
  - **Gastos de la Noche:** Formulario compacto de compras e imprevistos (hielo, insumos de urgencia) con tabla de auditoría inmediata.

---

### 15. Rediseño Maestro de Personal, Turnos y Planilla Nocturna
- **Exposición Real y Fiel de la Base de Datos:**
  - La columna `phone` de la base de datos se expone visiblemente en la tabla como `Contacto / Celular`. Si el trabajador tiene número registrado, se muestra con icono limpio en `font-mono`; si no posee número, se indica un tenue `— Sin número —`.
  - Nombre del trabajador destacado en blanco puro de alto contraste (`font-bold text-white text-sm`) acompañado de mini-badges sutiles indicando los días de asistencia habitual (`Vie`, `Sáb`, `Dom`).
- **Eliminación Total de Avisos Repetitivos de IA:**
  - Prohibidos los banners y toasts pegajosos tipo *"Personal actualizado correctamente."* que permanecen indefinidamente en pantalla.
  - El layout principal incluye auto-dismiss automático de 3 segundos con desvanecimiento suave para cualquier mensaje flash.
- **Limpieza de Tabla & Eliminación de Flechas Desalineadas:**
  - Erradicadas las flechas desfasadas tipo `Editar &rarr;`.
  - Botón de edición minimalista con icono SVG de lápiz integrado en pastilla oscura sobria (`bg-zinc-900 border border-zinc-800 text-zinc-300 hover:text-white`).
- **Drawers de Edición y Creación con UX/UI Profesional:**
  - Prohibidos los hacks de márgenes negativos (`margin: 0 -1.75rem -1.75rem`).
  - Encabezados fijos limpios con botón de cierre `✕` perfectamente alineado.
  - Formularios con espaciado natural (`space-y-4`), inputs en `#09090b` / `bg-zinc-900`, bordes `#27272a` y foco en dorado `#F5B81C`.
  - Footers fijos con botón de eliminación permanente a la izquierda y acciones de confirmación/cancelación a la derecha.

---

### 16. Rediseño Maestro de Pagos al Personal & Liquidación Asincrónica
- **Eliminación Total de Banners Invasivos Verdes:**
  - Erradicado el banner estático `"Planilla de pagos actualizada."` que bloqueaba visualmente la parte superior de la pantalla.
  - Sustituido por un sistema de retroalimentación discreto estilo iOS (`#discreet-toast`): pastilla flotante oscura en la esquina inferior derecha con indicador dorado ámbar, auto-dismiss a los 2.2 segundos y desvanecimiento suave.
- **Operaciones Asincrónicas sin Recarga de Página (Cero Parpadeo):**
  - **Eliminación Instantánea ("Quitar"):** El botón de quitar personal ya no realiza submit tradicional de formulario ni recarga la página completa. Utiliza llamadas `fetch(DELETE, { headers: { 'Accept': 'application/json' } })` para desvanecer y deslizar la fila (`opacity: 0, transform: translateX(12px)`) en 220ms.
  - **Recálculo Inmediato en el DOM:** Al remover a un trabajador o al modificar montos/checkboxes (`pay-amount-input`, `is-paid-checkbox`), los totales (`Total Planilla`, `Total Liquidado`, `Pendiente por Pagar`), los contadores y el pie de tabla se recalculan al instante sin esperar al servidor.
  - **Guardado Silencioso con Estado:** El botón "Guardar Cambios de Pagos" envía los datos por AJAX (`data-ajax="true"`), muestra spinner sutil de guardado y cambia a `"Guardado ✓"` sin refrescar la ventana.
- **Exposición Completa de Datos y Contactos:**
  - Se visualiza el celular del trabajador directamente en la fila (`Cel. XXXXXXXX`) o su ausencia (`— Sin celular —`).
  - Desglose por cargo (*Meseros & Limpieza*, *Bartenders*, *Seguridad*) con tarjetas compactas en `#09090b` y bordes finos `#27272a`.
  - Drawer de edición masiva de jornales por área (`#wage-batch-drawer`) sin hacks de márgenes negativos y con foco directo en teclado.

---

### 17. Rediseño Maestro de Historial de Pagos y Deudas del Personal
- **Eliminación Total de Estilos "Glassmorphism" Obsoletos y Relleno de IA:**
  - Erradicadas las clases residuales `glass-panel`, `glass-card`, degradados innecesarios y etiquetas artificiales como *"Módulo 3 — Auditoría & Planilla"*.
  - Implementación de estética **iOS Pure Dark**: fondo `#000000`, tarjetas en `#09090b` con bordes finos `#27272a` (`border-zinc-800/80`) y acento oficial dorado `#F5B81C`.
- **Liquidación Individual Asincrónica (Zero Parpadeo):**
  - El botón *"Pagar"* en filas con deuda pendiente ya no produce recarga de página.
  - Ejecuta una petición asincrónica `fetch(POST, { headers: { 'Accept': 'application/json' } })` que conmuta instantáneamente:
    - Estado a `Pagado esa noche` con insignia esmeralda sutil.
    - Saldo a `Deuda: Bs. 0.00`.
    - Acción a guion discreto `—`.
    - Recálculo en tiempo real de los KPIs de deuda pendiente, total pagado y contadores sin parpadeo.
  - Despliega un toast flotante discreto (`#discreet-toast`) confirmando la liquidación.
- **Drawer de Auditoría Histórica Global (`#nights-history-drawer`):**
  - Panel deslizante iOS moderno para navegar entre jornadas nocturnas con deudas pendientes.
  - Tarjetas de noche compactas con micro-desglose (*Planilla*, *Pagado*, *Sin Cobrar*) y selección directa sin fricción.

---

### 18. Rediseño Maestro de Historial de Noches y Apertura Operativa
- **Eliminación Total de Degradados y Emojis de IA:**
  - Erradicados emojis (como `⚠️`) y degradados en botones (`bg-gradient-to-r`).
  - Prohibidos términos coloquiales como *"noches de fiesta registradas"*; sustituidos por terminología sobria: *"Historial de Noches de Atención"*.
- **Cuadro de Mando Ejecutivo (KPIs):**
  - Indicadores clave en la parte superior: *Total Noches*, *Noche en Curso* (con estado activo en verde esmeralda si está abierta), *Noches Cerradas* y *Comisión POS Estándar*.
- **Tabla Minimalista iOS Pure Dark:**
  - Filas limpias con indicador circular de estado, fecha en `font-mono`, badge `"En curso"` en dorado `#F5B81C` para la noche activa y botón sobrio de acceso al dashboard (*"Operar"*).
- **Apertura de Noche Ágil:**
  - Formulario en contenedor sólido `#09090b` con detección de día en tiempo real, inputs de fecha optimizados para dark mode (`[color-scheme:dark]`) y advertencia elegante sin emojis en caso de noche anterior pendiente de cierre.

---

### 19. Tarjetas de Bebidas con Imagen de Fondo & Panel Jaspeado (Bodega Central)
- **Imagen de Fondo sin Bordes (Edge-to-Edge Upper Hero):**
  - La imagen oficial de cada bebida (`public/images/drinks/`) cubre el fondo de la tarjeta sin bordes internos separadores (`h-36 overflow-hidden flex items-center justify-center`).
  - Capa de desenfoque ambiental sutil (`object-cover blur-sm opacity-25 scale-110`) combinada con viñeta en degradado cinematográfico oscuro (`bg-gradient-to-t from-[#09090b] via-black/40 to-black/75`).
  - Silueta nítida y fiel de la botella centrada con sombra realista (`drop-shadow-[0_10px_16px_rgba(0,0,0,0.85)]`), conservando proporciones compactas sin alargar excesivamente la tarjeta.
  - Insignias flotantes estilo pastilla de cristal (`bg-black/70 backdrop-blur-md border border-white/10`) para subcategoría y precio de venta en Bs.
  - Botón flotante rápido para cambiar la foto de la bebida con respuesta táctil inmediata.
- **Panel Inferior Jaspeado (Frosted Glass / Textura con Controles de Stock):**
  - Panel translúcido de acabado texturado/jaspeado (`bg-zinc-950/85 backdrop-blur-md border-t border-zinc-800/80 p-3 rounded-b-2xl`).
  - Nombre del producto en tipografía limpia y presentación técnica (`unid/caja`).
  - Selector directo de stock en botellas totales con selector central monospace y cálculo inmediato en cajas y botellas sueltas.
  - Botones ágiles de ajuste por pasos (`-10`, `-1`, `+1`, `+10`) con retroalimentación cromática (esmeralda al sumar, rosa al restar).
  - Acceso directo a edición y cambio de imagen.
- **Gestor & Selector de Fotos en Drawers Lateral:**
  - Compatibilidad total con subida de nuevos archivos (`enctype="multipart/form-data"` hacia `public/images/drinks/`).
  - Vista previa en tiempo real con `FileReader` antes del guardado.
  - Malla interactiva del catálogo preexistente con miniaturas cuadradas y resaltado activo en dorado oficial `#F5B81C`.

---

### 20. Rediseño Maestro de Facturas & Tarjetas (Píldoras Switch iOS & Autoincremental)
- **Eliminación Definitiva de Banners Verdes:**
  - Sustitución de alertas estáticas que empujaban el contenido por un toast flotante ultradiscreto en la esquina inferior derecha (`#flash-success-toast` con pastilla oscura, punto dorado ámbar `#F5B81C` y auto-dismiss a los 2.4s).
  - Eliminación asincrónica vía `fetch(DELETE)` para borrar filas en vivo con transición suave (`opacity: 0, transform: translateX(14px)`) sin recarga de página.
- **Número de Factura 100% Autoincremental y Automático:**
  - Erradicado el campo de texto editable innecesario.
  - El sistema calcula y muestra una insignia compacta `#N` en la cabecera del formulario y asigna el correlativo siguiente automáticamente tanto en backend como frontend.
- **Selectores en Formato Píldoras Switch (Segmented Control iPhone):**
  - **Forma de Pago:** Píldoras interactivas `Tarjeta` y `Efectivo` con animación de estado activo en dorado ámbar `#F5B81C` con texto negro.
  - **Barra:** Renombrado de *"Barra / Punto de Venta"* a simplemente **"Barra"**, con 3 píldoras conmutables: `Principal`, `Subterráneo` y `Tienda`.
- **Monto Cobrado Compacto y Prominente:**
  - Sustitución del input largo que cruzaba la pantalla por un bloque monetario compacto (`w-36 font-mono font-black text-xl`) con prefijo integrado `Bs.` y foco dorado.
- **Notas y Vouchers Desplegables Bajo Demanda:**
  - Etiqueta *"Notas (opcional)"* con botón discreto `+ Agregar` que expande el campo únicamente si Don Ludo desea ingresar un número de voucher o detalle, manteniendo el formulario limpio por defecto.

---

### 21. Rediseño Maestro de Cobros por QR (Yasta / Yape)
- **Switches Modo iPhone (Segmented Controls) para Formularios de Pocas Opciones:**
  - **Punto de Venta:** Píldoras compactas (`Principal`, `Tienda`, `Subte`) en contenedor oscuro `bg-zinc-950 border border-zinc-800` con selección rápida e intuitiva.
  - **Aplicación Bancaria:** Píldoras de selección directa con los **logos oficiales de Yasta y Yape** (`images/LogosQR/yasta.png` y `images/LogosQR/yape.png`) integrados en el switch para un reconocimiento visual instantáneo.
- **Normalización Estricta de Roles (Eliminación de "MOZO"):**
  - Todo el personal de atención al cliente está unificado formalmente bajo la denominación **MESERO**.
  - Erradicado cualquier texto `(MOZO)` de las opciones y agrupadores, actualizando la base de datos y sanitizando la presentación en la vista.
- **Selector de Cobrante Personalizado iOS Pure Dark (Erradicación del Select Nativo):**
  - Eliminado el `<select>` nativo del navegador con su menú tosco y los textos con guiones (`-- SELECCIONAR PERSONAL --`).
  - Sustituido por un **Popover Flotante iOS Pure Dark** (`#cobrante-menu`):
    - Trigger con etiqueta sobria **"Cobrante"**, dot indicador dorado y flecha chevron animada.
    - Buscador integrado en tiempo real (`#cobrante-search`) para encontrar a cualquier mesero o bartender en 1 tecla.
    - Avatares con iniciales, agrupadores sobrios en dorado (`BARTENDERS`, `MESEROS`, `REFUERZOS`) y checkmarks activos.
    - Al seleccionar, el foco se transfiere automáticamente al campo de Monto para máxima agilidad operativa.
- **Monto Cobrado Compacto y Directo:**
  - Campo numérico monoespaciado en blanco nítido con prefijo dorado `Bs.` integrado y botón directo `Cobrar`.
- **Eliminación Asincrónica sin Parpadeo (Zero Reload):**
  - Las anulaciones o eliminaciones de cobros QR en las 3 columnas de auditoría (*Barra Kelly*, *Tienda*, *Barra Ariel*) se procesan de forma asincrónica con desvanecimiento suave de la fila.

---

### 22. Rediseño Maestro de Cierre de Caja (Arqueo Ejecutivo Compacto & Cero Scroll)
- **Erradicación de Relleno de "IA Generativa" y Textos Redundantes:**
  - Eliminados todos los títulos artificiales, subtítulos explicativos y oraciones pretenciosas (*"Arqueo final de la noche: entradas, salidas..."*, *"1. Todo el Dinero que Entró"*, *"4 Fuentes"*, *"Egresos del Turno"*, *"Salidas reales efectuadas"*, *"Avance de liquidación..."*).
  - La interfaz va directo a las cifras operativas esenciales que Don Ludo necesita consultar en 1 segundo.
- **Distribución de Pantalla Única (Zero Scroll Vertical):**
  - **Cuadrícula en 2 Columnas Horizontales (`lg:col-span-7` vs `lg:col-span-5`):** Toda la información cabe en el viewport estándar (~550px de altura total) sin obligar al usuario a hacer scroll infinito.
  - **Columna Izquierda (Matriz de Arqueo):**
    - Indicador central prominente de **Plata en Caja** (`text-2xl sm:text-3xl font-mono font-black text-white` con `Bs.` dorado `#F5B81C`) y micro-balance de Entradas vs Salidas.
    - Desglose paralelo de Entradas (*Tarjetas Neto*, *QR Yasta*, *QR Yape*, *Efectivo en Mano*) y Salidas (*Pago Personal*, *Gastos de Turno*) con filas compactas de 28px de altura y tipografía de alto contraste.
  - **Columna Derecha (Personal & Gastos):**
    - **Personal:** Ficha ultra compacta con 3 métricas (*Pagado*, *Planilla*, *Por Pagar*) y botón rápido a Drawer lateral.
    - **Gastos:** Formulario en una sola línea horizontal (switch *Interno*/*Externo*, descripción, monto y botón `+`) junto a una mini-tabla con scroll interno suave (`max-h-[130px]`) que no desplaza la pantalla general.
- **Modal Deslizante (Slide-Over Drawer `#staff-drawer`):**
  - Mantiene la nómina de trabajadores fuera del lienzo principal, desplegándose suavemente desde la derecha únicamente bajo demanda (`Ver personal de turno →`).
  - Buscador en vivo, avatares, cargos estandarizados a `MESERO` (cero `MOZO`), montos monoespaciados y estado de pago en píldora (`Pagado` / `Pendiente`).
- **Eliminación Asincrónica de Gastos Operativos:**
  - Anulación de gastos en tiempo real mediante `fetch(DELETE)` con animación suave sin recarga de pantalla.

---

### 23. Rediseño Maestro de Pagos al Personal (Planilla Nocturna Limpia de IA)
- **Erradicación de Relleno Generativo de IA:**
  - Erradicadas las frases artificiales y sobre-explicativas (*"Planilla & Jornales"*, *"Liquidación Nocturna"*, *"Registro de jornales, control de pagos en efectivo..."*, *"Auditoría & Planilla"*, *"Control de Deudas..."*).
  - Título limpio y directo: **Pagos al Personal**.
- **Supresión de Tarifas Promedio Falsas:**
  - Eliminados los textos estáticos e inventados como *"Tarifa promedio: Bs. 100 - 110"*. El desglose por área muestra datos 100% reales de la base de datos (conteo de personas y monto total).
- **Tipografía y Legibilidad de Alto Contraste para Don Ludo:**
  - Nombre del trabajador en `text-sm sm:text-base font-bold text-white uppercase`.
  - Roles normalizados y limpios (`MESERO`, sin `MOZO`).
  - Inputs de monto monetario en `text-base font-mono font-black text-white` con foco dorado `#F5B81C`.
  - Checkboxes cómodos (`w-5 h-5 accent-[#F5B81C]`).
- **Drawer de Edición por Área Depurado:**
  - Eliminados los subtítulos redundantes bajo cada cargo (*"Atención de meseros"*, *"Aseo y mantenimiento"*, *"Puerta y orden"*, *"Barra Kelly y Ariel"*), conservando etiquetas directas y claras.

---

### 24. Rediseño Maestro de Personal y Turnos (Tipografía Oficial Plus Jakarta Sans & Cero IA)
- **Erradicación de Relleno Generativo de IA:**
  - Eliminados textos superfluos como *"Cuadrilla Nocturna"*, *"Tío Chu Club"* y *"Nómina de trabajadores, meseros, barra y seguridad con jornales por turno"*.
  - Cabecera limpia con ícono dorado oficial `#F5B81C` y título directo: **Personal y Turnos**.
- **Normalización de Tipografía Oficial (`font-sans` Plus Jakarta Sans):**
  - Corregido el uso excesivo de `font-mono` que daba apariencia de terminal de código a las pestañas y tablas.
  - Pestañas por día (*Viernes*, *Sábado*, *Domingo*, *Todos*), encabezados de tabla, pastillas de días de trabajo y estados ahora usan la tipografía oficial sans-serif limpia.
  - `font-mono` queda reservado estrictamente para importes monetarios (*Bs. 100.00*) y campos numéricos.

---

### 25. Rediseño Maestro de Historial de Pagos y Deudas (Tarjetas Compactas & Cero Gimmicks de IA)
- **Malla de Tarjetas Compactas (Compact Cards Grid):**
  - Vista demostrativa y de auditoría gerencial con disposición compacta de alta densidad (`grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3`).
  - Tarjetas estilizadas en Pure Dark iOS (`#09090b`, borde `border-zinc-800/80`, padding reducido `p-3.5`).
- **Erradicación de Efectos Pulsantes de IA (`animate-pulse`) y Colores Chillones:**
  - Eliminados todos los puntos parpadeantes y semáforos verdes/rojos saturados estilo plantilla de IA.
  - Paleta sobria con pastillas neutras (`bg-zinc-950 border border-zinc-800 text-zinc-300`), indicadores estáticos y acentos dorados oficiales `#F5B81C`.
- **Filtro Rápido Segmentado en Tiempo Real:**
  - Píldoras conmutables (*Todos*, *Con Deuda*, *Al Día*) con buscador instantáneo sin recargar la página.
- **Acción Rápida de Liquidación Asincrónica:**
  - Botón dorado `#F5B81C` `Pagar Jornal` en las tarjetas con saldo pendiente, con actualización en vivo del estado y los KPIs de deuda sin parpadeos.

---

### 26. Unificación Tipográfica Absoluta: Plus Jakarta Sans (Cero Fuentes Mixtas)
- **Fuente Única Oficial del Sistema:**
  - Se eliminó `JetBrains Mono` y cualquier otra tipografía que fragmentaba la identidad visual del sistema.
  - La fuente de **Resumen General** (`Plus Jakarta Sans`) es ahora la **única tipografía** en todo el software: sidebar, modales, drawers, tarjetas, tablas, formularios e inputs.
- **Normalización de `--font-mono`:**
  - Se mapeó `--font-mono` a `Plus Jakarta Sans` con activación de números tabulares (`font-feature-settings: "tnum" 1, "lnum" 1;`).
  - Esto garantiza que los importes monetarios y columnas numéricas mantengan su perfecta alineación contable sin recurrir a una fuente monoespaciada distinta que rompa la armonía tipográfica.

---

### 27. Módulo de Administración de Usuarios & Caducidad de Sesión por Puerto
- **Control Centralizado de Cuentas de Acceso:**
  - Enlace directo en el sidebar en la sección de **Gestión**: `Usuarios`.
  - Don Ludo puede crear nuevas cuentas autorizadas (Nombre, Correo, Contraseña) mediante un slide-over drawer minimalista (`drawer-panel`).
  - Las nuevas cuentas quedan inmediatamente habilitadas para iniciar sesión en la pantalla de bienvenida.
  - Edición de credenciales y protección estricta contra eliminación accidental de la cuenta activa o de Don Ludo.
- **Caducidad Estricta de Sesión (`SESSION_EXPIRE_ON_CLOSE`):**
  - Se configuró la expiración de sesión inmediata al cerrar la pestaña/navegador o reiniciar el puerto/servidor (`expire_on_close = true`).
  - Se eliminó la persistencia de cookies *"remember me"* para garantizar que al abrir el puerto `8000` el sistema obligue a pasar por la pantalla de login.

---

### 28. Coreografía de Entrada Post-Login (Apple / iOS Spring Motion)
- **Activación Exclusiva Post-Login (Zero Repetición en Navegación):**
  - Controlado por flash de sesión de Laravel (`session()->flash('animate_entrance', true)`) generado al autenticarse en `AuthController::login()`.
  - Se reproduce **única y exclusivamente** la primera vez que Don Ludo inicia sesión y aterriza en el Dashboard. Al navegar entre módulos o recargar la página, la sesión flash se consume y la interfaz carga de forma instantánea sin animaciones repetitivas.
- **Secuencia y Coreografía Escalonada (Staggered Motion):**
  - **Sidebar:** Se despliega fluidamente desde el borde izquierdo (`translateX(-100%)` a `translateX(0)`) con curva de aceleración/desaceleración Apple (`cubic-bezier(0.16, 1, 0.3, 1)` durante 0.75s).
  - **Cabecera de Noche:** Se eleva suavemente desde abajo (`translateY(28px)` a `0`) tras un retraso sutil de 0.08s.
  - **Tarjetas KPI Escalonadas:** Las 4 tarjetas de métricas (*Venta en Barras*, *Tarjetas POS*, *Cobros QR*, *Efectivo en Caja*) emergen desde abajo con un desfase progresivo (0.16s, 0.24s, 0.32s, 0.40s) creando un efecto de armado visual de alta gama.
  - **Cuadro Operativo Inferior:** La distribución de ventas por barra y el balance de cuentas se ensamblan al final (0.48s y 0.54s) completando la experiencia visual en menos de 1 segundo.

---

### 29. Buscador en Tiempo Real por Nombre en Personal, Turnos y Planillas
- **Filtrado Instantáneo Zero-Reload (Client-Side):**
  - En la vista principal **Personal y Turnos** (`staff.index`), se integró un buscador minimalista con icono SVG y botón de limpieza rápida `&times;`.
  - Filtra instantáneamente por **nombre del trabajador**, **cargo/rol** y **área asignada** a medida que se teclea (`oninput`).
  - Actualiza en vivo el contador de integrantes visibles (`Mostrando N integrantes`) y muestra un estado vacío elegante si no hay coincidencias.
- **Buscador en Planilla de Turno (`staffPayments.index`):**
  - En la cabecera de la tabla de pagos por turno se incorporó un buscador homólogo que permite localizar rápidamente a cualquier mesero, bartender o seguridad en planillas extensas.
  - Compatible con atajo de teclado `Escape` para limpiar el filtro al instante.

---

### 30. Separación Visual Estricta: Identidad del Personal vs Cifras Financieras (Historial de Pagos)
- **Eliminación de Ambigüedad entre Persona y Montos:**
  - En [`resources/views/payment_history/index.blade.php`](file:///c:/Users/Usuario/Desktop/Tio%20Chu/resources/views/payment_history/index.blade.php), se dividió cada tarjeta de trabajador en 3 compartimentos jerárquicos independientes:
    1. **Cabecera de Identidad:** Avatar con inicial, nombre del trabajador en mayúsculas grandes, cargo formal (`MESERO`, `BARTENDER`, `SEGURIDAD`), teléfono y píldora de estado cromática (`Por Pagar` en dorado `#F5B81C` vs `Pagado esa noche` en verde esmeralda).
    2. **Faja Financiera Bicolor:** Cuadrícula de 2 columnas con etiquetas explícitas que diferencian sin lugar a duda el **Jornal Fijo Asignado** (monto neutro de referencia) de la **Deuda Pendiente / Se le debe:** (destacada en dorado prominente).
    3. **Botón de Acción Directo:** Botón ancho y claro *"Pagar Jornal (Bs. X.XX)"* con icono y monto explícito para evitar cualquier error de cobro por parte de Don Ludo.
- **Diferenciación Semántica en Tarjetas KPI Superiores:**
  - **Deuda de la Noche:** Resaltada con borde dorado `#F5B81C`, número grande en oro y etiqueta de alerta.
  - **Pagado esa Noche:** Resaltada con número grande en verde esmeralda (`text-emerald-400`) para certificar dinero ya desembolsado.
  - **Planilla de Turno:** Fondo neutro con total global asignado.

---

### 31. Estabilización y Congelamiento de Dimensiones en Personal y Turnos
- **Bloqueo de Columnas con `table-fixed` & `<colgroup>`:**
  - En [`resources/views/staff/index.blade.php`](file:///c:/Users/Usuario/Desktop/Tio%20Chu/resources/views/staff/index.blade.php), se configuró `table-layout: fixed` con porcentajes inmutables por columna:
    `N°: 5%`, `Trabajador: 27%`, `Contacto: 18%`, `Cargo: 17%`, `Área: 17%`, `Jornal: 10%`, `Acción: 6%`.
  - Impide que el ancho de la tabla o de las columnas cambie o se mueva al alternar entre *Viernes*, *Sábado*, *Domingo* y *Todos*, independientemente de la longitud de los nombres o la cantidad de filas.
- **Dimensiones Uniformes en Pestañas y Contenedor:**
  - Se estableció un ancho mínimo fijo en las 4 pestañas de días (`min-w-[110px] text-center`) y una altura mínima de referencia en el contenedor de tabla (`min-h-[540px]`), garantizando que la estructura visual permanezca firme y estática en todo momento.






























