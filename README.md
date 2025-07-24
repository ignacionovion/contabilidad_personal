# Sistema de Contabilidad Personal

Aplicación web robusta y moderna desarrollada con **Laravel 12** para una gestión financiera personal completa e intuitiva. Permite a los usuarios registrar y clasificar sus ingresos y gastos, gestionar deudas de tarjetas de crédito y visualizar su salud financiera a través de un dashboard interactivo.

## Características Principales

-   **Autenticación Segura:** Sistema de registro e inicio de sesión basado en Laravel.

-   **Dashboard Interactivo:**
    -   Vista principal con resúmenes claros del **total de ingresos, gastos y el balance actual**.
    -   **Gráfico de Tendencias:** Un gráfico de líneas muestra la evolución de los gastos totales durante los últimos 12 meses, permitiendo identificar patrones y estacionalidades.
    -   Resumen detallado del estado de todas las tarjetas de crédito.

-   **Módulos de Gestión Financiera:**
    -   **Categorías:** CRUD completo para personalizar las categorías de ingresos y gastos.
    -   **Ingresos y Gastos Generales:** Registro y gestión de todos los movimientos financieros que no pertenecen a tarjetas de crédito.

-   **Módulo Avanzado de Tarjetas de Crédito:**
    -   **Gestión de Múltiples Tarjetas:** Añade, edita y elimina todas tus tarjetas de crédito.
    -   **Registro de Compras en Cuotas:** Registra compras y especifica el número de cuotas. El sistema calcula y genera automáticamente cada pago mensual.
    -   **Gestión Detallada de Cuotas:**
        -   **Modal Interactivo:** Visualiza el detalle completo de cuotas (pagadas y pendientes) para cada compra con un solo clic.
        -   **Actualización de Estado en Tiempo Real:** Cambia el estado de cada cuota ('Pagada', 'Pendiente') directamente desde el modal. Los cambios se guardan al instante sin necesidad de recargar la página.
        -   **Seguimiento de Progreso:** La vista principal muestra un contador de progreso (ej: `3/6 cuotas pagadas`) para cada compra, ofreciendo una visión clara de las deudas pendientes.
    -   **Resumen en Dashboard:** El panel principal consolida la deuda del mes, la deuda total y el cupo disponible para cada tarjeta.

-   **Experiencia de Usuario (UX) Optimizada:**
    -   **Navegación Simplificada:** El menú lateral ha sido rediseñado para ofrecer acceso directo a todas las secciones, eliminando submenús y mejorando la usabilidad.
    -   **Formato de Moneda Automático:** Los campos de monto formatean los números a formato de moneda (CLP) mientras se escribe.
    -   **Interfaz en Español:** Toda la aplicación está localizada para hispanohablantes.

## Stack Tecnológico

-   **Backend:** PHP 8.x, Laravel 12.x
-   **Frontend:** AdminLTE 3, Bootstrap, JavaScript, Chart.js
-   **Base de Datos:** PostgreSQL
-   **Gestión de Dependencias:** Composer (PHP), NPM (JavaScript)

## Requisitos Previos

Asegúrate de tener instalado lo siguiente en tu sistema:
-   PHP >= 8.2
-   Composer
-   Node.js y NPM
-   PostgreSQL

## Instalación y Configuración

Sigue estos pasos para configurar el proyecto en tu entorno local:

1.  **Clonar el repositorio:**
    ```bash
    git clone https://github.com/ignacionovion/contabilidad_personal.git
    cd contabilidad_personal
    ```

2.  **Instalar dependencias de PHP:**
    ```bash
    composer install
    ```

3.  **Instalar dependencias de JavaScript:**
    ```bash
    npm install
    ```

4.  **Compilar los assets de frontend:**
    ```bash
    npm run dev
    ```

5.  **Configurar el archivo de entorno:**
    Copia el archivo de ejemplo `.env.example` y renómbralo a `.env`.
    ```bash
    cp .env.example .env
    ```

6.  **Generar la clave de la aplicación:**
    ```bash
    php artisan key:generate
    ```

7.  **Configurar la base de datos en el archivo `.env`:**
    Abre el archivo `.env` y ajusta las siguientes variables con tus credenciales de PostgreSQL:
    ```ini
    DB_CONNECTION=pgsql
    DB_HOST=127.0.0.1
    DB_PORT=5432
    DB_DATABASE=nombre_de_tu_bd
    DB_USERNAME=tu_usuario_de_bd
    DB_PASSWORD=tu_contraseña_de_bd
    ```
    Asegúrate de que la base de datos (`nombre_de_tu_bd`) exista en PostgreSQL.

8.  **Ejecutar las migraciones:**
    Este comando creará todas las tablas necesarias en la base de datos.
    ```bash
    php artisan migrate
    ```

## Ejecutar la Aplicación

Para iniciar el servidor de desarrollo de Laravel, ejecuta el siguiente comando:
```bash
php artisan serve
```
La aplicación estará disponible en `http://127.0.0.1:8000`.

## Uso

1.  **Registro y Login:** Crea una cuenta en `/register` e inicia sesión en `/login`.
2.  **Navegación:** Utiliza el menú lateral de acceso rápido para moverte entre el Dashboard y los módulos de gestión.
3.  **Gestión de Tarjetas y Cuotas:**
    -   En "Tarjetas de Crédito", añade tus tarjetas.
    -   Haz clic en "Ver Gastos" para añadir compras y definir las cuotas.
    -   En la lista de gastos, haz clic en "Ver Detalle" para abrir el modal y gestionar el estado de cada cuota individualmente.
