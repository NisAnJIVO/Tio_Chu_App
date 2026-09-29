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
| **Texto Principal** | `#FFFFFF` (`text-white`) | Títulos, etiquetas clave y valores principales. |
| **Texto Secundario** | `#A1A1AA` (`text-zinc-400`) / `#71717A` (`text-zinc-500`) | Placeholders, descripciones breves, metadatos. |
| **Texto en Botones Dorados** | `#000000` (`text-black font-semibold`) | Contraste óptimo y legibilidad perfecta. |

---

## 3. Notificaciones y Feedback al Usuario (UX)

- **Cero bloques invasivos:** No colocar banners gigantes de color verde o rojo estáticos que empujen los formularios o rompan el diseño.
- **Toasts Flotantes Sutiles (Micro-Feedback):**
  - Mensajes de confirmación (ej. *"Sesión cerrada correctamente"*) aparecen flotando abajo en el centro (`fixed bottom-6 left-1/2 -translate-x-1/2`).
  - Fondo negro/zinc oscuro translúcido (`bg-zinc-950/90 border border-white/10`), texto discreto (`text-zinc-400 text-xs`).
  - **Duración efímera:** Se muestran por 1.5 a 2 segundos y se desvanecen automáticamente con fade-out suave.
- **Errores de validación:** Mensajes tipográficos concisos debajo del campo o centrados discretamente (`text-xs text-rose-400`), sin alertas escandalosas.

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
  - Al presionar *Iniciar sesión*, el botón cambia a estado de carga discreto y los paneles se contraen suavemente antes de dar paso al Dashboard.
