# 🧠 Gestor Inteligente de Compras (SaaS B2B)

Sistema profesional diseñado para automatizar y optimizar el proceso de compras empresariales, permitiendo una gestión fluida desde la solicitud hasta la comparativa de cotizaciones.

---

## 🚀 Funcionalidades Principales

- **Gestión Multi-tenant:** Cada empresa opera de forma aislada y segura.
- **Automatización de Solicitudes:** Creación de pedidos de compra y envío masivo de cotizaciones.
- **Comunicación Integrada:**
  - **Email:** Envío automático de solicitudes a proveedores.
  - **WhatsApp:** Vista previa en tiempo real y gestión de plantillas personalizadas.
- **Comparativa de Cotizaciones:** Panel para analizar y elegir al mejor proveedor basado en costo y tiempo.
- **Interfaz Premium:** Diseño moderno basado en el font **Outfit** con paleta de colores personalizada (Amber/Slate).

---

## 🛠️ Tecnologías Utilizadas

- **Backend:** [Laravel 10+](https://laravel.com/)
- **Panel Administrativo:** [Filament v3](https://filamentphp.com/)
- **Base de Datos:** MySQL
- **Frontend / Styling:**
  - [Tailwind CSS v4](https://tailwindcss.com/)
  - [Vite](https://vitejs.dev/)
  - Google Fonts (Outfit)
- **Plugins Destacados:**
  - **WhatsApp Preview:** `rarq/filament-whatsapp-message-preview`
  - **Email Templates:** `majezanu/filament-email-templates`
  - **Themes:** `hasnayeen/themes`

---

## 📦 Instalación y Configuración

1. **Clonar el repositorio:**
   ```bash
   git clone <repo-url>
   ```
2. **Instalar dependencias:**
   ```bash
   composer install
   npm install
   ```
3. **Configurar el entorno:**
   Copiar `.env.example` a `.env` y configurar la base de datos.
4. **Migraciones y Seeds:**
   ```bash
   php artisan migrate --seed
   ```
5. **Compilar assets:**
   ```bash
   npm run build
   ```

---

## ✒️ Créditos
Desarrollado con ❤️ para la optimización de procesos B2B.
Power by **Marcelo Ifran Singh**.

