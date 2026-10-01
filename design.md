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

## 8. Arquitectura del Dashboard — Híbrido Real TransGlobal + Dilemo + iOS

- **Política Estricta de Integridad:** 
  - **Cero Datos Falsos o Simulados:** Prohibido el uso de mapas de calor artificiales o métricas inventadas. Todo número, barra y estado proviene 100% de la base de datos real.
  - **Cero Píldoras de Confetti IA:** Eliminadas las micro-píldoras aleatorias de colores (azul, verde, púrpura); la paleta se rige por el dorado oficial `#F5B81C`, zinc puro, verde para superávit y rojo para salidas.
- **Modo Oscuro Oficial y Permanente:**
  - Sin botones ni selectores de tema innecesarios; estética nocturna elegante y directa que no distrae al usuario.
- **Lenguaje Claro y Natural para Personas Mayores (Don Ludo):**
  - **Cero Jerga Contable o IA:** Prohibidas palabras técnicas abstractas como *"Superávit"*, *"Déficit"*, *"POS Neto"*, *"POS Bruto"*, *"Total Liquidado"*, *"Balance en Bóveda"*.
  - **Vocabulario Cotidiano y Directo:**
    - `Total Vendido en Barras` (con detalle: Barra Principal y Subte).
    - `Cobrado con Tarjeta` (con aclaración: Pasado por máquina: Bs. X).
    - `Cobrado por QR` (con bancos: Yasta y Yape).
    - `Dinero en Caja` (Plata a Favor / Faltante en Caja).
    - `Venta en Cada Barra` (Barra Kelly en Piso Principal, Barra Ariel en Subterráneo, Tienda en Entrada/Guardarropa, Suma Total de las Barras).
    - `Cuentas Claras: Entradas y Salidas` (dividido en *Todo el Dinero que Entró* vs *Todo lo que se Pagó*, y *Plata que Debe Quedar en Caja*).
- **Tarjetas 100% Estáticas:**
  - Las tarjetas no tienen saltos ni traslaciones `hover`. Solo los botones de acción reaccionan al cursor (`.dilemo-btn`).
- **Distribución Estructural Compacta (Cero Scroll en Pantalla):**
  - Todo el contenido del Resumen General encaja limpiamente en la altura de una pantalla normal de trabajo sin forzar scroll vertical.

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



















