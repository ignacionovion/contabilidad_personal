# Sistema de Contabilidad Personal v2.0

Aplicación web robusta y moderna desarrollada con **Laravel 12** para una gestión financiera personal completa e intuitiva. Permite a los usuarios registrar y clasificar sus ingresos y gastos, gestionar deudas de tarjetas de crédito, controlar gastos recurrentes y visualizar su salud financiera a través de un dashboard interactivo.

---

## ✨ Características Principales

### 📊 Dashboard Avanzado
El panel de control ofrece una visión 360° de tus finanzas de un solo vistazo:
-   **Resúmenes Mensuales:** Tarjetas informativas con el **total de ingresos, gastos y el balance del mes en curso**.
-   **Gráfico de Balance Anual:** Un gráfico de líneas muestra la evolución del **balance (ingresos vs. gastos)** durante los últimos 12 meses, permitiendo identificar tendencias.
-   **Gráfico de Cuentas del Hogar:** Visualiza la evolución de los gastos recurrentes (luz, agua, internet, etc.) en un gráfico dedicado.
-   **Resumen de Tarjetas de Crédito:** Un resumen consolidado de la deuda del mes, deuda total y cupo disponible para cada tarjeta.

### 💸 Gestión de Ingresos
-   **Sueldo Fijo:** Registra tu sueldo mensual una sola vez y actívalo/desactívalo con un solo clic.
-   **Ingresos Variables:** Añade ingresos adicionales (proyectos, bonos, etc.). La vista principal los agrupa automáticamente por mes en un formato de **acordeón interactivo**, manteniendo la interfaz limpia y ordenada.

### 💳 Módulo Avanzado de Tarjetas de Crédito
-   **Gestión de Múltiples Tarjetas:** Añade, edita y elimina todas tus tarjetas de crédito.
-   **Registro de Compras en Cuotas:** Registra compras y especifica el número de cuotas. El sistema calcula y genera automáticamente cada pago mensual.
-   **Gestión Detallada de Cuotas:**
    -   **Modal Interactivo:** Visualiza el detalle completo de cuotas (pagadas y pendientes) para cada compra.
    -   **Actualización de Estado en Tiempo Real:** Cambia el estado de cada cuota ('Pagada', 'Pendiente') directamente desde el modal. Los cambios se guardan al instante.
    -   **Seguimiento de Progreso:** La vista principal muestra un contador de progreso (ej: `3/6 cuotas pagadas`) para cada compra.

### 🏠 Cuentas del Hogar (Gastos Recurrentes)
-   **Registro Simplificado:** Un módulo dedicado para registrar y gestionar gastos fijos como arriendo, servicios básicos, suscripciones, etc.
-   **Panel de Acceso Rápido:** Registra el pago mensual de tus cuentas directamente desde la vista de gastos generales, agilizando el proceso.

### ⚙️ Módulos de Soporte
-   **Gestión de Gastos Generales:** Registra todos los movimientos que no pertenecen a tarjetas ni a cuentas del hogar.
-   **Gestión de Categorías:** CRUD completo para personalizar las categorías de ingresos y gastos.

### 🚀 Experiencia de Usuario (UX) Optimizada
-   **Navegación Simplificada:** Menú lateral rediseñado para ofrecer acceso directo a todas las secciones.
-   **Formato de Moneda Automático:** Los campos de monto formatean los números a formato de moneda (CLP) mientras se escribe.
-   **Interfaz en Español:** Toda la aplicación está localizada para hispanohablantes.

---

## 🛠️ Stack Tecnológico

-   **Backend:** PHP 8.x, Laravel 12.x
-   **Frontend:** AdminLTE 3, Bootstrap, JavaScript, Chart.js
-   **Base de Datos:** PostgreSQL
-   **Gestión de Dependencias:** Composer (PHP), NPM (JavaScript)

---

## 📦 Instalación y Configuración

Sigue estos pasos para configurar el proyecto en tu entorno local:

1.  **Clonar el repositorio:**
    ```bash
    git clone https://github.com/ignacionovion/contabilidad_personal.git
    cd contabilidad_personal
    ```

2.  **Instalar dependencias:**
    ```bash
    composer install
    npm install
    ```

3.  **Configurar el entorno:**
    Copia el archivo de ejemplo `.env.example` y renómbralo a `.env`.
    ```bash
    cp .env.example .env
    ```

4.  **Generar la clave de la aplicación:**
    ```bash
    php artisan key:generate
    ```

5.  **Configurar la base de datos en `.env`:**
    Ajusta las variables `DB_*` con tus credenciales de PostgreSQL.
    ```ini
    DB_CONNECTION=pgsql
    DB_HOST=127.0.0.1
    DB_PORT=5432
    DB_DATABASE=contabilidad_personal
    DB_USERNAME=tu_usuario
    DB_PASSWORD=tu_contraseña
    ```
    Asegúrate de que la base de datos exista en PostgreSQL.

6.  **Ejecutar las migraciones:**
    Este comando creará toda la estructura de la base de datos.
    ```bash
    php artisan migrate
    ```

7.  **Compilar los assets:**
    ```bash
    npm run dev
    ```

---

## ▶️ Ejecutar la Aplicación

Para iniciar el servidor de desarrollo, ejecuta:
```bash
php artisan serve
```
La aplicación estará disponible en `http://127.0.0.1:8000`.

### Credenciales de ejemplo
-   **Usuario:** `test@example.com`
-   **Contraseña:** `password`

*(Puedes crear tu propia cuenta en la página de registro)*
