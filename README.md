# 🚀 Proyecto de Gestión de Proyectos (Vue 3 + Pinia + Laravel API)

Este proyecto es una aplicación de gestión de proyectos con tareas, desarrollada con **Vue 3**, **Pinia** y una **API en Laravel**.  
Permite crear, editar, eliminar y visualizar proyectos, así como gestionar tareas dentro de cada uno por medio de un kanban.

---

## 🧩 Tecnologías principales

### Frontend
- [Vue 3](https://vuejs.org/)
- [Pinia](https://pinia.vuejs.org/)
- [Vite](https://vitejs.dev/)
- [Tailwind CSS](https://tailwindcss.com/)

### Backend
- [Laravel 12](https://laravel.com/)
- [Sanctum](https://laravel.com/docs/sanctum) para autenticación de la API

---

## ⚙️ Requisitos previos

Asegúrate de tener instalado:
- [Node.js 18+](https://nodejs.org/)
- [PHP 8.2+](https://www.php.net/)
- [Composer](https://getcomposer.org/)
- [MySQL o MariaDB](https://www.mysql.com/)

---

## 🖥️ Instalación del Backend (Laravel)

1. Clona el repositorio:
   ```bash
   git clone https://github.com/luis-indriago/project-manager
   cd backend
   ```

2. Instala las dependencias:
   ```bash
   composer install
   ```

   ```bash
   npm install
   ```

3. Copia el archivo de entorno y configura la base de datos:
   ```bash
   cp .env.example .env
   ```

4. Genera la clave de la aplicación:
   ```bash
   php artisan key:generate
   ```

5. Ejecuta las migraciones y seeders:
   ```bash
   php artisan migrate --seed
   ```

6. Inicia el servidor de desarrollo de vite:
   ```bash
   npm run dev
   ```

7. Inicia el servidor de desarrollo de laravel:
   ```bash
   php artisan serve
   ```

   Por defecto, estará en: `http://127.0.0.1:8000`

---


## 🧠 Funcionalidades principales

- ✅ CRUD de proyectos  
- ✅ CRUD de tareas dentro de cada proyecto  
- ✅ Drag & drop para mover tareas entre estados (`pendiente`, `en proceso`, `completada`)  


---

## 👨‍💻 Desarrollado por:

**Luis Indriago**  
📧 luisindriago202@gmail.com